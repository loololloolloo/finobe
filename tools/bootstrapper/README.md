# Finobe launcher (bootstrapper)

`FinobeLauncher.exe`: a small native Windows program (Win32 + GDI+ + WinINet, no runtime needed).
White window, spinning Finobe logo, progress bar, Cancel, like Roblox's 2016 bootstrapper.

What it does:

1. Asks `https://prod1-setup.finobe.wtf/version` for the current client build.
2. If that build isn't installed: downloads `<version>-FinobePlayer.zip` ("Downloading Finobe..." on a
   fresh install, "Upgrading Finobe..." when an older build is there) and unpacks it ("Installing
   Finobe...") into `%LOCALAPPDATA%\Finobe\Versions\<version>`, then removes older builds. On a slow
   step it adds "This is taking a while, hang tight!".
3. Copies itself to `%LOCALAPPDATA%\Finobe\FinobeLauncher.exe` and registers `finobe-launcher://`
   (per user, no admin rights needed), so the site's Play buttons open it.
4. Started from a Play button, it runs
   `RobloxPlayerBeta.exe --play -a https://www.finobe.wtf/Login/Negotiate.ashx -t <token> -j <PlaceLauncher URL>`.
   Started by itself, it says "Finobe is installed!" and closes.

## Publishing a client build

On the web server, with the patched client folder (the `RobloxPlayer` folder of the `client2016` branch):

```bash
python3 tools/client/package_client.py /path/to/RobloxPlayer
```

That writes `storage/app/private/setup/version-<hash>-FinobePlayer.zip` and prints
`FINOBE_CLIENT_VERSION=version-<hash>`. Put that line in `.env`, run `php artisan config:clear`, and every
launcher upgrades on its next start.

The site links `/download` to `https://prod1-setup.finobe.wtf/FinobeLauncher.exe`
(`storage/setup/FinobeLauncher.exe`).

## Building

```bash
sudo apt install g++-mingw-w64-i686 binutils-mingw-w64-i686
./build.sh
```

Then copy `FinobeLauncher.exe` to `storage/setup/`. Uses [miniz](https://github.com/richgel999/miniz)
(MIT, `miniz.LICENSE`) for unzipping. `FINOBE_SETUP_URL` (environment variable) points it at a
different setup server for testing.
