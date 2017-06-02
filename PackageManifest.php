<?php

namespace Illuminate\Foundation;

use Exception;
use Illuminate\Filesystem\Filesystem;

class PackageManifest
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    public $files;

    /**
     * The base path.
     *
     * @var string
     */
    public $basePath;

    /**
     * The vendor path.
     *
     * @var string
     */
    public $vendorPath;

    /**
     * The manifest path.
     *
     * @var string|null
     */
    public $manifestPath;

    /**
     * The loaded manifest array.
     *
     * @var array
     */
    public $manifest;

    /**
     * Create a new package manifest instance.
     *
     * @param  \Illuminate\Foundation\Filesystem  $files
     * @param  string  $basePath
     * @param  string  $manifestPath
     * @return void
     */
    public function __construct(Filesystem $files, $basePath, $manifestPath)
    {
        $this->files = $files;
        $this->basePath = $basePath;
        $this->manifestPath = $manifestPath;
        $this->vendorPath = $basePath.'/vendor';
    }

    /**
     * Get all of the service provider class names for all packages.
     *
     * @return array
     */
    public function providers()
    {
        $this->ensureManifestIsLoaded();

        return collect($this->manifest)->flatMap(function ($configuration, $name) {
            return (array) ($configuration['providers'] ?? []);
        })->filter()->all();
    }

    /**
     * Get all of the aliases for all packages.
     *
     * @return array
     */
    public function aliases()
    {
        $this->ensureManifestIsLoaded();

        return collect($this->manifest)->flatMap(function ($configuration, $name) {
            return (array) ($configuration['aliases'] ?? []);
        })->filter()->all();
    }

    /**
     * Ensure the manifest has been loaded into memory.
     *
     * @return void
     */
    protected function ensureManifestIsLoaded()
    {
        if (! is_null($this->manifest)) {
            return;
        }

        if (! file_exists($this->manifestPath)) {
            $this->build();
        }

        if (file_exists($this->manifestPath)) {
            $this->manifest = $this->files->getRequire($this->manifestPath);
        } else {
            $this->manifest = [];
        }
    }

    /**
     * Build the manifest and write it to disk.
     *
     * @return void
     */
    public function build()
    {
        $installedFile = $this->vendorPath.'/composer/installed.json';

        $packages = [];
        if ($this->files->exists($installedFile)) {
            $packages = json_decode($this->files->get($installedFile), true);
        }

        $ignore = $this->packagesToIgnore();

        $this->write(collect($packages)->mapWithKeys(function ($package) {
            return [$this->format($package['name']) => $package['extra']['laravel'] ?? []];
        })->reject(function ($configuration, $package) use ($ignore) {
            return in_array($package, $ignore);
        })->filter()->all());
    }

    /**
     * Format the given package name.
     *
     * @param  string  $package
     * @return string
     */
    protected function format($package)
    {
        return str_replace($this->vendorPath.'/', '', $package);
    }

    /**
     * Get all of the package names that should be ignored.
     *
     * @return array
     */
    protected function packagesToIgnore()
    {
        if (! file_exists($this->basePath.'/composer.json')) {
            return [];
        }

        return json_decode(file_get_contents(
            $this->basePath.'/composer.json'
        ), true)['extra']['laravel']['dont-discover'] ?? [];
    }

    /**
     * Write the given manifest array to disk.
     *
     * @param  arary  $manifest
     * @return void
     */
    protected function write(array $manifest)
    {
        if (! is_writable(dirname($this->manifestPath))) {
            throw new Exception('The bootstrap/cache directory must be present and writable.');
        }

        $this->files->put(
            $this->manifestPath, '<?php return '.var_export($manifest, true).';'
        );
    }
}
