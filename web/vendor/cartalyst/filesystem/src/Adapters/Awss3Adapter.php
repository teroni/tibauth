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

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v2\AwsS3Adapter as AwsS3;

class Awss3Adapter implements AdapterInterface
{
    use ValidatorTrait;

    /**
     * Required fields.
     *
     * @var array
     */
    protected $required = [
        'key',
        'secret',
        'bucket',
    ];

    /**
     * {@inheritDoc}
     */
    public function connect(array $config)
    {
        $key = array_get($config, 'key');

        $secret = array_get($config, 'secret');

        $bucket = array_get($config, 'bucket');

        $options = array_get($config, 'options', []);

        $prefix = array_get($config, 'prefix');

        $this->validate($config);

        $client = S3Client::factory($config);

        return new AwsS3($client, $bucket, $prefix, $options);
    }
}
