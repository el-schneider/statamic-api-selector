<?php

namespace ElSchneider\StatamicApiSelector\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class CacheManagementController extends Controller
{
    public function clearCache(Request $request)
    {
        $cacheKey = $request->input('cacheKey');

        Cache::forget($cacheKey);

        return response()->json(['message' => 'Cache cleared successfully for key: ' . $cacheKey]);
    }
}
