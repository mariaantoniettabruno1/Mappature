<?php

include_once '../includes/Connection.php';
class Area
{
private $meta_key = 'area';
private $area;

    /**
     * @return mixed
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param mixed $area
     */
    public function setArea($area): void
    {
        $this->area = $area;
    }

    public function setUserArea($userid){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO user_has_metadata (user_id,name,value) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iss", $userid, $this->meta_key, $this->area);
        $res = $stmt->execute();
        $mysqli->close();
    }

public function editUserArea($userid){
    $conn = new Connection();
    $mysqli = $conn->connect();
    $sql = "UPDATE user_has_metadata SET  value=? WHERE user_id=? AND name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $this->area, $userid, $this->meta_key);
        $res = $stmt->execute();
    $mysqli->close();
}
}
//class Settore
//{
//    private $formidSettore = 17;
//
//    public function __construct()
//    {
//
//    }
//
//    public function getFormidSettore()
//    {
//        return $this->formidSettore;
//    }
//
//
//    public function setFormidSettore(int $formidSettore)
//    {
//        $this->formidSettore = $formidSettore;
//    }
//
//    public function selectSettore()
//    {
//        $conn = new ConnectionSarala();
//        $mysqli = $conn->connect();
//        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE form_id=?";
//        $stmt = $mysqli->prepare($sql);
//        $stmt->bind_param("i", $this->formidSettore);
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
//}