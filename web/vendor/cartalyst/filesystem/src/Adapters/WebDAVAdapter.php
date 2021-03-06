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

use Sabre\DAV\Client;
use League\Flysystem\WebDAV\WebDAVAdapter as WebDAV;

class WebDAVAdapter implements AdapterInterface
{
    use ValidatorTrait;

    /**
     * Required fields.
     *
     * @var array
     */
    protected $required = [
        'baseUri',
    ];

    /**
     * {@inheritDoc}
     */
    public function connect(array $config)
    {
        $this->validate($config);

        $client = new Client($config);

        return new WebDAV($client);
    }
}
