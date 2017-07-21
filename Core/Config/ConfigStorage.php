<?php
namespace Core\Config;

use Core\Storage\Storage;
use Core\Toolbox\Strings\IsSerialized;

/**
 * ConfigStorage.php
 *
 * @author Michael "Tekkla" Zorn <tekkla@tekkla.de>
 * @copyright 2016
 * @license MIT
 */
class ConfigStorage extends Storage
{

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var array
     */
    private $definition = [];

    /**
     *
     * @var array
     */
    private $structure = [];

    /**
     * Sets the name of storage
     *
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns set storage name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

/**
 * 
 * {@inheritDoc}
 * @see \Core\Storage\AbstractStorage::getValue()
 */
    public function getValue(string $key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        Throw new ConfigException(sprintf('Config "%s" does not exists in storage "%s"', $key, $this->name));
    }

    /**
     * Adds file paths to a storage
     *
     * @param string $storage_name
     * @param array $dirs
     */
    public function addPaths(array $dirs = [])
    {
        // Write dirs to config storage
        foreach ($dirs as $key => $val) {
            $this->data['dir.' . $key] = $val;
        }
    }

    /**
     * Adds urls to a storage
     *
     * @param string $storage_name
     * @param array $urls
     */
    public function addUrls(array $urls = [])
    {
        // Write urls to config storage
        foreach ($urls as $key => $val) {
            $this->data['url.' . $key] = $val;
        }
    }

    /**
     * Adds an array of config definitions of a storage
     *
     * @param string $storage_name
     *            The name storage this definitions are for
     * @param array $definition
     *            Array of definitions
     */
    public function setDefinition(array $definition)
    {
        // Store flattened config. The flattening process also takes care of missing definition data
        $this->definition = $definition;

        // Check existing config for missing entries and set default values on empty config values
        $this->checkDefaults($definition);
    }

    /**
     * Returns the set config definition
     *
     * @return array
     */
    public function getDefinition(): array
    {
        return $this->definition;
    }

    /**
     * Returns flattened version of config definition array
     *
     * @return array
     */
    public function getStructure(): array
    {
        return $this->structure;
    }

    /**
     * Sets config default values
     *
     * This method works recursive and checks the app given configstructure against the configdata loaded from db.
     * It checks for serialize flags in config definition and deserializes the config value if needed.
     * Fills the class structure property using the combined key as index and the controldefinition as data.
     *
     * @param string $storage_name
     *            Name of the app this config belongs to.
     * @param array $array
     *            Array with groups and/or controls to check for cfg values and default value and serialize state.
     * @param string $prefix
     *            Prefix used to build config key. Will be the current prefix + glue + current key.
     * @param string $glue
     *            The glue which combines prefix and key.
     */
    private function checkDefaults(array $definition, string $prefix = '', string $glue = '.')
    {
        // First step, check for controls
        if (!empty($definition['controls'])) {

            foreach ($definition['controls'] as $name => $control) {

                // Create the config key using the prefix passed as argument and the name used as index
                $key = (!empty($prefix) ? $prefix . $glue : '') . $name;

                $this->structure[$key] = $control;

                if (!isset($this->data[$key])) {
                    $this->data[$key] = !empty($control['default']) ? $control['default'] : null;
                }

                if (!empty($control['serialize'])) {

                    $string = new IsSerialized($this->data[$key]);

                    if ($string->isSerialized()) {
                        $this->data[$key] = unserialize($this->data[$key]);
                    }
                }
            }
        }

        // Do we have subgroups in this definition?
        if (!empty($definition['groups'])) {
            foreach ($definition['groups'] as $name => $group) {
                $this->checkDefaults($group, (!empty($prefix) ? $prefix . $glue : '') . $name);
            }
        }
    }
}

