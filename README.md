# Single sign on multi factor authentication (SSOMFA)

# Intergration

It is assumed that developer have the priviledges to access and modify the target Laravel/PHP application in order to consume the `thuleen/ssomfa` package.

The following are the steps to integrate with the SSOMFA system.

# IMPORTANT

It is **required** that your laravel/php application to host on a secure connection using SSL (HTTPS).

## Install package

```
composer require thuleen/ssomfa
```

Take note the path of the `ssomfa`.

## 1. Environment

Include the following line in the `.env` file of your laravel/php application:

```
APP_ID=1
```

Then, the `'id' => env('APP_ID', '1'),` line in the `/config/app.php`:

```
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'id' => env('APP_ID', '1'),
    ...
```

## 2. Routes

In the `routes/web.php`, add `'ssomfa_verify'` like so:

```
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'ssomfa_verify'])->name('dashboard');
```

## 3. Kernel

In the `app/Http/Kernel.php`, add the following line in the last item;

```
    protected $middlewareAliases = [
        ...
        'ssomfa_verify' => \Thuleen\SsoMfa\Http\Middleware\SsoMfaMiddleware::class
    ];
```

## 4. AuthenticatedSessionController.php

In the `app/Http/Controllers/Auth/AuthenticatedSessionController.php`, add `app(SsoMfaMiddleware::class)->logout();` in the `destroy` function:

```

use Thuleen\Ssomfa\Http\Middleware\SsoMfaMiddleware; // <<-- Add this line

public function destroy(Request $request): RedirectResponse
{
    app(SsoMfaMiddleware::class)->logout();          // <<-- Add this line

    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');
}
```

## 5. Install package

Then, in the `composer.json` file edit as the followings:

```
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
