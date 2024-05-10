<?php

namespace Models;

use Helpers\ResponseHelper;

use PDO;
use RuntimeException;

class HelperModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function checkForDuplicateEmail($tableName, $email)
    {

        $query = "SELECT COUNT(*) AS emailCount FROM $tableName WHERE customerEmail = :email";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':email', $email, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
