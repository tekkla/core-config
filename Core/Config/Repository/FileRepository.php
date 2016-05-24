<?php
namespace Core\Config\Repository;

/**
 * FileRepository.php
 *
 * @author Michael "Tekkla" Zorn <tekkla@tekkla.de>
 * @copyright 2016
 * @license MIT
 */
class FileRepository implements RepositoryInterface
{

    /**
     *
     * @var string
     */
    private $filename;

    public function __construct($filename) {
        $this->filename = $filename;
    }

    /**
     * Sets the filename from where the config has to be loaded and saved to
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Core\Config\ConfigRepositoryInterface::read()
     *
     */
    public function read()
    {
        if (empty($this->filename)) {
            Throw new RepositoryException(sprintf('There is no filename set for %s', __CLASS__));
        }

        if (! file_exists($this->filename)) {
            Throw new RepositoryException(sprintf('Config file $s is does not exists', $this->filename));
        }

        if (! is_readable($this->filename)) {
            Throw new RepositoryException(sprintf('Config file $s is not readable', $this->filename));
        }

        $array = include ($this->filename);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Core\Config\ConfigRepositoryInterface::write()
     *
     */
    public function write($data)
    {
        if (empty($this->filename)) {
            Throw new RepositoryException(sprintf('There is no filename set for %s', __CLASS__));
        }

        try {
            file_put_contents($this->filename, 'return ' . var_export($data, true));
        }
        catch (\Throwable $t) {
            Throw new RepositoryException($t->getMessage(), $t->getCode());
        }
    }
}
