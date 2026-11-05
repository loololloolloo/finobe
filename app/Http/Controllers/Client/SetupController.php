<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    // what the launcher should download: FINOBE_CLIENT_VERSION in .env (e.g. version-fa01fdc40051450f)
    public function getCurrentVersion (Request $request): string {
        return (string) env('FINOBE_CLIENT_VERSION', 'version-xqs0ranlc9ofKJwiJtSd');
    }

    public function getCurrentBootstrapperVersion (Request $request): string {
        return '1, 6, 3, 172';
    }

    public function getCurrentActiveCDN (Request $request): string {
        return 'prod1-setup.' . env('FINOBE_DOMAIN', 'finobe.wtf');
    }

    // client builds live in two places: storage/setup (tracked launcher packages) and
    // storage/app/private/setup (builds uploaded from the admin "change versions" page)
    public function getFile (Request $request, string $file) {
        $file = basename($file);

        foreach ([Storage::path('setup/' . $file), storage_path('setup/' . $file)] as $filePath) {
            if (is_file($filePath)) {
                return response()->file($filePath);
            }
        }

        return response()->json([ 'code' => 0, 'message' => 'Not found.'], 404);
    }
}
