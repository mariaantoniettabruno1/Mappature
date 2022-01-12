<?php

namespace MappaturePlugin;

class GForm
{
    public static function getForm($id_current_form){
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE entry_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $id_current_form);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = array();
        foreach ($res as $lines) {
            array_push($result, $lines["meta_value"]);
        }
        $mysqli->close();
        return $result;
    }

}