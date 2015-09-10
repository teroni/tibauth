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

use Illuminate\Filesystem\Filesystem;

class FileFinder implements FinderInterface
{
    /**
     * The Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * The array of paths.
     *
     * @var array
     */
    protected $paths = array();

    /**
     * Constructor.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  array  $paths
     * @return void
     */
    public function __construct(Filesystem $filesystem, array $paths)
    {
        $this->paths = $paths;

        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritDoc}
     */
    public function findExtensions()
    {
        $extensions = array();

        foreach ($this->paths as $path) {
            $extensions = array_merge($extensions, $this->findExtensionsInPath($path));
        }

        return $extensions;
    }

    /**
     * {@inheritDoc}
     */
    public function addPath($path)
    {
        $this->paths[] = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function findExtensionsInPath($path)
    {
        $extensions = $this->filesystem->glob($path.'/*/*/extension.php');

        if ($extensions === false) {
            return array();
        }

        return $extensions;
    }
}
