<?php

namespace MappaturePlugin;

class TableUtente
{

    public function getAllUsers()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT user_nicename FROM wp_users";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all();
        mysqli_close($mysqli);
        return $row;
    }

    public function getIdKanboard($username)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT user_login FROM wp_users WHERE user_nicename=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $username);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $user_id = $res->fetch_assoc();
        $sql = "SELECT m1.meta_value FROM wp_usermeta AS m1 JOIN 
                wp_usermeta AS m2 ON m1.user_id = m2.user_id
                     WHERE (m1.meta_key='id_kanboard') 
                    AND (m2.meta_key='nickname' AND m2.meta_value=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $user_id['user_login']);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $id_kan = $res->fetch_assoc();
        mysqli_close($mysqli);
        return $id_kan['meta_value'];
    }
    public function selectProcessoUtente($id_kan){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT name FROM projects WHERE id IN (SELECT project_id FROM MAPP_project_users_owner WHERE user_id=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $id_kan);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $projects = $res->fetch_all();
        mysqli_close($mysqli);
        return $projects;
    }
    public function selectProcedimentoUtenteCreator($id_kan){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT title FROM tasks WHERE id IN (SELECT task_id FROM MAPP_task_users_creator WHERE user_id=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $id_kan);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $task_creator = $res->fetch_all();
        mysqli_close($mysqli);
        return $task_creator;
    }
    public function selectProcedimentoUtenteOwner($id_kan){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT title FROM tasks WHERE id IN (SELECT task_id FROM MAPP_task_users_owner WHERE user_id=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $id_kan);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $task_owner = $res->fetch_all();
        mysqli_close($mysqli);
        return $task_owner;
    }
    public function selectProcedimentoUtenteDipendente($id_kan){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT title FROM tasks WHERE id IN (SELECT task_id FROM MAPP_task_users WHERE user_id=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $id_kan);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $task_dip = $res->fetch_all();
        mysqli_close($mysqli);
        return $task_dip;
    }
    public function selectFaseAttivitaUtente($id_kan){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT title FROM subtasks WHERE id IN (SELECT subtask_id FROM MAPP_subtask_users WHERE user_id=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $id_kan);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $task_dip = $res->fetch_all();
        mysqli_close($mysqli);
        return $task_dip;
    }
}