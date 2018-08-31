<?php

namespace Amber\Container\Container;

use Amber\Collection\CollectionAware\CollectionAwareInterface;
use Amber\Collection\CollectionAware\CollectionAwareTrait;
use Amber\Container\Config\ConfigAwareTrait;
use Amber\Container\Config\ConfigAwareInterface;
use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Service\Service;
use Amber\Validator\Validator;
use Psr\Container\ContainerInterface;

/**
 * Class for PSR-11 Container compliance.
 */
class SimpleBinder implements ContainerInterface, ConfigAwareInterface, CollectionAwareInterface
{
    use Finder, MultipleBinder, Validator, ConfigAwareTrait, CollectionAwareTrait;

    /**
     * The Container constructor.
     *
     * @param array $config The configurations for the Container.
     */
    public function __construct($config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Binds an item to the Container's map by a unique key.
     *
     * @param string $key   The unique item's key.
     * @param mixed  $value The value of the item.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bool True on success. False if key already exists.
     */
    final public function bind($key, $value = null)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        if (!$this->has($key)) {
            return $this->put($key, $value);
        }

        return false;
    }

    /**
     * Gets an item from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return mixed The value of the item.
     */
    final public function get($key)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        /* Retrieves the service from the map. */
        $service = $this->locate($key);

        if (!$this->isClass($service->value)) {
            return $service->value;
        }

        return $this->instantiate($service);
    }

    /**
     * Checks for the existance of an item on the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bool
     */
    final public function has($key)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        return $this->getCollection()->has($key);
    }

    /**
     * Unbinds an item from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bool true on success, false on failure.
     */
    final public function unbind($key)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        if ($this->getCollection()->has($key)) {
            $this->getCollection()->remove($key);

            return true;
        }

        return false;
    }
}
