<?php
namespace Core\Config\Repository;

/**
 * DbRepository.php
 *
 * @author Michael "Tekkla" Zorn <tekkla@tekkla.de>
 * @copyright 2016
 * @license MIT
 */
class DbRepository implements RepositoryInterface
{

    /**
     *
     * @var \PDO
     */
    private $pdo;

    /**
     *
     * @var string
     */
    private $table;

    /**
     *
     * @param \PDO $pdo
     */
    public function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     *
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Core\Config\ConfigRepositoryInterface::read()
     *
     */
    public function read()
    {
        if (empty($this->pdo)) {
            Throw new RepositoryException(sprintf('No PDO set for %s', __CLASS__));
        }

        if (empty($this->table)) {
            Throw new RepositoryException(sprintf('No read table name for %s', __CLASS__));
        }

        $stmt = $this->pdo->prepare("SELECT storage, id, value FROM $this->table ORDER BY storage, id");
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS, "\Core\Config\ConfigObject");
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Core\Config\ConfigRepositoryInterface::write()
     *
     */
    public function write()
    {}
}
