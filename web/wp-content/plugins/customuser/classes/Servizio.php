<?php
include_once '../includes/Connection.php';

class Servizio
{
    private $meta_key = 'servizio';
    private $servizio;


    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getServizio()
    {
        return $this->servizio;
    }

    /**
     * @param mixed $servizio
     */
    public function setServizio($servizio): void
    {
        $this->servizio = $servizio;
    }



    public function setUserServizio($userid)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO user_has_metadata (user_id,name,value) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iss", $userid, $this->meta_key, $this->servizio);
        $res = $stmt->execute();
        $mysqli->close();
    }
    public function editUserServizio($userid){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE user_has_metadata SET  value=? WHERE user_id=? AND name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $this->servizio, $userid, $this->meta_key);
        $res = $stmt->execute();
        $mysqli->close();
    }
//    public function getFormidServizio()
//    {
//        return $this->formidServizio;
//    }
//
//
//    public function setFormidServizio(int $formidServizio)
//    {
//        $this->formidServizio = $formidServizio;
//    }
//
//    public function getMetakeyServizio(): int
//    {
//        return $this->metakeyServizio;
//    }
//
//    public function setMetakeyServizio(int $metakeyServizio)
//    {
//        $this->metakeyServizio = $metakeyServizio;
//    }
//
//    public function selectServizio()
//    {
//        $conn = new ConnectionSarala();
//        $mysqli = $conn->connect();
//        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE form_id=? and meta_key=?";
//        $stmt = $mysqli->prepare($sql);
//        $stmt->bind_param("ii", $this->formidServizio, $this->metakeyServizio);
//        $res = $stmt->execute();
//        $res = $stmt->get_result();
//        $result = array();
//        foreach ($res as $lines) {
//            array_push($result, $lines["meta_value"]);
//        }
//        $mysqli->close();
//        return $result;
//
//    }
    public function selectServizio()
    {
        $form_id = 21;
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE form_id=? AND meta_key=1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $form_id);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_all();
        $mysqli->close();
        return $result;
    }
}