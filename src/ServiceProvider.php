<?php

namespace ElSchneider\StatamicApiSelector;

use ElSchneider\StatamicApiSelector\Fieldtypes\ApiSelectorFieldtype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Statamic\Facades\Image;
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
        // register an action route to process thumbmails, that can only be accessed authenticated from the cp
        $this->registerWebRoutes(function () {

            Route::get('/thumbnail', function (Request $request) {
                $externalUrl = $request->query('url');

                try {
                    // Process the image using Statamic's Image facade
                    $manipulator = Image::manipulate($externalUrl, [
                        'w' => 50,
                        'h' => 50,
                        'fm' => 'jpg',
                        // additional Glide parameters as needed
                    ]);

                    // Return the URL of the processed image
                    return response()->json(['thumbnailUrl' => $manipulator]);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            })->name('statamic-api-selector.thumbnail')->middleware('statamic.cp');
        });
    }
}
