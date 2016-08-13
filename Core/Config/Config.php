<?php
namespace Core\Config;

use Core\Config\Repository\RepositoryInterface;

/**
 * Config.php
 *
 * @author Michael "Tekkla" Zorn <tekkla@tekkla.de>
 * @copyright 2016
 * @license MIT
 */
final class Config implements ConfigInterface
{

    /**
     * Storage array for config values grouped by app name
     *
     * @var array
     */
    public $storage = [];

    /**
     * Flattened version of config definition grouped by app name
     *
     * @var array
     */
    public $structure = [];

    /**
     * Storage array for config definitions grouped by app names
     *
     * @var array
     */
    public $definitions = [];

    /**
     *
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * Constructor
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * Sets repositiory to use
     *
     * @param RepositoryInterface $repository
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Creates a named config storage object, adds it to the storages list and returns a reference to it.
     *
     * @param string $storage_name
     *            Name of storage to create
     *
     * @throws ConfigException
     *
     * @return ConfigStorage
     */
    public function &createStorage(string $storage_name): ConfigStorage
    {
        if (isset($this->storage[$storage_name])) {
            return $this->getStorage($storage_name);
        }

        $storage = new ConfigStorage();
        $storage->setName($storage_name);

        $this->storage[$storage_name] = $storage;

        return $storage;
    }

    /**
     * Requests a config storage by it's name and returns (if exists) a reference to it
     *
     * @param string $storage_name
     *            Name of config storage
     *
     * @return ConfigStorage
     */
    public function &getStorage(string $storage_name): ConfigStorage
    {
        if (!isset($this->storage[$storage_name])) {
            Throw new ConfigException(sprintf('A config storage with name "%s" does not exists.', $storage_name));
        }

        return $this->storage[$storage_name];
    }

    /**
     * Get a cfg value.
     *
     * @param string $app
     * @param string $key
     *
     * @throws ConfigException
     *
     * @return mixed
     */
    public function get(string $storage_name, string $key = '')
    {
        // Calls only with app name indicates, that the complete app config is requested
        if (empty($key) && !empty($this->storage[$storage_name])) {
            return $this->storage[$storage_name];
        }

        // Calls with app and key are normal cfg requests
        if (!empty($key)) {

            if (!isset($this->storage[$storage_name]->{$key})) {
                Throw new ConfigException(sprintf('Config "%s" of app "%s" does not exist.', $key, $storage_name));
            }

            return $this->storage[$storage_name]->{$key};
        }

        // All other will result in an error exception
        Throw new ConfigException(sprintf('Config "%s" of app "%s" not found.', $key, $storage_name));
    }

    /**
     * Set a cfg value.
     *
     * @param string $storage_name
     * @param string $key
     * @param mixed $val
     */
    public function set(string $storage_name, string $key, $val)
    {
        $this->storage[$storage_name]->set($key, $val);
    }

    /**
     * Checks the state of a cfg setting
     *
     * Returns true for set and false for not set.
     *
     * @param string $storage_name
     * @param string $key
     *
     * @return boolean
     */
    public function exists($storage_name, $key = null)
    {
        // No app found = false
        if (!isset($this->storage[$storage_name])) {
            return false;
        }

        // app found and no key requested? true
        if (!isset($key)) {
            return true;
        }

        // key requested and found? true
        return isset($this->storage[$storage_name]->{$key}) && !empty($this->storage[$storage_name]->{$key});
    }

    /**
     * Loads config from database
     *
     * @param boolean $refresh
     *            Optional flag to force a refresh load of the config that updates the cached config too
     *
     * @return void
     */
    public function load($refresh = false)
    {
        $results = $this->repository->read();

        /* @var $config \Core\Config\ConfigObject */
        foreach ($results as $config) {
            $storage = $this->createStorage($config->getStorage());
            $storage->set($config->getId(), $config->getValue());
        }
    }

    public function __get($offset)
    {
        return $this->storage[$offset];
    }
}
