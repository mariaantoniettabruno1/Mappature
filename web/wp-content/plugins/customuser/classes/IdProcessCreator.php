<?php

class IdProcessCreator
{
    public static function getProcessOwnerId($area_processo)
    {
        $res = array();
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT ALL meta_value FROM wp_usermeta WHERE meta_key ='id_kanboard'
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value='Dirigente')
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $area_processo);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_all();
        $mysqli->close();
        return $row;

    }

    public static function getProcedureOwnerId($area_procedimento, $servizio_procedimento, $ufficio_procedimento)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();

        $sql = "SELECT ALL meta_value FROM wp_usermeta WHERE meta_key ='id_kanboard'
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value='PO')
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value=?)
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value LIKE ?)
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value LIKE ?)";
        $stmt = $mysqli->prepare($sql);
        $servizio = "%$servizio_procedimento%";
        $ufficio = "%$ufficio_procedimento%";
        $stmt->bind_param("sss", $area_procedimento, $servizio, $ufficio);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_all();
        $mysqli->close();
        if (is_null($row)) {
            $id = self::getProcessOwnerId($area_procedimento);
            return $id;
        } else
            return $row;
    }

    public static function getAttoFaseOwnerId($wp_id)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();

        $sql = "SELECT meta_value FROM wp_usermeta WHERE meta_key ='id_kanboard' AND user_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $wp_id);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $mysqli->close();
        return $result['meta_value'];

    }


}