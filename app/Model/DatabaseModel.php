<?php

namespace App\Model;

class DatabaseModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Insert a row/s in Database table
     *
     * @param string $statement
     * @param array $params
     */
    public function insert($statement, $params = [])
    {
        try {
            $this->executeQuery($statement, $params);
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Select record/s from table
     *
     * @param string $statement
     * @param array $params
     */
    public function select($statement, $params = [])
    {
        try {
            $stmt = $this->executeQuery($statement, $params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Update a row/s in a Database
     *
     * @param string $statement
     * @param array $params
     */
    public function update($statement, $params)
    {
        try {
            $this->executeQuery($statement, $params);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Delete a row/s in a database
     *
     * @param string $statement
     * @param array $params
     */
    public function delete($statement, $params)
    {
        try {
            $this->executeQuery($statement, $params);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Execute DB Query
     *
     * @param string $statement
     * @param array $params
     */
    private function executeQuery($statement, $params = [])
    {
        try {
            $stmt = $this->db->prepare($statement);
            $stmt->execute($params);

            return $stmt;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}
