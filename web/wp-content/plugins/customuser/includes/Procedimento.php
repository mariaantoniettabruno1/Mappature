<?php

include_once 'Connection.php';
include_once 'ConnectionSarala.php';

function visualize_procedimento()
{
    $entry_gforms = GFAPI::get_entries(2);
    $procedure = new Procedure();
    $procedure->setTitle($entry_gforms[0][2]);
    $procedure->setIdForm($entry_gforms[0]['form_id']);
    $procedure->createProcedure();

}
add_shortcode('post_procedimento', 'visualize_procedimento');

class Procedure
{
    private $id_procedure;
    private $title;
    private $description;
    private $date_creation;
    private $date_completed;
    private $id_form;

    public function __construct()
    {

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


    public function getDateCreation()
    {
        return $this->date_creation;
    }


    public function setDateCreation($date_creation)
    {
        $this->date_creation = $date_creation;
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


    public function insertDataProcedureSarala()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_procedimenti (id_form,id_procedimento) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $this->id_form, $this->id_procedure);
        $res = $stmt->execute();
    }

    public function createProcedure()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO tasks (title,description,date_creation,date_completed) VALUES(?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssii", $this->title, $this->description, $this->date_creation, $this->date_completed);
        $res = $stmt->execute();
        $sql = "SELECT id FROM tasks WHERE title=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->title);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        if ($procedure = $res->fetch_assoc()) {
            $this->setIdProcedure($procedure['id']);
            $this->insertDataProcedureSarala();
        }

        $mysqli->close();

    }

    public function updateProcedure()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE tasks SET title=?,description=?,date_creation=?,date_completed=? WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssiii", $this->title, $this->description, $this->date_creation, $this->date_completed, $this->id_procedure);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function deleteProcedure($idProcedure)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $this->setIdTask($idProcedure);
        $sql = "DELETE  FROM tasks WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_procedure);
        $res = $stmt->execute();
        $mysqli->close();
    }


}