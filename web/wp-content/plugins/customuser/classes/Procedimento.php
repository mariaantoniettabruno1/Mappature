<?php

include_once 'Connection.php';
include_once 'ConnectionSarala.php';

include_once "Form.php";
include_once "IdProcessCreator.php";


function crea_procedimento()
{
    $entry_gforms = GFAPI::get_entries(2);
    $procedure = new Procedimento();
    $procedure->setTitle($entry_gforms[0][2]);
    $procedure->setIdForm($entry_gforms[0]['form_id']);
    $procedure->setNameProcess($entry_gforms[0][17]);
    $settore = $entry_gforms[0][18];
    $procedure->setCreatorId(idProcessCreator::getProcessOwnerId($settore));
    $servizio = $entry_gforms[0][19];
    $ufficio = $entry_gforms[0][20];
    $procedure->setOwnerId(idProcessCreator::getProcedureOwnerId($settore, $servizio, $ufficio));

    //$procedure->setDateCreated($entry_gforms[0]['date_created']);
    //$procedure->setDateUpdated($entry_gforms[0]['date_updated']);
    $procedure->setPosition(1);
    $procedure->createProcedure();


}

add_shortcode('post_procedimento', 'crea_procedimento');


function update_procedimento()
{
    $entry_gforms = GFAPI::get_entries(37);
    $procedimento = new Procedimento();
    $id_current_form = $entry_gforms[0]['id'];
    $results_procedimento = Form::getForm($id_current_form);
    $procedimento->setTitle($results_procedimento[1]);
    $entry = array('1'=>$results_procedimento[1],'2'=>$results_procedimento[2],'3'=>$results_procedimento[3], '4'=>$results_procedimento[4], '5'=>$results_procedimento[5]);
    $entry_gforms = GFAPI::get_entries(2);
    echo "<pre>";
    print_r($entry_gforms);
    echo "</pre>";
    $id_current_form = $entry_gforms[0]['id'];
    $procedimento->setOldTitle($entry_gforms[0][2]);
    $procedimento->updateProcedure();
    $result = GFAPI::update_entry($entry,$id_current_form);

}

add_shortcode('post_updateprocedimento', 'update_procedimento');

function delete_procedimento()
{
    $entry_gforms = GFAPI::get_entries(2);
    $id_current_form = $entry_gforms[0]['id'];
    $procedimento = new Procedimento();
    $procedimento->setTitle($entry_gforms[0][2]);
    $procedimento->deleteProcedure();
    $result = GFAPI::delete_entry($id_current_form);

}

add_shortcode('post_deleteprocedimento', 'delete_procedimento');

class Procedimento
{
    private $id_procedure;
    private $title;
    private $description;
    private $date_completed;
    private $id_form;
    private $id_process;
    private $column_id;
    private $swimlane_id;
    private $name_process;
    private $creator_id;
    private $date_created;
    private $date_updated;
    private $position;
    private $old_title;
    private $owner_id;
    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getOwnerId()
    {
        return $this->owner_id;
    }

    /**
     * @param mixed $owner_id
     */
    public function setOwnerId($owner_id): void
    {
        $this->owner_id = $owner_id;
    }


    public function setIdProcedure($id_procedure)
    {
        $this->id_procedure = $id_procedure;
    }


    public function getIdProcedure()
    {
        return $this->id_procedure;
    }


    public function setIdTask($id_procedure)
    {
        $this->id_procedure = $id_procedure;
    }


    public function getTitle()
    {
        return $this->title;
    }


    public function setTitle($title)
    {
        $this->title = $title;
    }


    public function getDescription()
    {
        return $this->description;
    }


    public function setDescription($description)
    {
        $this->description = $description;
    }


    public function getDateCompleted()
    {
        return $this->date_completed;
    }


    public function setDateCompleted($date_completed)
    {
        $this->date_completed = $date_completed;
    }

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

    public function getNameProcess()
    {
        return $this->name_process;
    }

    public function setNameProcess($name_process)
    {
        $this->name_process = $name_process;
    }

    public function getSwimlaneId()
    {
        return $this->swimlane_id;
    }

    public function setSwimlaneId($swimlane_id)
    {
        $this->swimlane_id = $swimlane_id;
    }


    public function getColumnId()
    {
        return $this->column_id;
    }


    public function setColumnId($column_id)
    {
        $this->column_id = $column_id;
    }


    public function getCreatorId()
    {
        return $this->creator_id;
    }


    public function setCreatorId($creator_id)
    {
        $this->creator_id = $creator_id;
    }

    public function getDateCreated()
    {
        return $this->date_created;
    }


    public function setDateCreated($date_created)
    {
        $this->date_created = $date_created;
    }


    public function getDateUpdated()
    {
        return $this->date_updated;
    }


    public function setDateUpdated($date_updated)
    {
        $this->date_updated = $date_updated;
    }


    public function getPosition()
    {
        return $this->position;
    }


    public function setPosition($position)
    {
        $this->position = $position;
    }



    public function insertDataProcedureSarala()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_procedimenti (id_form,id_procedimento,id_processo) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iii", $this->id_form, $this->id_procedure, $this->id_process);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function createProcedure()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id FROM projects WHERE name=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_process);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdProcess($result['id']);


        $sql = "SELECT id FROM swimlanes WHERE project_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_process);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setSwimlaneId($result['id']);

        $sql = "SELECT id FROM columns WHERE project_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_process);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setColumnId($result['id']);

        $sql = "INSERT INTO tasks (title,project_id,column_id,swimlane_id,position,date_creation,date_modification,owner_id,creator_id) VALUES(?,?,?,?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("siiiiiiii", $this->title, $this->id_process, $this->column_id, $this->swimlane_id, $this->position,$this->date_created,$this->date_updated,$this->owner_id,$this->creator_id);
        $res = $stmt->execute();
        $sql = "SELECT id FROM tasks WHERE title=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->title);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $procedimento = $res->fetch_assoc();
        $this->setIdProcedure($procedimento['id']);
        $sql = "INSERT INTO project_daily_column_stats (project_id,column_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $this->id_process, $this->procedure_status_id);
        $res = $stmt->execute();
        $sql = "INSERT INTO project_daily_stats (project_id) VALUES(?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_process);
        $res = $stmt->execute();
        $this->insertDataProcedureSarala();


        $mysqli->close();

    }

    public function updateProcedure()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "UPDATE tasks SET title=? WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $this->title,  $this->old_title);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function deleteProcedure()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "DELETE  FROM tasks WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->title);
        $res = $stmt->execute();
        $mysqli->close();
    }

    /**
     * @return mixed
     */
    public function getOldTitle()
    {
        return $this->old_title;
    }

    /**
     * @param mixed $oldTitle
     */
    public function setOldTitle($oldTitle)
    {
        $this->old_title = $oldTitle;
    }


}