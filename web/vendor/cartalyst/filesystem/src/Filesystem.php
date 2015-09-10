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

use League\Flysystem\Handler;
use League\Flysystem\Filesystem as Flysystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Cartalyst\Filesystem\Exceptions\FileExistsException;
use Cartalyst\Filesystem\Exceptions\InvalidMimeTypeException;
use Cartalyst\Filesystem\Exceptions\MaxFileSizeExceededException;
use League\Flysystem\FileExistsException as LeagueFileExistsException;

class Filesystem extends Flysystem
{
    /**
     * The Filesystem manager.
     *
     * @var \Cartalyst\Filesystem\FilesystemManager
     */
    protected $manager;

    /**
     * Flag to either overwrite a file on upload or not.
     *
     * @var bool
     */
    protected $overwrite = true;

    /**
     * The destination path for file uploads.
     *
     * @var string
     */
    protected $destination;

    /**
     * Returns the Filesystem manager.
     *
     * @return \Cartalyst\Filesystem\FilesystemManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Sets the Filesystem manager.
     *
     * @param  \Cartalyst\Filesystem\FilesystemManager  $manager
     * @return \Cartalyst\Filesystem\Filesystem
     */
    public function setManager(FilesystemManager $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Sets the flag to either overwrite the file or not when uploading.
     *
     * @param  bool  $status
     * @return $this
     */
    public function overwrite($status = true)
    {
        $this->overwrite = $status;

        return $this;
    }

    /**
     * Sets the destination path for file uploads.
     *
     * @param  string  $path
     * @return $this
     */
    public function saveTo($path)
    {
        $this->destination = $path;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function write($path, $contents, array $config = [])
    {
        $path = $this->prepareFileLocation($path);

        parent::write($path, $contents, $config);

        return $this->get($path);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, $contents, array $config = [])
    {
        $_path = $this->prepareFileLocation($path);

        $path = $this->has($_path) ? $_path : $path;

        return parent::put($path, $contents, $config);
    }

    /**
     * {@inheritDoc}
     */
    public function update($path, $contents, array $config = [])
    {
        parent::update($path, $contents, $config);

        return $this->get($path);
    }

    /**
     * {@inheritDoc}
     */
    public function get($path, Handler $handler = null)
    {
        return parent::get($path, new File($this, $path));
    }

    /**
     * Check if the given file is valid.
     *
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $file
     * @return bool
     * @throws \Cartalyst\Filesystem\Exceptions\MaxFileSizeExceededException
     * @throws \Cartalyst\Filesystem\Exceptions\InvalidMimeTypeException
     */
    public function validateFile(UploadedFile $file)
    {
        // Get the filesystem manager
        $manager = $this->getManager();

        // Get all the allowed mime types
        $allowedMimes = $manager->getAllowedMimes();

        // Validate the file size
        if ($file->getSize() > $manager->getMaxFileSize()) {
            throw new MaxFileSizeExceededException;
        }

        // Validate the file mime type
        if (! empty($allowedMimes) && ! in_array($file->getClientMimeType(), $allowedMimes)) {
            throw new InvalidMimeTypeException;
        }

        return true;
    }

    /**
     * Upload the file to the given destination.
     *
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $uploadedFile
     * @param  string  $destination
     * @param  array  $config
     * @return \Cartalyst\Filesystem\File
     * @throws \Cartalyst\Filesystem\Exceptions\FileExistsException
     */
    public function upload(UploadedFile $uploadedFile, $fileName = null, array $config = [])
    {
        try {
            $overwrite = $this->overwrite;

            $fileName = $fileName ?: $uploadedFile->getClientOriginalName();

            if (! $destination = $this->destination) {
                $path = $this->prepareFileLocation($uploadedFile, $fileName);

                $method = ($this->has($path) && $overwrite) ? 'update' : 'write';

                $destination = $method === 'update' ? $path : $fileName;
            }

            $method = ($this->has($destination) && $overwrite) ? 'update' : 'write';

            $uploaded = $this->{$method}($destination, file_get_contents($uploadedFile->getPathName()), $config);

            return $this->get($uploaded->getPath());
        } catch (LeagueFileExistsException $e) {
            throw new FileExistsException;
        }
    }

    /**
     * Prepares the file location name using the file dispersion feature.
     *
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile|string  $file
     * @param  string  $destination
     * @return string
     */
    public function prepareFileLocation($file, $destination = null)
    {
        $manager = $this->getManager();

        $placeholders = array_merge($manager->getPlaceholders(), [
            ' ' => '_',
        ]);

        if ($file instanceof UploadedFile) {
            $placeholders = array_merge($placeholders, [
                ':extension' => $file->getExtension(),
                ':mime'      => $file->getMimeType(),
            ]);
        }

        $destination = ($file instanceof UploadedFile) ? $destination : $file;

        $destination = $manager->getDispersion().$destination;

        return str_replace(array_keys($placeholders), array_values($placeholders), $destination);
    }
}
