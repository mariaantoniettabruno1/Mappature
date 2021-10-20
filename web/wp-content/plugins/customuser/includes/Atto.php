<?php
include_once 'Connection.php';
include_once 'ConnectionSarala.php';
function visualize_atto()
{
    $entry_gforms = GFAPI::get_entries(24);
    $atto = new Atto();
    $atto->setTitleAtto($entry_gforms[0][1]);
    $atto->setNameProcedureAtto($entry_gforms[0][3]);
    $atto->setNameProcessAtto($entry_gforms[0][2]);
    $atto->setIdFormAtto($entry_gforms[0]['form_id']);
    $atto->setIdAtto($entry_gforms[0]['id']);
    $atto->createAtto();

}

add_shortcode('post_atto', 'visualize_atto');

class Atto{
    private $id_atto;
    private $title_atto;
    private $user_id;
    private $id_procedure_atto;
    private $name_procedure_atto;
    private $id_process_atto;
    private $name_process_atto;
    private $id_form_atto;


    public function getIdProcessAtto()
    {
        return $this->id_process_atto;
    }


    public function setIdProcessAtto($id_process)
    {
        $this->id_process_atto = $id_process;
    }

    public function getNameProcessAtto()
    {
        return $this->name_process_atto;
    }


    public function setNameProcessAtto($name_process)
    {
        $this->name_process_atto = $name_process;
    }


    public function getIdFormAtto()
    {
        return $this->id_form_atto;
    }


    public function setIdFormAtto($id_form)
    {
        $this->id_form_atto = $id_form;
    }



    public function getIdAtto()
    {
        return $this->id_atto;
    }


    public function setIdAtto($id)
    {
        $this->id_atto = $id;
    }


    public function getTitleAtto()
    {
        return $this->title_atto;
    }


    public function setTitleAtto($title)
    {
        $this->title_atto = $title;
    }



    public function getUserIdAtto()
    {
        return $this->user_id;
    }


    public function setUserIdAtto($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getIdProcedureAtto()
    {
        return $this->id_procedure_attp;
    }


    public function setIdProcedureAtto($id_procedure)
    {
        $this->id_procedure_atto = $id_procedure;
    }


    public function getNameProcedureAtto()
    {
        return $this->name_procedure_atto;
    }


    public function setNameProcedureAtto($name_procedure)
    {
        $this->name_procedure_atto = $name_procedure;
    }

    public function insertDataAttoSarala(){
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_atti (id_atto,id_form,id_processo,id_procedimento) VALUES(?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iiii", $this->id_atto, $this->id_form_atto, $this->id_process_atto,$this->id_procedure_atto);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function createAtto(){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id FROM tasks WHERE title=?";

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_procedure_atto);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdProcedureAtto($result['id']);

        $sql = "SELECT id FROM projects WHERE name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_process_atto);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdProcessAtto($result['id']);
        $sql = "INSERT INTO subtasks (title,task_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $this->title_atto, $this->id_procedure_atto);
        $res = $stmt->execute();
        $mysqli->close();
        $this->insertDataAttoSarala();
    }


}