<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;

use PDO;
use PDOException;

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

        $query = "SELECT COUNT(*) AS emailCount FROM $tableName WHERE email = :email";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':email', $email, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function generateUuid(): string
    {
        return Uuid::uuid7()->toString();
    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 15]);
    }

}
