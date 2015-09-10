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

namespace Cartalyst\Filesystem\Adapters;

use Dropbox\Client;
use League\Flysystem\Dropbox\DropboxAdapter as Dropbox;

class DropboxAdapter implements AdapterInterface
{
    use ValidatorTrait;

    /**
     * Required fields.
     *
     * @var array
     */
    protected $required = [
        'token',
        'app_name',
    ];

    /**
     * {@inheritDoc}
     */
    public function connect(array $config)
    {
        $this->validate($config);

        $token = array_get($config, 'token');

        $app = array_get($config, 'app_name');

        $prefix = array_get($config, 'prefix');

        $client = new Client($token, $app);

        return new Dropbox($client, $prefix);
    }
}
