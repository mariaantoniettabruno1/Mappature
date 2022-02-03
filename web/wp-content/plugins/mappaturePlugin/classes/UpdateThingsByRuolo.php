<?php

namespace MappaturePlugin;

class UpdateThingsByRuolo
{
    public function delete_dipendente_from_customtable($idKanboard)
    {
        //viene cancellato solo da questa tabella, perchè in MAPP_task_users_creator e MAPP_project_users_owner
        //se è presente non importa, in quanto li ci possono essere sia PO che dirigenti
        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "DELETE FROM MAPP_task_users_owner WHERE user_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $idKanboard);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function delete_po_from_customtable($idKanboard)
    {
        //delete per il passaggio da po a dirigente
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

    public function delete_po_dipendente_from_customtable($idKanboard)
    {
        //delete per il passaggio da po a dipendente
        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "DELETE FROM MAPP_task_users_owner WHERE user_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $idKanboard);
        $res = $stmt->execute();
        $sql = "DELETE FROM MAPP_task_users_creator WHERE user_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $idKanboard);
        $res = $stmt->execute();
        $sql = "DELETE FROM MAPP_project_users_owner WHERE user_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $idKanboard);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function insert_po_in_customtable($idKanboard, $array_ids)
    {
        //inserimento del PO nella custom table degli assegnatari delle task
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

    public function find_dirigente_for_procedimenti($array_ids_task)
    {
        //passaggio da dipendente a po
        //qui controllo se ci sono delle task che hanno area/servizio/ufficio del po su cui sono e se hanno come creator un dirigente o un po
        //Se hanno un po come creator poichè il dirigente è assente, inserisco anche il mio nuovo po nella custom table
        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "SELECT user_id FROM MAPP_task_users_creator WHERE task_id=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids_task as $idtask) {
            $stmt->bind_param("i", $idtask);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $result_user = $res->fetch_assoc();
        }

        $sql = "SELECT user_id FROM user_has_metadata WHERE user_id=? AND name='Ruolo' AND value='Dirigente'";
        $stmt = $mysqli->prepare($sql);

        foreach ($result_user as $user) {

            $stmt->bind_param("i", $user);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $result_ruolo_dirigente = $res->fetch_assoc();
        }

        if (empty($result_ruolo_dirigente)) {

            $sql = "SELECT user_id FROM user_has_metadata WHERE user_id=? AND name='Ruolo' AND value='PO'";
            $stmt = $mysqli->prepare($sql);

            foreach ($result_user as $user) {

                $stmt->bind_param("i", $user);
                $res = $stmt->execute();
                $res = $stmt->get_result();
                $result_ruolo_po = $res->fetch_assoc();
            }

            if (!empty($result_ruolo_po)) {
                $sql = "INSERT INTO MAPP_task_users_creator (task_id,user_id) VALUES (?,?)";
                $stmt = $mysqli->prepare($sql);
                foreach ($result_user as $id) {
                    foreach ($array_ids_task as $task) {
                        $stmt->bind_param("ii", $task, $id);
                        $res = $stmt->execute();
                    }

                }
            }
        }

        $mysqli->close();


    }

    public function find_dirigente_for_processi($array_ids_processi)
    {
        //passaggio da dipendente a po
        //qui controllo se ci sono dei processi che hanno area/servizio/ufficio del po su cui sono e se hanno come owner un dirigente o un po
        //Se hanno un po come owner del processo poichè il dirigente è assente, inserisco anche il mio nuovo po nella custom ta
        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "SELECT user_id FROM MAPP_project_users_owner WHERE project_id=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids_processi as $idprocesso) {
            $stmt->bind_param("i", $idprocesso);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $result_user = $res->fetch_assoc();
        }

        $sql = "SELECT user_id FROM user_has_metadata WHERE user_id=? AND name='Ruolo' AND value='Dirigente'";
        $stmt = $mysqli->prepare($sql);

        foreach ($result_user as $user) {

            $stmt->bind_param("i", $user);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $result_ruolo_dirigente = $res->fetch_assoc();
        }

        if (empty($result_ruolo_dirigente)) {

            $sql = "SELECT user_id FROM user_has_metadata WHERE user_id=? AND name='Ruolo' AND value='PO'";
            $stmt = $mysqli->prepare($sql);

            foreach ($result_user as $user) {

                $stmt->bind_param("i", $user);
                $res = $stmt->execute();
                $res = $stmt->get_result();
                $result_ruolo_po = $res->fetch_assoc();
            }

            if (!empty($result_ruolo_po)) {
                $sql = "INSERT INTO MAPP_project_users_owner (project_id,user_id) VALUES (?,?)";
                $stmt = $mysqli->prepare($sql);
                foreach ($result_user as $id) {
                    foreach ($array_ids_processi as $project) {
                        $stmt->bind_param("ii", $project, $id);
                        $res = $stmt->execute();
                    }

                }
            }
        }

        $mysqli->close();


    }

    public function insert_dirigente($array_ids_processi, $userid)
    {
        //passaggio da po a dirigente
        //inserisco il nuovo dirigente nella custom table dato che ho trovato dei processi che hanno i suoi stessi area/servizio/ufficio
        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_project_users_owner (project_id,user_id) VALUES (?,?)";
        $stmt = $mysqli->prepare($sql);

        foreach ($array_ids_processi as $project) {
            $stmt->bind_param("ii", $project, $userid);
            $res = $stmt->execute();

        }
        $mysqli->close();
    }

    public function link_dipendente_to_fase($array_ids_fase, $userid)
    {
        //passaggio da po a dipendente
        //collega il nuovo dipendente con le fasi e le attività che hanno in comune i suoi area/servizio/ufficio
        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_subtask_users (project_id,user_id) VALUES (?,?)";
        $stmt = $mysqli->prepare($sql);

        foreach ($array_ids_fase as $fase) {
            $stmt->bind_param("ii", $fase, $userid);
            $res = $stmt->execute();

        }
        $mysqli->close();
    }

}