<?php
class misyDbInformation {
    private $CONNECTION_URL = null;
    private $DB_HOST = 'localhost';
    private $DB_USER = 'root';
    private $DB_DBNAME = 'misy';
    private $DB_PASSWORD ='';    
    private $DB_PORT = 3306;
    private $CHARSET = 'utf8mb4';
    private $SSL_CERTIFICATE = true;

    function misyGetDb($params) {
        if (empty($params)) { return 400; }

        $this -> CONNECTION_URL = match ($params) {
            'mysql' => "mysql:host={$this->DB_HOST};port={$this->DB_PORT};dbname={$this->DB_DBNAME};charset={$this->CHARSET}",
            'pgsql' => "pgsql:host={$this->DB_HOST};port={$this->DB_PORT};dbname={$this->DB_DBNAME};sslmode=require",
            default => 500
        };

        return $this -> CONNECTION_URL; 
    }

    //function getHost() { return $this -> DB_HOST; }
    function getUser() { return $this -> DB_USER; }
    //function getDbName() { return $this -> DB_DBNAME; }
    function getPassword() { return $this -> DB_PASSWORD; }
    //function getPort() { return $this -> DB_PORT; }
    //function getSslCertificate() { return $this -> SSL_CERTIFICATE; }
}