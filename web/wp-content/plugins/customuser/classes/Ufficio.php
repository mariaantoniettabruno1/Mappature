<?php
include_once '../includes/Connection.php';

class Ufficio
{
    private $meta_key = 'ufficio';
    private $ufficio;

    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getUfficio()
    {
        return $this->ufficio;
    }

    /**
     * @param mixed $ufficio
     */
    public function setUfficio($ufficio): void
    {
        $this->ufficio = $ufficio;
    }

    public function setUserUfficio($userid)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO user_has_metadata (user_id,name,value) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iss", $userid, $this->meta_key, $this->ufficio);
        $res = $stmt->execute();
        $mysqli->close();
    }
    public function editUserUfficio($userid){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE user_has_metadata SET  value=? WHERE user_id=? AND name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $this->ufficio, $userid, $this->meta_key);
        $res = $stmt->execute();
        $mysqli->close();
    }
//    public function getFormidUfficio()
//    {
//        return $this->formidUfficio;
//    }
//
//
//    public function setFormidUfficio(int $formidUfficio)
//    {
//        $this->formidUfficio = $formidUfficio;
//    }
//
//
//    public function getMetakeyUfficio()
//    {
//        return $this->metakeyUfficio;
//    }
//
//
//    public function setMetakeyUfficio(int $metakeyUfficio)
//    {
//        $this->metakeyUfficio = $metakeyUfficio;
//    }
//
//    public function selectUfficio()
//    {
//        $conn = new ConnectionSarala();
//        $mysqli = $conn->connect();
//        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE form_id=? and meta_key=?";
//        $stmt = $mysqli->prepare($sql);
//        $stmt->bind_param("ii", $this->formidUfficio, $this->metakeyUfficio);
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
}