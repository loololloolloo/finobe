<?php

namespace App\Services\ItemImport;

use App\Http\Controllers\Asset as AssetHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
    AssetCopier
     - copies a Roblox catalog item onto the site from its link (or id)
     - name, description and type come from Roblox; hats/accessories and gear go through
       ItemImporter, faces / shirts / pants / t-shirts are rebuilt from their image
*/
class AssetCopier
{
    const HAT_TYPES = [8, 41, 42, 43, 44, 45, 46, 47]; // hat + the accessory types (hair, face, neck, ...)

    /** @return array{id: int, name: string} */
    public static function copy(string $link, int $author, int $price, bool $onSale, ?string $title = null, ?string $description = null): array
    {
        $id = self::idFromLink($link);
        $details = self::details($id);
        $name = trim((string) ($title ?: ($details['Name'] ?? "Item {$id}")));
        $desc = (string) ($description ?? ($details['Description'] ?? ''));
        $type = (int) ($details['AssetTypeId'] ?? 0);

        if (in_array($type, self::HAT_TYPES, true) || $type === 19) {
            $siteId = ItemImporter::import(ItemImporter::download($id), $name, $author, $desc, $price, $onSale,
                $type === 19 ? ItemImporter::GEAR : ItemImporter::HAT);
            return ['id' => $siteId, 'name' => $name];
        }

        $kind = match ($type) {
            18 => ['face', 'Texture'],
            11 => ['shirt', 'ShirtTemplate'],
            12 => ['pants', 'PantsTemplate'],
            2 => ['tshirt', 'Graphic'],
            default => throw new ImportException("That item's type ({$type}) can't be copied yet: hats, accessories, gear, faces, shirts, pants and t-shirts can."),
        };

        $xml = RbxmReader::toXml(ItemImporter::download($id));
        if (!preg_match('#<Content name="' . $kind[1] . '">\s*<url>([^<]+)</url>#', $xml, $m) || ($imageId = ItemImporter::robloxAssetId($m[1])) === null) {
            throw new ImportException("Couldn't find the image in that {$kind[0]}.");
        }

        $siteId = self::fromImage(ItemImporter::download($imageId), $kind[0], $name, $author, $desc, $price, $onSale);
        return ['id' => $siteId, 'name' => $name];
    }

    /** Face / shirt / pants / t-shirt from an image (also used for plain PNG uploads); approved straight away. */
    public static function fromImage(string $image, string $kind, string $name, int $author, string $description, int $price, bool $onSale): int
    {
        if (!str_starts_with($image, "\x89PNG") && !str_starts_with($image, "\xFF\xD8")) {
            throw new ImportException('The image has to be a PNG or JPEG.');
        }
        $tmp = tempnam(sys_get_temp_dir(), 'finobe_item_');
        file_put_contents($tmp, $image);
        try {
            $siteId = AssetHelper::createAccessory($name, ['tmp_name' => $tmp], $author, $description, $price, $onSale, false, $kind);
        } finally {
            @unlink($tmp);
        }

        // createAccessory queues user uploads for review; staff uploads are live right away
        $db = DB::connection('finobe');
        $additional = json_decode((string) $db->table('assets')->where('id', $siteId)->value('additional'), true) ?: [];
        $db->table('assets')->whereIn('id', array_filter([$siteId, (int) ($additional['media']['textureAssetId'] ?? 0)]))->update(['visibility' => 'n']);

        return $siteId;
    }

    /** roblox.com/catalog/123/Name, /library/123, create.roblox.com/store/asset/123, item.aspx?id=123 or just 123 */
    public static function idFromLink(string $link): int
    {
        $link = trim($link);
        if (preg_match('#^\d+$#', $link)
            || preg_match('#roblox\.com/(?:[a-z-]+/)?(?:catalog|library)/(\d+)#i', $link, $m)
            || preg_match('#create\.roblox\.com/(?:[a-z-]+/)?(?:store|marketplace)/asset/(\d+)#i', $link, $m)
            || preg_match('#[?&]id=(\d+)#i', $link, $m)) {
            return (int) ($m[1] ?? $link);
        }
        throw new ImportException("That doesn't look like a Roblox item link.");
    }

    private static function details(int $id): array
    {
        try {
            $r = Http::timeout(15)->retry(2, 500, throw: false)->get("https://economy.roproxy.com/v2/assets/{$id}/details");
        } catch (\Throwable $e) {
            throw new ImportException("Couldn't reach Roblox to look up item {$id}, try again.");
        }
        if (!$r->ok() || !is_array($r->json())) {
            throw new ImportException("Roblox has no item {$id}.");
        }
        return $r->json();
    }
}
