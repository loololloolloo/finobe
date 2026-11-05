<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/*
    SecurityNotary (Signing) Class
     - Ported from ROBLOX 2019 Web Assemblies (backend) leak.
*/
enum FormatVersion
{
    case V1; //Arseny: V1 used for 2009-2013M (%{signature}%{script})
    case V2; //Arseny: V2 used for 2013L-2018E (--rbxsig%{signature}%{script})
}
class SecurityNotary extends Controller
{
    public static function CreateSignature(string $message): string
    {
        $keyPath = storage_path("rbx/files/privateKey1024.pem");

        if (!is_readable($keyPath)) {
            throw new \RuntimeException("Script signing key missing: create {$keyPath} (see tools/rcc/README.md, 'Signing key')");
        }

        $key = file_get_contents($keyPath);
        $signature = "";
        openssl_sign($message, $signature, $key, OPENSSL_ALGO_SHA1);

        return base64_encode($signature);
    }

    public static function SignScript(string $script, FormatVersion $version = FormatVersion::V1, bool $newline = true): string
    {
        if ($newline)
        {
            $script = "\r\n".$script;
        }
        switch ($version) {
            case FormatVersion::V1:
                $signature = self::CreateSignature($script);
                return "%{$signature}%{$script}";
            case FormatVersion::V2:
                $signature = self::CreateSignature($script);
                return "--rbxsig%{$signature}%{$script}";
            default:
                return "";
        }
    }
}
