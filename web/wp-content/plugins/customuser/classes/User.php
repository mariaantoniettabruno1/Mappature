<?php

class User
{
    private $username;
    private $email;
    private $name;
    private $idKanboard;
    private $password;

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }


    public function setIdKanboard($idKanboard)
    {
        $this->idKanboard = (int)$idKanboard;
    }

    public function getIdKanboard()
    {
        return $this->idKanboard;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {

        return $this->username;

    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }


    public function getEmail()
    {
        return $this->email;

    }


    public function updateUser()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE users SET name=?, email=?, password=? WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssi", $this->name, $this->email, $this->password, $this->idKanboard);
        $res = $stmt->execute();
//
//        $sql = "UPDATE user_has_metadata SET value=?  WHERE user_id=? AND name=?";
//        $stmt = $mysqli->prepare($sql);
//        $stmt->bind_param("sis", $this->value_servizio, $this->idKanboard, $this->servizio);
//        $res = $stmt->execute();
//        $sql = "UPDATE user_has_metadata SET  value=?  WHERE user_id=? AND name=?";
//        $stmt = $mysqli->prepare($sql);
//        $stmt->bind_param("sis", $this->value_ufficio, $this->idKanboard, $this->ufficio);
//        $res = $stmt->execute();
        $mysqli->close();
    }

    public function createUser()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO users (username,name,email,password) VALUES(?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssss", $this->username, $this->name, $this->email, $this->password);
        $res = $stmt->execute();
        $sql = "SELECT id FROM users WHERE username=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->username);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdKanboard($result['id']);
        $mysqli->close();
    }

    public function deleteUser($id_kan)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $this->setIdKanboard($id_kan);
        $sql = "DELETE FROM users WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->idKanboard);
        $res = $stmt->execute();
        $mysqli->close();
    }
}