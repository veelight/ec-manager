# EC for Laravel

### Installation

1. From your laravel projects root folder in terminal run:

    composer require maiev/ec-manager

2. Register the package

- Laravel 5.5 and up Uses package auto discovery feature
- Laravel 5.4 and below Register the package with laravel in config/app.php under providers with the following:
```
    'providers' => [
      	Maiev\EC\Providers\ECServiceProvider::class
    ];
```
3. Publish the packages config file
```
    php artisan vendor:publish --provider="Maiev\EC\Providers\ECServiceProvider"
```



