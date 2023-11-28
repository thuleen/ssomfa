# Single sign on multi factor authentication (SSOMFA)

# Intergration

It is assumed that developer have the priviledges to access and modify the target Laravel/PHP application in order to consume the `thuleen/ssomfa` package.

The following are the steps to integrate with the SSOMFA system.

# IMPORTANT

It is required that your laravel/php based application to host on a secure connection using SSL (HTTPS).

## 1. Environment

Include the following line in the `.env` file of your php application:

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

Download the `ssomfa` package.

Then, in the `composer.json` file edit as the followings:

```
    ...
    "repositories": [
        {
            "type": "path",
            "url": "/path/to/package/ssomfa"
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
