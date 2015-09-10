<?php

/**
 * Part of the Extensions package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Extensions
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Extensions;

use ArrayAccess;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Collection;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Cartalyst\Dependencies\DependencySorter;

class ExtensionBag extends Collection
{
    /**
     * The Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * The Extensions Finder instance
     *
     * @var \Cartalyst\Extensions\FinderInterface
     */
    protected $finder;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Cartalyst\Extensions\FinderInterface  $finder
     * @param  \Illuminate\Container\Container  $container
     * @param  array  $extensions
     * @return void
     */
    public function __construct(
        Filesystem $filesystem,
        FinderInterface $finder,
        Container $container = null,
        array $extensions = array(),
        CacheManager $cache = null
    ) {
        $this->filesystem = $filesystem;

        $this->finder = $finder;

        $this->container = $container;

        foreach ($extensions as $extension) {
            $this->register($extension);
        }

        $this->cache = $cache;
    }

    /**
     * Creates an Extension from the given fully qualified extension file.
     *
     * @param  string  $file
     * @return \Cartalyst\Extensions\ExtensionInterface
     * @throws \RuntimeException
     */
    public function create($file)
    {
        $attributes = $this->filesystem->getRequire($file);

        if ( ! is_array($attributes) || ! isset($attributes['slug'])) {
            throw new \RuntimeException("Malformed extension.php at path [{$file}].");
        }

        $slug = $attributes['slug'];

        unset($attributes['slug']);

        $namespace = null;

        if (isset($attributes['namespace'])) {
            $namespace = $attributes['namespace'];

            unset($attributes['namespace']);
        }

        return new Extension($this, $slug, dirname($file), $attributes, $namespace, $this->cache);
    }

    /**
     * Registers an extension with the bag.
     *
     * @param  mixed  $extension
     * @return void
     */
    public function register($extension)
    {
        if (is_string($extension)) {
            $extension = $this->create($extension);
        }

        $this->registerInstance($extension);
    }

    /**
     * Sorts all registered extensions by their dependencies.
     *
     * @return void
     */
    public function sortExtensions()
    {
        $sorter = new DependencySorter;

        foreach ($this->all() as $extension) {
            $sorter->add($extension->getSlug(), $extension->getDependencies());
        }

        $extensions = array();

        foreach ($sorter->sort() as $slug) {
            $extensions[$slug] = $this->items[$slug];
        }

        $this->items = $extensions;

        unset($extensions);
    }

    /**
     * Finds and registers Extensions with the Extension Bag.
     *
     * @return void
     */
    public function findAndRegisterExtensions()
    {
        foreach ($this->finder->findExtensions() as $extension) {
            $this->register($extension);
        }
    }

    /**
     * Returns all uninstalled extensions.
     *
     * @return array
     */
    public function allUninstalled()
    {
        return array_filter($this->all(), function (ExtensionInterface $extension) {
            return $extension->isUninstalled();
        });
    }

    /**
     * Returns all installed extensions.
     *
     * @return array
     */
    public function allInstalled()
    {
        return array_filter($this->all(), function (ExtensionInterface $extension) {
            return $extension->isInstalled();
        });
    }

    /**
     * Returns all installed but disabled extensions.
     *
     * @return array
     */
    public function allDisabled()
    {
        return array_filter($this->all(), function (ExtensionInterface $extension) {
            return $extension->isDisabled();
        });
    }

    /**
     * Returns all installed and enabled extensions.
     *
     * @return array
     */
    public function allEnabled()
    {
        return array_filter($this->all(), function (ExtensionInterface $extension) {
            return $extension->isEnabled();
        });
    }

    /**
     * Sets the IoC container associated with extensions.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the IoC container associated with extensions.
     *
     * @return \Illuminate\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Registers an instance of an extension with the bag.
     *
     * @param  \Cartalyst\Extensions\ExtensionInterface  $extension
     * @return void
     */
    protected function registerInstance(ExtensionInterface $extension)
    {
        $extension->register();

        $this->items[$extension->getSlug()] = $extension;
    }
}
