<?php

namespace Models;

use Helpers\ResponseHelper;

use PDO;

class HelperModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function checkForDuplicateEmail($table_name, $email)
    {
        if (!is_string($email)) {
            return false;
        }

        $query = "SELECT * FROM $table_name WHERE email = :email";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
            exit;
        }
    }
}
