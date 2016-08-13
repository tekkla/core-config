<?php
namespace Core\Config;

/**
 * ConfigObject.php
 *
 * @author Michael "Tekkla" Zorn <tekkla@tekkla.de>
 * @copyright 2016
 * @license MIT
 */
class ConfigObject
{

    /**
     *
     * @var string
     */
    public $storage;

    /**
     *
     * @var string
     */
    public $id;

    /**
     *
     * @var mixed
     */
    public $value;

    /**
     * Sets storage name
     *
     * @param string $storage
     */
    public function setStorage(string $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Returns set storage name
     *
     * @return string
     */
    public function getStorage(): string
    {
        return $this->storage;
    }

    /**
     * Set config id (key)
     *
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * Returns set config id (key)
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set config value
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Returns set config value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
