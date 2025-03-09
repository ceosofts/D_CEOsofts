<?php

namespace App\Support;

use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\Filesystem as FlysystemFilesystem;
use Illuminate\Support\Collection;

class CustomFilesystem extends FilesystemAdapter
{
    protected $localAdapter;
    protected $root;

    public function __construct(FlysystemFilesystem $driver, LocalFilesystemAdapter $adapter, array $config)
    {
        parent::__construct($driver, $adapter, $config);
        $this->localAdapter = $adapter;
        $this->root = $config['root'];
    }

    public function glob($pattern)
    {
        $fullPattern = $this->root . DIRECTORY_SEPARATOR . ltrim($pattern, '/');
        $files = glob($fullPattern);

        if ($files === false) {
            return [];
        }

        return Collection::make($files)
            ->map(function ($file) {
                return ltrim(substr($file, strlen($this->root)), '/');
            })
            ->values()
            ->all();
    }
}
