<?php

namespace ElSchneider\StatamicApiSelector\Fieldtypes;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Statamic\Fields\Fieldtype;

class ApiSelectorFieldtype extends Fieldtype
{
    protected static $title = 'API Selector';

    protected $icon = 'select';

    protected function configFieldItems(): array
    {
        return [
            'placeholder' => [
                'display' => __('Placeholder'),
                'instructions' => __('statamic::fieldtypes.select.config.placeholder'),
                'type' => 'text',
                'default' => '',
                'width' => 50,
            ],
            'endpoint_type' => [
                'display' => __('Endpoint Type'),
                'type' => 'select',
                'options' => [
                    'config' => 'Config',
                    'url' => 'URL',
                ],
                'default' => 'url',
                'width' => 25,
                'required' => true,
            ],
            'endpoint' => [
                'display' => __('Endpoint'),
                'type' => 'text',
                'placeholder' => __('URL / Config "dot" syntax variable.'),
                'width' => 75,
                'required' => true,
            ],
            'cache_minutes' => [
                'display' => __('Cache Duration'),
                'instructions' => __('How long API results should be cached for in minutes.'),
                'type' => 'text',
                'input_type' => 'number',
                'default' => 0,
                'width' => 25,
            ],
            'use_stale_cache' => [
                'display' => __('Use Stale Cache'),
                'instructions' => __('Use stale cache if API fails.'),
                'type' => 'toggle',
                'default' => true,
                'width' => 25,
            ],
            'data_set_key' => [
                'display' => __('Data Set Key'),
                'instructions' => __('If your data set isn\'t top-level, you can define it\'s location.'),
                'type' => 'text',
                'placeholder' => 'data.users',
                'width' => 25,
            ],
            'item_key' => [
                'display' => __('Item Key'),
                'instructions' => __('Define the unique identifier to be used as the option value.'),
                'type' => 'text',
                'default' => 'id',
                'width' => 25,
                'required' => true,
            ],
            'item_label' => [
                'display' => __('Item Label'),
                'instructions' => __('Define the value to be used as the option label.'),
                'type' => 'text',
                'default' => 'title',
                'width' => 25,
                'required' => true,
            ],
            'item_thumbnail' => [
                'display' => __('Thumbnail Key'),
                'instructions' => __('Define the key for thumbnail URLs.'),
                'type' => 'text',
                'placeholder' => 'image',
                'width' => 50,
            ],
            'clearable' => [
                'display' => __('Clearable'),
                'instructions' => __('statamic::fieldtypes.select.config.clearable'),
                'type' => 'toggle',
                'default' => false,
                'width' => 25,
            ],
            'multiple' => [
                'display' => __('Multiple'),
                'instructions' => __('statamic::fieldtypes.select.config.multiple'),
                'type' => 'toggle',
                'default' => false,
                'width' => 25,
            ],
            'searchable' => [
                'display' => __('Searchable'),
                'instructions' => __('statamic::fieldtypes.select.config.searchable'),
                'type' => 'toggle',
                'default' => true,
                'width' => 25,
            ],
            'cast_booleans' => [
                'display' => __('Cast Booleans'),
                'instructions' => __('statamic::fieldtypes.select.config.cast_booleans'),
                'type' => 'toggle',
                'default' => false,
                'width' => 25,
            ],
        ];
    }

    public function preload()
    {
        return [
            'endpoint' => $this->getEndpoint(),
            'options' => $this->getOptions(),
        ];
    }

    public function augment($value)
    {
        $data = collect($this->getData());
        $key = $this->config('item_key');

        $values = $data
            ->whereIn($key, $value);

        if (!is_array($value)) {
            return $values->first();
        }

        return $values->all();
    }

    public function preProcess($value)
    {
        if ($this->config('cast_booleans')) {
            if ($value === true) {
                return 'true';
            } elseif ($value === false) {
                return 'false';
            }
        }

        return $value;
    }

    public function preProcessIndex($value)
    {
        $data = collect($this->getData());
        $key = $this->config('item_key');
        $label = $this->config('item_label');

        return $data
            ->whereIn($key, $value)
            ->implode($label, ', ');
    }

    public function process($value)
    {
        if ($this->config('cast_booleans')) {
            if ($value === 'true') {
                return true;
            } elseif ($value === 'false') {
                return false;
            }
        }

        return $value;
    }

    private function getOptions()
    {
        $data = $this->getData();
        $dsKey = $this->config('data_set_key');

        $options = collect(data_get($data, $dsKey))
            ->map(function ($option) {
                $key = $this->config('item_key');
                $label = $this->config('item_label');
                $image = $this->config('item_thumbnail');

                $optionData = [
                    'label' => data_get($option, $label),
                    'value' => data_get($option, $key),
                ];

                if ($image && ($thumbnailUrl = data_get($option, $image, null)) !== null) {
                    $optionData['thumbnailUrl'] = $thumbnailUrl;
                }

                return $optionData;
            })
            ->all();

        return $options;
    }

    private function getData()
    {
        $key = $this->handle() . $this->getEndpoint();
        $minutes = $this->config('cache_minutes');

        try {
            if (!$data = Cache::get($key)) {
                $response = app(Client::class)->get($this->getEndpoint());

                $data = json_decode((string) $response->getBody(), true);

                // limit data to max 100 items
                // $data = array_slice($data, 0, 5);

                // check if "item_thumbnail" is set
                // then check if the key exists in the data
                // compile a list of image URLs
                if ($this->config('item_thumbnail') && $data) {
                    $thumbnailKey = $this->config('item_thumbnail');

                    $data = collect($data)
                        ->map(function ($item) use ($thumbnailKey) {
                            if (array_key_exists($thumbnailKey, $item)) {
                                $imageUrl = $item[$thumbnailKey];

                                // check if the imageUrl is already cached in glide
                                // if so, use that url instead

                                $url = route('statamic-api-selector.thumbnail', [
                                    'url' => $imageUrl,
                                ]);

                                // http request to get the thumbnail
                                $response = app(Client::class)->get($url);

                                // add the thumbnail url to the item
                                $item['thumbnailUrl'] = json_decode((string) $response->getBody(), true)['thumbnailUrl'];
                            }

                            return $item;
                        })
                        ->all();
                }

                if ($minutes > 0) {
                    Cache::put($key, $data, now()->addMinutes($minutes));
                }
            }
        } catch (\Exception $e) {
            // If there's an error and we have cached data, return the cached data

            if ($this->config('use_stale_cache') && $data = Cache::get($key)) {
                return $data;
            }

            // If there's no cached data, rethrow the exception
            throw $e;
        }

        return $data;
    }

    private function getEndpoint()
    {
        $endpoint = $this->config('endpoint');

        switch ($this->config('endpoint_type')) {
            case 'config':
                return config($endpoint);
            default:
                return $endpoint;
        }
    }
}
