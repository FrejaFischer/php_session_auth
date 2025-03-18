<?php

require_once 'DBCredentials.php';

Class Database extends DBCredentials
{

    protected ?PDO $pdo;

    // the moment you instantiate the database class
    public function __construct()
    {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];

            $this->pdo = new PDO($dsn, $this->user, $this->password, $options);
    }

    // runs the moment the instance / an object of the class is destroyed or the script is finish running
    public function __destruct()
    {
        $this->pdo = null;
    }
}
