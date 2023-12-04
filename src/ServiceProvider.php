<?php

namespace ElSchneider\StatamicApiSelector;

use ElSchneider\StatamicApiSelector\Controllers\CacheManagementController;
use ElSchneider\StatamicApiSelector\Fieldtypes\ApiSelectorFieldtype;
use Illuminate\Support\Facades\Route;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [
        'input' => [
            'resources/js/addon.js',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    protected $fieldtypes = [
        ApiSelectorFieldtype::class,
    ];

    public function bootAddon()
    {
        $this->registerCpRoutes(function () {
            Route::post('/api-selector/clear-cache', [CacheManagementController::class, 'clearCache'])->name('api-selector.clear-cache');
        });
    }
}
