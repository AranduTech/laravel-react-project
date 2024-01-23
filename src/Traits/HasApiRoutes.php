<?php

namespace Arandu\LaravelMuiAdmin\Traits;

use Arandu\LaravelMuiAdmin\Http\Controllers\RepositoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait HasApiRoutes
{
    public function getDefaultApiUrls()
    {
        $schemaName = $this->getSchemaName();
        
        return [
            'list' => Str::plural($schemaName),
            'item' => $schemaName . '/{id}',
            'create' => [
                'url' => Str::plural($schemaName),
                'method' => 'post',
            ],
            'update' => [
                'url' => $schemaName . '/{id}',
                'method' => 'post',
            ],
            'delete' => [
                'url' => $schemaName . '/{id}',
                'method' => 'delete',
            ],
            'massDelete' => [
                'url' => Str::plural($schemaName) . '/delete',
                'method' => 'post',
            ]
        ];
    }

    public function getApiUrls()
    {
        $apiUrls = array_merge(
            $this->getDefaultApiUrls(),
            [
                //
            ]
        );

        if ($this->hasSoftDelete()) {
            $apiUrls['restore'] = [
                'url' => $this->getSchemaName() . '/{id}/restore',
                'method' => 'post',
            ];
            $apiUrls['forceDelete'] = [
                'url' => $this->getSchemaName() . '/{id}/force',
                'method' => 'delete',
            ];
            $apiUrls['massRestore'] = [
                'url' => Str::plural($this->getSchemaName()) . '/restore',
                'method' => 'post',
            ];
            $apiUrls['massForceDelete'] = [
                'url' => Str::plural($this->getSchemaName()) . '/forceDelete',
                'method' => 'post',
            ];
        }

        if ($this->hasImportable()) {
            $apiUrls['import'] = [
                'url' => Str::plural($this->getSchemaName()) . '/import',
                'method' => 'post',
            ];
        }

        if ($this->hasExportable()) {
            $apiUrls['export'] = Str::plural($this->getSchemaName()) . '/export';
        }

        return $apiUrls;
    }

    public function api()
    {
        $urls = $this->getApiUrls();

        foreach ($urls as $action => $url) {
            $method = 'get';

            if (is_array($url)) {
                $method = $url['method'];
                $url = $url['url'];
            }

            $overrides = config('admin.cms.controller_overrides', []);

            $controller = isset($overrides[static::class])
                ? $overrides[static::class]
                : RepositoryController::class;

            Route::$method($url, [$controller, $action])
                ->name('admin.' . $this->getSchemaName() . '.' . $action);
        }
    }
}