<?php

namespace Arandu\LaravelMuiAdmin\Traits;

use Arandu\LaravelMuiAdmin\Http\Controllers\RendererController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use InvalidArgumentException;

trait HasWebRoutes
{
    public function getDefaultWebUrls()
    {
        $schemaName = $this->getSchemaName();

        return [
            'index' => Str::plural($schemaName),
            // 'new' => $schemaName . '/create',
            // 'edit' => $schemaName . '/update',
            // 'item' => $schemaName . '/{id}',
        ];
    }

    /**
     * 
     * @return string[] 
     * @throws InvalidArgumentException 
     */
    public function getWebUrls()
    {
        return array_merge(
            $this->getDefaultWebUrls(),
            [
                //
            ]
        );
    }

    public function web()
    {
        $urls = $this->getWebUrls();

        foreach ($urls as $page => $url) {
            Route::get($url, [RendererController::class, 'render'])
                ->name('admin.' . $this->getSchemaName() . '.' . $page);
        }
    }

}