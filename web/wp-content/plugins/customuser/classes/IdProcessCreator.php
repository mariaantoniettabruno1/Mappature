<?php

class IdProcessCreator
{
    public static function getProcessOwnerId($settore_processo){
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();

        $sql = "SELECT meta_value FROM wp_usermeta WHERE meta_key ='id_kanboard'
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value='Apicale')
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s",  $settore_processo);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        echo "<pre>";
        print_r($settore_processo);
        echo "</pre>";
        $mysqli->close();
        return $result['meta_value'];
    }

    public static function getProcedureOwnerId($settore_procedimento, $servizio_procedimento){
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();

        $sql = "SELECT meta_value FROM wp_usermeta WHERE meta_key ='id_kanboard'
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value='Responsabile Processo')
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value=?)
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss",  $settore_procedimento,$servizio_procedimento);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $mysqli->close();
        return $result['meta_value'];
    }

}