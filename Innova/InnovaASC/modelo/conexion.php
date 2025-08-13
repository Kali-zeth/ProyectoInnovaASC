<?php
class Conectar
{
    private $host, $user, $pass, $database;

    public function __construct()
    {
        $this->host = "localhost";
        $this->user = "root";
        $this->pass = "";
        $this->database = "distribuidora";
    }

    public function conexion()
    {
        $con = new mysqli($this->host, $this->user, $this->pass, $this->database);
        return $con;
    }
}
