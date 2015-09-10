<?php namespace Cartalyst\Widgets;
/**
 * Part of the Widgets package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Widgets
 * @version    1.1.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Closure;
use RuntimeException;
use InvalidArgumentException;
use Illuminate\Container\Container;
use Cartalyst\Extensions\ExtensionBag;

class WidgetResolver {

	/**
	 * The inversion of control container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $container;

	/**
	 * The Extension Bag used by the add-on.
	 *
	 * @var \Cartalyst\Extensions\ExtensionBag
	 */
	protected $extensionBag;

	/**
	 * Default namespace prefix for parsing keys.
	 *
	 * @var string
	 */
	protected $namespacePrefix = 'Widgets';

	/**
	 * List of registered items.
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Array of parsed keys.
	 *
	 * @var array
	 */
	protected $parsed = array();

	/**
	 * Create a new extension resolver.
	 *
	 * @param  \Illuminate\Container\Container  $container
	 * @param  \Cartalyst\Extensions\ExtensionBag  $extensionBag
	 * @return \Cartalyst\Widgets\WidgetResolver
	 */
	public function __construct(Container $container, ExtensionBag $extensionBag)
	{
		$this->container = $container;

		$this->extensionBag = $extensionBag;
	}

	/**
	 * Makes the resolvable instance for the given key.
	 *
	 * @param  string  $key
	 * @param  array  $parameters
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function make($key, array $parameters = array())
	{
		// If we haven't actually got the item, we'll attempt to auto-detect
		// it based on our Extension Bag and the given key.
		if ( ! isset($this->items[$key]))
		{
			$this->autoDetect($key);
		}

		$item = $this->items[$key];

		if ($item instanceof Closure)
		{
			return call_user_func_array($item, $parameters);
		}

		if (strpos($item, '@') === false)
		{
			throw new InvalidArgumentException("No @ character was found to separate the class and method to be loaded in [{$class}].");
		}

		list($class, $method) = explode('@', $item);

		$instance = $this->container->make($class);

		return call_user_func_array(array($instance, $method), $parameters);
	}

	/**
	 * Manually maps an item into the registered array.
	 *
	 * @param  string  $key
	 * @param  mixed  $item
	 * @return void
	 */
	public function map($key, $item)
	{
		$this->items[$key] = $item;
	}

	/**
	 * If the item has not been registered with the resolver, we will
	 * attempt to detect it based on the given key and register the
	 * item with this object.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function autoDetect($key)
	{
		list($class, $method) = $this->parseKey($key);

		$this->items[$key] = "{$class}@{$method}";
	}

	/**
	 * Parses an extension resolvable key and returns the corresponding class
	 * and method in an array.
	 *
	 * A second parameter can be provided to denote a namespace prefix
	 * (within the extension's namespace) for the class name.
	 *
	 * @param  string  $key
	 * @param  string  $namespacePrefix
	 * @return array
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	public function parseKey($key, $namespacePrefix = null)
	{
		// Default the namespace prefix
		$namespacePrefix = $namespacePrefix ?: $this->namespacePrefix;

		// Generate a cache key based on the key and prefix
		$cacheKey = $key;
		if ( ! is_null($namespacePrefix))
		{
			$cacheKey .= $namespacePrefix;
		}

		// If we have already parsed this key let's just return
		// the class and save on the overhead.
		if ( ! empty($this->parsed[$cacheKey]))
		{
			return $this->parsed[$cacheKey];
		}

		if ( ! str_contains($key, '::'))
		{
			throw new InvalidArgumentException("An extension must be provided in the format vendor/extension::class.method for key [{$key}].");
		}

		list($extensionSlug, $classKey) = explode('::', $key);

		// Check if the extension exists
		if (empty($this->extensionBag[$extensionSlug]))
		{
			throw new InvalidArgumentException("Extension [{$extensionSlug}] was not found on the Extension Bag.");
		}

		// Get the extension information
		$extension = $this->extensionBag[$extensionSlug];

		// Check if the extension is enabled
		if ( ! $extension->isEnabled())
		{
			throw new RuntimeException("Extension [{$extension->getSlug()}] is not enabled.");
		}

		// Check if we have a method name
		if (substr_count($classKey, '.') < 1)
		{
			$class  = $classKey;
			$method = 'show';
		}
		else
		{
			list($class, $method) = explode('.', $classKey);
		}

		// The class is the namespace of the extension plus the
		// dot-notation converted to a namespace structure.
		$className = $extension->getNamespace().'\\';

		// Add in the namespace prefix if one is specified
		if (isset($namespacePrefix))
		{
			$className .= $namespacePrefix.'\\';
		}
		$className .= str_replace(' ', '\\', ucwords($class));

		// Cache our parsed key and return it
		return $this->parsed[$cacheKey] = array($className, $method);
	}

	/**
	 * Get an item at a given offset.
	 *
	 * @param  mixed  $key
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->make($key);
	}

}
