<?php

/**
 * Part of the Filesystem package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Filesystem
 * @version    3.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Filesystem;

use League\Flysystem\File as FileHandler;

class File extends FileHandler
{
    /**
     * Holds all the valid images MIME Types.
     *
     * @var array
     */
    protected $imagesMimeTypes = [
        'image/bmp',
        'image/gif',
        'image/jpeg',
        'image/png',
    ];

    /**
     * Returns the file contents.
     *
     * @return string
     */
    public function getContents()
    {
        return $this->filesystem->read($this->path);
    }

    /**
     * Returns the filename without the extension.
     *
     * @return string
     */
    public function getFilename()
    {
        return str_replace(".{$this->getExtension()}", '', basename($this->path));
    }

    /**
     * Returns the file full path.
     *
     * @return string
     */
    public function getFullpath()
    {
        return $this->filesystem->getAdapter()->applyPathPrefix($this->path);
    }

    /**
     * Checks if the uploaded file is an image.
     *
     * @return bool
     */
    public function isImage()
    {
        return in_array($this->getMimetype(), $this->imagesMimeTypes);
    }

    /**
     * Return the image width and height.
     *
     * @return array
     */
    public function getImageSize()
    {
        if (! $this->isImage()) {
            return [ 'width' => 0, 'height' => 0 ];
        }

        $raw = $this->filesystem->getAdapter()->read($this->path);

        $image = imagecreatefromstring($raw['contents']);

        $width  = imagesx($image);
        $height = imagesy($image);

        return compact('width', 'height');
    }

    /**
     * Returns the file extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }
}
