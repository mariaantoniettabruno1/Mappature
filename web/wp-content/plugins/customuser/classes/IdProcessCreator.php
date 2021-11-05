<?php

class IdProcessCreator
{
    public static function getProcessOwnerId($settore_processo)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();

        $sql = "SELECT meta_value FROM wp_usermeta WHERE meta_key ='id_kanboard'
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value='Apicale')
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $settore_processo);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $mysqli->close();
        return $result['meta_value'];
    }

    public static function getProcedureOwnerId($settore_procedimento, $servizio_procedimento, $ufficio)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();

        $sql = "SELECT meta_value FROM wp_usermeta WHERE meta_key ='id_kanboard'
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value='Responsabile Processo')
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value=?)
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value=?)
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $settore_procedimento, $servizio_procedimento, $ufficio);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $mysqli->close();
        if ($result['meta_value'] == NULL) {
            $id = self::getProcessOwnerId($settore_procedimento);
            return $id;
        } else
            return $result['meta_value'];
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
        print_r($result['meta_value']);
        return $result['meta_value'];

    }


}