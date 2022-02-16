<?php

namespace MappaturePlugin;

class TableProcessi
{
    public function getDataOfProcesso($name_processo)
    {
        $array = array();
        $name_tasks = array();
        $dirigente = array();
        $array_po = array();
        $subtasks = array();
        $array_dipendenti = array();
        $array_dipendenti_associati = array();

        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "SELECT id FROM projects WHERE name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $name_processo);
        $res = $stmt->execute();
        $res = $stmt->get_result();

        if (  $res->fetch_assoc()!=NULL && !empty($res->fetch_assoc()['id'])) {

            $id_processo = $res->fetch_assoc()['id'];

            $sql = "SELECT name FROM users WHERE id IN (SELECT user_id FROM MAPP_project_users_owner WHERE project_id=?) ";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $id_processo);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res->fetch_all();
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    array_push($dirigente, $row[0]);
                }

            }


            $sql = "SELECT title FROM tasks WHERE  project_id=? ";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $id_processo);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res->fetch_all();
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    array_push($name_tasks, $row[0]);
                }

            }

            $sql = "SELECT name FROM users WHERE id IN (SELECT user_id FROM MAPP_task_users_owner WHERE task_id IN (SELECT id FROM tasks WHERE title=?)) ";
            $stmt = $mysqli->prepare($sql);

            if (!empty($name_tasks)) {
                foreach ($name_tasks as $item) {
                    $stmt->bind_param("s", $item);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $rows = $result->fetch_all();

                    array_push($array, $item);

                    if (!empty($rows)) {
                        foreach ($rows as $row) {
                            array_push($array, $row[0]);
                        }
                        array_push($array_po, $array);
                    }


                }
            }


            $sql = "SELECT name FROM users WHERE id IN (SELECT user_id FROM MAPP_task_users WHERE task_id IN (SELECT id FROM tasks WHERE title=?)) ";
            $stmt = $mysqli->prepare($sql);

            if (!empty($name_tasks)) {
                foreach ($name_tasks as $item) {
                    $stmt->bind_param("s", $item);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $rows = $result->fetch_all();

                    array_push($array, $item);

                    if (!empty($rows)) {
                        foreach ($rows as $row) {
                            array_push($array, $row[0]);
                        }
                        array_push($array_dipendenti_associati, $array);
                    }


                }

            }



            $sql = "SELECT title FROM subtasks WHERE task_id IN (SELECT id FROM tasks WHERE title=?)";
            $stmt = $mysqli->prepare($sql);
            if (!empty($name_tasks)) {
                foreach ($name_tasks as $item) {
                    $stmt->bind_param("s", $item);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $rows = $result->fetch_all();
                }
                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        array_push($subtasks, $row[0]);
                    }

                }
            }





            $sql = "SELECT name FROM users WHERE id IN (SELECT user_id FROM MAPP_subtask_users WHERE subtask_id IN (SELECT id FROM subtasks WHERE title=?)) ";
            $stmt = $mysqli->prepare($sql);

            if (!empty($subtasks)) {
                foreach ($subtasks as $item) {
                    $array = array();

                    $stmt->bind_param("s", $item);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $rows = $result->fetch_all();


                    array_push($array, $item);
                    if (!empty($rows)) {
                        foreach ($rows as $row) {
                            array_push($array, $row[0]);
                        }
                        array_push($array_dipendenti, $array);
                    }


                }
            }


        }


        $table = array("Processo" => $name_processo,
            "Dirigente" => $dirigente,
            "Procedimento" => $name_tasks,
            "PO" => $array_po,
            "Dipendenti associati" => $array_dipendenti_associati,
            "Fase/Attivita" => $subtasks,
            "Dipendenti" => $array_dipendenti);

        $mysqli->close();
        return $table;
    }


}