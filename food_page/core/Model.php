<?php
declare(strict_types=1);

abstract class Model
{
    protected static function db(): PDO
    {
        return Database::connection();
    }
}

