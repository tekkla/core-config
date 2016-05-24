<?php
namespace Core\Config\Repository;

/**
 * RepositoryInterface.php
 *
 * @author Michael "Tekkla" Zorn <tekkla@tekkla.de>
 * @copyright 2016
 * @license MIT
 */
interface RepositoryInterface
{
    /**
     * Reads and returns data from repository
     */
    public function read();

    /**
     * Writes data to the repository
     */
    public function write();
}
