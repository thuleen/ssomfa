# Single sign on multi factor authentication (SSOMFA)

This document explains how to setup the SSOMFA solution. The solution consists of four modules:

-   `mfa-contracts` - contains all the smart contracts
-   `mfa-dapp` - is the frontend built for mobile browser
-   `ssomfa` - is the Laravel package to be consumed by existing Laravel (legacy) application
-   `ssomfa--api` - is the api service that supports `ssomfa`.

## Signup

When an existing user of a Laravel application, for example called "Helpdesk" attempt to login, a QR code will be displayed prompting the user to scan. Once scan, using smart phone's camera, the user is directed to a mobile app or dapp.

Then, the user will click a button on the dapp to authorize and to display one-time-password (OTP).
In the Helpdesk app, the user will key in the OTP. Finally, once OTP is validated, the user can proceed using Helpdesk as usual.

## Login

Follow same signup steps as above.

# Intergration

It is assumed that the system intergrator have the priviledges to access and modify the target Laravel/PHP application in order to consume the `thuleen/ssomfa` package.

## 1. Environment

Include the following line in the `.env` file of the php application:

```
APP_ID=1
```

Then, the `'id' => env('APP_ID', '1'),` line in the `/config/app.php`:

```
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'id' => env('APP_ID', '1'),
    'name' => env('APP_NAME', 'Laravel'),
    ...
```

## 2. Routes

Add `'ssomfa_verify'` in the middleware array:

```
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', ssomfa_verify'])->name('dashboard');
```

## 3. Kernel

In the `app/Http/Kernel.php`;

```
    protected $middlewareAliases = [
        ...
        'ssomfa_verify' => \Thuleen\SsoMfa\Http\Middleware\SsoMfaMiddleware::class
    ];
```

## 4. AuthenticatedSessionController.php

Add `app(SsoMfaMiddleware::class)->logout();` in the `destroy` function:

```
public function destroy(Request $request): RedirectResponse
{
    app(SsoMfaMiddleware::class)->logout(); // <<-- Add this line

    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');
}
```

## 5. Install package

In the `composer.json` file edit as the followings:

```
    ...
    "repositories": [
        {
            "type": "path",
            "url": "/Users/azlan/projects/multi-factor-auth/ssomfa"
        }
    ],

    ...

     "require": {
        ...
        "thuleen/ssomfa": "dev-master"
    },

    ...

    "autoload": {
        "psr-4": {
            ...
            "Thuleen\\SsoMfa\\": "vendor/thuleen/ssomfa/src"
        }
    },
```

Then run:

```
composer update
composer dump-autoload
```

# Development

## Cloned three(3) repositories

Download all four - `mfa-contracts`, `mfa-dapp`, `ssomfa` and `ssomfa-api` into a "project" directory. If you do not have a project directory, create new. In this case I already have and it is called `multi-factor-auth`.

So change into the `multi-factor-auth` directory, then do:

```
git clone git@bitbucket.org:thuleen/mfa-contracts.git
git clone git@bitbucket.org:thuleen/mfa-dapp.git
git clone git@bitbucket.org:thuleen/ssomfa.git
git clone git@bitbucket.org:thuleen/ssomfa-api.git
```

## Geth network

For testing you can run local hardhat node. To run it, change into `mfa-contracts` directory and run:

```
npx hardhat node
```

> **PRODUCTION**<br />
> Ensure you have the access to the **production** version of the private Ethereum network.

## Deploy smart contract

Follow the steps below to deploy the **SSOMFA** solution.

### 1. Compile smart contract

Change into `mfa-contracts` and run `npm install`. Then:

```
npx hardhat compile
```

This will copy `artifacts` directory to `mfa-dapp/src/assets` directory and copy `contract-address.json` to `ssomfa-api/contract-address.json`.

**NOTES**

Sometime i got error <span style="color:red">Error HH700</span>. To solve:

```
npx hardhat clean
npm install
```

and, again re-run:

```
npx hardhat compile
```

### 2. Run deploy script

Before execute the deploy script, ensure the deployer account has sufficient ETH. Also it is important that you have access to its private key or mnemonics!

```
npx hardhat run scripts/deploy.ts --network localhost
```

Then run:

```
npm run copy
```

## Run ssomfa-api

Change into `ssomfa-api` directory. Make sure run `npm install`.

1. Configure the `.env` file and contains something like:

```
PORT=9000
VITE_APP_RPC_URL=http://192.168.100.9:8545/
VITE_APP_MFA_ADDR=0xB581C9264f59BF0289fA76D61B2D0746dCE3C30D
VITE_APP_DEPLOYER_PRIVATE_KEY=0xdf57089febbacf7...411a14efcf23656e
```

> Note that `VITE_APP_MFA_ADDR` will be updated in the next command. You may update the value in the env file if you wish.

2.  Check by running in dev mode:

```
npm run dev
```
