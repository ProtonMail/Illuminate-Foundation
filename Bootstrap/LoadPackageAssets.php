<?php

namespace Illuminate\Foundation\Bootstrap;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\PackageAssetLoader;
use Illuminate\Contracts\Foundation\Application;

class LoadPackageAssets
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $assetLoader = new PackageAssetLoader(new Filesystem, base_path('vendor'));

        foreach ($assetLoader->get('providers') as $provider) {
            $app->register($provider);
        }

        AliasLoader::getInstance($assetLoader->get('facades'))->register();
    }
}
