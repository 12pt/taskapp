<?php declare(strict_types=1);

final class Database {
    public function __construct(string $hostname, string $dbname, string $user, string $pass) {
        $dsn = "mysql:host=$hostname;dbname=$dbname;charset=utf8mb4";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        $this->pdo = new PDO($dsn, $user, $pass, $opt);
    }

}