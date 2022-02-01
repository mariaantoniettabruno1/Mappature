<?php

namespace MappaturePlugin;

class UpdateThingsByRuolo
{
    public function delete_dipendente_from_customtable($idKanboard)
    {

        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "DELETE FROM MAPP_subtask_users WHERE user_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $idKanboard);
        $res = $stmt->execute();
        $sql = "DELETE FROM MAPP_task_users WHERE user_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $idKanboard);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function insert_po_in_customtable($idKanboard, $array_ids)
    {

        $conn = new Connection;
        $mysqli = $conn->connect();


        $sql = "INSERT INTO MAPP_task_users_owner (task_id,user_id) VALUES (?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids as $id) {
            $stmt->bind_param("ii", $id, $idKanboard);
            $res = $stmt->execute();

        }

        $mysqli->close();
    }

    public function find_dirigente($array_ids_task)
    {
        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "SELECT user_id FROM MAPP_task_users_creator";
        $result = mysqli_query($mysqli, $sql);
        $row = $result->fetch_assoc();

        $sql = "SELECT user_id FROM user_has_metadata WHERE user_id=? AND name='Ruolo' AND value='Dirigente'";
        $stmt = $mysqli->prepare($sql);

        foreach ($row as $user) {

            $stmt->bind_param("i", $user);
            $res = $stmt->execute();
            $res = $result->fetch_assoc();
        }
        if(empty($res)){
            $sql = "SELECT user_id FROM user_has_metadata WHERE user_id=? AND name='Ruolo' AND value='PO'";
            $stmt = $mysqli->prepare($sql);

            foreach ($row as $user) {

                $stmt->bind_param("i", $user);
                $res = $stmt->execute();
                $res = $result->fetch_assoc();
            }

            if(!empty($res)){
                $sql = "INSERT INTO MAPP_task_users_creator (task_id,user_id) VALUES (?,?)";
                $stmt = $mysqli->prepare($sql);
                foreach ($res as $id) {
                    foreach ($array_ids_task as $task){
                        $stmt->bind_param("ii", $task, $id);
                        $res = $stmt->execute();
                    }

                }
            }
        }

        $mysqli->close();


    }

}