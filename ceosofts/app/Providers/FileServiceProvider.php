<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\CustomFilesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\Filesystem as FlysystemFilesystem;

class FileServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('filesystem.local', function () {
            $root = storage_path('app');
            $adapter = new LocalFilesystemAdapter($root);
            $driver = new FlysystemFilesystem($adapter);

            return new CustomFilesystem($driver, $adapter, [
                'root' => $root,
                'disable_asserts' => true,
            ]);
        });
    }
}
