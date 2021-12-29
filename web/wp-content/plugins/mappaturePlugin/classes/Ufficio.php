<?php
namespace MappaturePlugin;

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
    public function selectUfficio($area,$servizi)
    {

        $uffici = array();
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE form_id=20 AND meta_key=4 
                                          AND entry_id IN (SELECT entry_id FROM wp_gf_entry_meta WHERE meta_key=7 AND meta_value=?)
                                        AND entry_id IN (SELECT entry_id FROM wp_gf_entry_meta WHERE meta_key=8 AND meta_value=?)";
        $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ss", $area,$servizi);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $result = $res->fetch_all();
            foreach ($result as $item) {
                array_push($uffici, $item[0]);
            }

        $mysqli->close();
        return $uffici;
    }
    public function findUfficioByDipendente($dipendenti){

        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT value FROM user_has_metadata WHERE name='ufficio' AND user_id IN (SELECT id FROM users WHERE username=?)";
        foreach ($dipendenti as $dipendente) {

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $dipendente);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $result = $res->fetch_all();
        }

        $mysqli->close();
        return $result[0];
    }
}
