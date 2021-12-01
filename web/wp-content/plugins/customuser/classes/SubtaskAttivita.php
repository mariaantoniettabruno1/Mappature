<?php

include_once '../includes/Connection.php';
include_once '../includes/ConnectionSarala.php';

function create_attivita(){
    $entry_gforms = GFAPI::get_entries(24)[0];
    $attivita = new SubtaskAttivita();
    $attivita->setTitleAttivita($entry_gforms[1]);
    $attivita->setNameProcedureAttivita($entry_gforms[11]);
    $attivita->setNameProcessAttivita($entry_gforms[10]);
    $attivita->setIdFormAttivita($entry_gforms['form_id']);
    $attivita->setIdAttivita($entry_gforms['id']);

    foreach ($entry_gforms as $key => $value) {
        $pattern = "[^9.]";
        if (preg_match($pattern, $key) && $value) {
            $attivita->addUser($value);
        }
    }
    $attivita->createAttivita();



}
add_shortcode('post_create_attivita', 'create_attivita');
function create_attivita_postuma()
{
    $entry_gforms = GFAPI::get_entries(59)[0];
    $attivita = new SubtaskAttivita();
    $attivita->setTitleAttivita($entry_gforms[1]);
    $attivita->setNameProcedureAttivita($entry_gforms[3]);
    $attivita->setNameProcessAttivita($entry_gforms[2]);
    $attivita->setIdFormAttivita($entry_gforms['form_id']);
    $attivita->setIdAttivita($entry_gforms['id']);

    foreach ($entry_gforms as $key => $value) {
        $pattern = "[^7.]";
        if (preg_match($pattern, $key) && $value) {
            $attivita->addUser($value);
        }
    }
    $attivita->createAttivita();


}

add_shortcode('post_createattivitapostuma', 'create_attivita_postuma');

//function update_attivita()
//{
//    $entry_gforms = GFAPI::get_entries(41);
//    $atto = new Attività();
//    $id_current_form = $entry_gforms[0]['id'];
//    $results_atto = Form::getForm($id_current_form);
//    $atto->setTitleAttivita($results_atto[1]);
//
//    $entry = array('1' => $results_atto[1], '2' => $results_atto[2], '3' => $results_atto[3], '4' => $results_atto[4], '5' => $results_atto[5], '6' => $results_atto[6]);
//    $entry_gforms = GFAPI::get_entries(24);
//    $id_current_form = $entry_gforms[0]['id'];
//    $atto->setOldTitleAttivita($entry_gforms[0][1]);
//    $atto->update();
//    $result = GFAPI::update_entry($entry, $id_current_form);
//
//}
//
//add_shortcode('post_updateattivita', 'update_attivita');
//
//function delete_attivita()
//{
//    $entry_gforms = GFAPI::get_entries(24);
//    $id_current_form = $entry_gforms[0]['id'];
//    $atto = new Attività();
//    $atto->setTitleAttivita($entry_gforms[0][1]);
//    $atto->delete();
//    $result = GFAPI::delete_entry($id_current_form);
//}
//
//add_shortcode('post_deleteattivita', 'delete_attivita');

class SubtaskAttivita
{
    private $id_attivita;
    private $title_attivita;
    private $user_id;
    private $id_procedure_attivita;
    private $name_procedure_attivita;
    private $id_process_attivita;
    private $name_process_attivita;
    private $id_form_attivita;
    private $old_title_attivita;
    private $users;

    public function __construct()
    {
        $this->users = [];
    }
    public function getIdProcessAttivita()
    {
        return $this->id_process_attivita;
    }


    public function setIdProcessAttivita($id_process)
    {
        $this->id_process_attivita = $id_process;
    }

    public function getNameProcessAttivita()
    {
        return $this->name_process_attivita;
    }


    public function setNameProcessAttivita($name_process)
    {
        $this->name_process_attivita = $name_process;
    }


    public function getIdFormAttivita()
    {
        return $this->id_form_attivita;
    }


    public function setIdFormAttivita($id_form)
    {
        $this->id_form_attivita = $id_form;
    }


    public function getIdAttivita()
    {
        return $this->id_attivita;
    }


    public function setIdAttivita($id)
    {
        $this->id_attivita = $id;
    }


    public function getTitleAttivita()
    {
        return $this->title_attivita;
    }


    public function setTitleAttivita($title)
    {
        $this->title_attivita = $title;
    }


    public function getUserIdAttivita()
    {
        return $this->user_id;
    }


    public function setUserIdAttivita($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getIdProcedureAttivita()
    {
        return $this->id_procedure_attivita;
    }


    public function setIdProcedureAttivita($id_procedure)
    {
        $this->id_procedure_attivita = $id_procedure;
    }


    public function getNameProcedureAttivita()
    {
        return $this->name_procedure_attivita;
    }


    public function setNameProcedureAttivita($name_procedure)
    {
        $this->name_procedure_attivita = $name_procedure;
    }

    public function insertDataAttivitaSarala()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_attivita (id_attivita,id_form,id_processo,id_procedimento) VALUES(?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iiii", $this->id_attivita, $this->id_form_attivita, $this->id_process_attivita, $this->id_procedure_attivita);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function createAttivita()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "SELECT id FROM tasks WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_procedure_attivita);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdProcedureAttivita($result['id']);


        $sql = "SELECT id FROM projects WHERE name=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_process_attivita);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdProcessAttivita($result['id']);


        $a = " - attività";
        $this->title_attivita = $this->title_attivita . $a;

        $sql = "INSERT INTO subtasks (title,task_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $this->title_attivita, $this->id_procedure_attivita);
        $res = $stmt->execute();
        $subtask_id = $mysqli->insert_id;
        $sql = "INSERT INTO MAPP_subtask_users (subtask_id,user_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($this->users as $userId) {
            $stmt->bind_param("ii", $subtask_id, $userId);
            $res = $stmt->execute();
        }
        $mysqli->close();
        $this->insertDataAttivitaSarala();
    }

    public function update()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $dbTitle = $this->getDbTitle($this->title_attivita);
        $dbOldTitle = $this->getDbTitle($this->old_title_attivita);

        $sql = "UPDATE subtasks SET title=? WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $dbTitle, $dbOldTitle);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function delete()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $dbTitle = $this->getDbTitle($this->title_attivita);
        $sql = "DELETE FROM subtasks WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $dbTitle);
        $res = $stmt->execute();
        $mysqli->close();
    }

    private function getDbTitle($title)
    {
        return $title . " - attività";
    }

    /**
     * @return mixed
     */
    public function getOldTitleAttivita()
    {
        return $this->old_title_attivita;
    }

    /**
     * @param mixed $old_title_attivita
     */
    public function setOldTitleAttivita($old_title_attivita)
    {
        $this->old_title_attivita = $old_title_attivita;
    }
    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param array $users
     */
    public function setUsers(array $users)
    {
        $this->users = $users;
    }
    public function addUser($value)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT meta_value FROM wp_usermeta WHERE meta_key='id_kanboard' AND user_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $value);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        array_push($this->users, $result['meta_value']);
        $mysqli->close();

    }
}