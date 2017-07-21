<?php
namespace Core\Config\Repository;

/**
 * DbRepository.php
 *
 * @author Michael "Tekkla" Zorn <tekkla@tekkla.de>
 * @copyright 2016-2017
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
     * Sets PDO which should be used to store data
     *
     * @param \PDO $pdo
     */
    public function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Sets table name to store config in
     *
     * @param string $table
     */
    public function setTable(string $table)
    {
        $this->table = $table;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Core\Config\Repository\RepositoryInterface::read()
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
     *
     * {@inheritdoc}
     * @see \Core\Config\Repository\RepositoryInterface::write()
     */
    public function write()
    {}
}
