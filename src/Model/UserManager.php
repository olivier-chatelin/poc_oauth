<?php

namespace App\Model;

class UserManager extends AbstractManager
{
    public const TABLE = 'user';

    public function selectOneByPseudo($pseudo)
    {
        $statement = $this->pdo->prepare('SELECT * FROM ' . self::TABLE . ' WHERE pseudo = :pseudo ');
        $statement->bindValue('pseudo', $pseudo);
        $statement->execute();
        return $statement->fetch();
    }
    public function createUser(array $userData): string
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO  ' . self::TABLE . ' (pseudo, avatar,created_at)
             VALUES (:pseudo, :avatar,NOW()) '
        );
        $statement->bindValue('pseudo', $userData['pseudo']);
        $statement->bindValue('avatar', $userData['avatar']);
        $statement->execute();
        return $this->pdo->lastInsertId();
    }
}
