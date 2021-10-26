<?php
include_once 'Connection.php';
include_once 'ConnectionSarala.php';
function visualize_fase()
{
    $entry_gforms = GFAPI::get_entries(23);
    $fase = new Fase();
    $fase->setTitle($entry_gforms[0][1]);
    $fase->setNameProcedure($entry_gforms[0][3]);
    $fase->setNameProcess($entry_gforms[0][2]);
    $fase->setIdForm($entry_gforms[0]['form_id']);
    $fase->setId($entry_gforms[0]['id']);
    $fase->createFase();

}

add_shortcode('post_fase', 'visualize_fase');

class Fase{
    private $id;
    private $title;
    private $user_id;
    private $id_procedure;
    private $name_procedure;
    private $name_process;
    private $id_process;
    private $id_form;


    public function getIdForm()
    {
        return $this->id_form;
    }


    public function setIdForm($id_form)
    {
        $this->id_form = $id_form;
    }



    public function getIdProcess()
    {
        return $this->id_process;
    }


    public function setIdProcess($id_process)
    {
        $this->id_process = $id_process;
    }

    public function getId()
    {
        return $this->id;
    }


    public function setId($id)
    {
        $this->id = $id;
    }


    public function getTitle()
    {
        return $this->title;
    }


    public function setTitle($title)
    {
        $this->title = $title;
    }


    public function getUserId()
    {
        return $this->user_id;
    }


    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getIdProcedure()
    {
        return $this->id_procedure;
    }


    public function setIdProcedure($id_procedure)
    {
        $this->id_procedure = $id_procedure;
    }


    public function getNameProcedure()
    {
        return $this->name_procedure;
    }


    public function getNameProcess()
    {
        return $this->name_process;
    }

    public function setNameProcess($name_process)
    {
        $this->name_process = $name_process;
    }


    public function setNameProcedure($name_procedure)
    {
        $this->name_procedure = $name_procedure;
    }
    public function insertDataFaseSarala(){
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_fasi (id_fase,id_form,id_processo,id_procedimento) VALUES(?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iiii", $this->id, $this->id_form, $this->id_process,$this->id_procedure);
        $res = $stmt->execute();
        $mysqli->close();
    }
    public function createFase(){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id FROM tasks WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_procedure);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdProcedure($result['id']);
        $sql = "SELECT id FROM projects WHERE name=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_process);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdProcess($result['id']);

        $a = " - fase";
        $this->title = $this->title.$a;
        $sql = "INSERT INTO subtasks (title,task_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $this->title, $this->id_procedure);
        $res = $stmt->execute();
        $this->insertDataFaseSarala();
        $mysqli->close();

    }


}