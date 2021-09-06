<?php
class Connection
{
private $url;
private $username;
private $password;
private $dbname;

    public function __construct()
    {
        $this->url = "localhost:3306/";
        $this->username = "c1demomg3";
        $this->password = "mxnCouMD!6M8";
        $this->dbname = "c1kanboard";
    }

    function connect()
    {
        echo $this->url;
        $conn = new mysqli($this->url, $this->username, $this->password, $this->dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }


}