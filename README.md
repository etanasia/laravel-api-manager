# Welcome to Bantenprov/laravel-api-manager!

[![Join the chat at https://gitter.im/laravel-api-manager/Lobby](https://badges.gitter.im/laravel-api-manager/Lobby.svg)](https://gitter.im/laravel-api-manager/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bantenprov/laravel-api-manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bantenprov/laravel-api-manager/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/bantenprov/laravel-api-manager/badges/build.png?b=master)](https://scrutinizer-ci.com/g/bantenprov/laravel-api-manager/build-status/master)

## Documents
-------------

Ini adalah package yang di gunakan untuk laravel api manager pemprov banten, dan package ini masih versi beta, found some bugs, text me at 085711511295 or drop email to ahmadnorin@gmail.com
update pull

> **Note:**

> - Package ini masih dalam tahap pengembangan.
> - package ini di gunakan untuk mengelola API KEY Provinsi Banten.
> - Package ini untuk laravel 4.2 keatas.
> - Package ini membutuhkan package Workflow. Anda bisa download di https://github.com/bantenprov/workflow

## Workflow
API Key Management ini dilengkapi dengan workflow management yang digunakan untuk melakukan proses permintaan API Key

### Workflow State
> - Request
> - Needs completed document
> - Document submitted
> - Approved by admin
> - Rejected by admin

### Workflow Trasition
> - Rejected by admin

#### <i class="icon-file"></i> Install package

```sh
composer require bantenprov/laravel-api-manager:dev-master
```
#### <i class="icon-file"></i> edit file config/app.php

tambahan class ini pada file config/app.php
```php
Collective\Html\HtmlServiceProvider::class,
'That0n3guy\Transliteration\TransliterationServiceProvider',
Bantenprov\LaravelApiManager\LaravelApiManagerServiceProvider::class,
```

#### <i class="icon-file"></i> running script vendor:publish

running vendor publish
```sh
php artisan vendor:publish
```

hasilnya kegini
```sh
Copied Directory [/vendor/bantenprov/laravel-api-manager/src/config] To [/config]
Copied Directory [/vendor/bantenprov/laravel-api-manager/src/views] To [/resources/views/api_manager]
Copied Directory [/vendor/bantenprov/laravel-api-manager/src/controller] To [/app/Http/Controllers]
Copied Directory [/vendor/bantenprov/laravel-api-manager/src/models] To [/app]
Copied Directory [/vendor/bantenprov/laravel-api-manager/src/migrations] To [/database/migrations]
Copied Directory [/vendor/laravel/framework/src/Illuminate/Mail/resources/views] To [/resources/views/vendor/mail]
Publishing complete.
```
#### <i class="icon-file"></i> tambahkan route

running script
```sh
php artisan laravel-api-manager:add-route
```

hasilnya akan menambahkan route resource di routes/web.php
```sh
Route::resource('api-manager', 'ApiManagerController');
```

#### <i class="icon-file"></i> Migrasi database

running script
```sh
php artisan migrate
php artisan make:middleware ApiKey
```

tambahkan ini pada file app/Http/Kernel.php
```php
protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ....
        'ApiKey' => \App\Http\Middleware\ApiKey::class,
    ];
```

dalam middleware ApiKey di app/Http/Middleware/ApiKey.php tambahkan ini.
```php
....
use Redirect;
use Validator;
use App\ApiKeys;

class ApiKey
{
    public function handle($request, Closure $next)
    {
        ....
        if($request->get('apikey') == '')
        {
            return response()->json([
                'error'     => true,
                'message'   => 'apikey not found',
                'data'      => []
                ]);
        }
        $check = ApiKeys::where('api_key', $request->get('apikey'))->first();
        if(count($check) == 0)
        {
            return response()->json([
                'error'     => true,
                'message'   => 'invalid apikey',
                'data'      => []
                ]);
        }
        return $next($request);
    }
}
```

dalam route web di routes/web.php tambahkan ini di route yang ingin menggunakan authentication apikey.
```php
->middleware('ApiKey');

//Atau

Route::group('middleware' => 'ApiKey'], function(){
  //Your Route
});
```

#### <i class="icon-file"></i> Running Modul

browse dari browser anda
```sh
http://your_domain.dev/api-manager
```
#### <i class="icon-file"></i> Running Modul

tambahkan pada .env anda parameter berikut
```sh
BANTENPROV_APIHOST=api.bantenprov.go.id
```
untuk production site
```sh
BANTENPROV_APIHOST=api-01.dev.bantenprov.go.id
```
untuk development site
#### <i class="icon-file"></i> Happy Coding  \\(*i^)//
