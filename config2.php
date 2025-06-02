<?php 
class Database { //class, objek, properti, method
    private $host = "localhost";
    private $user = "root";
    private $pass;
    private $db = "kotak_saran";
    protected $conn;

    public function __construct(){ //construct
        $this->conn = new mysqli($this->host, $this->user,$this->pass, $this->db);
        if ($this->conn->connect_error){
            die("Connection failed: ". $this->conn->connect_error);
        }
    }
    public function getConnection(){ //visibility
        return $this->conn;
        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        }
    }

}
// Tambahkan di config2.php

?>
