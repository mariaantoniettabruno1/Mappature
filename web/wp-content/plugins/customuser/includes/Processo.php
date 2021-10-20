<?php
include_once 'Connection.php';
include_once 'ConnectionSarala.php';
function visualize_processo()
{
    $entry_gforms = GFAPI::get_entries(1);
    $process = new Process();
    $process->setProcessName($entry_gforms[0][1]);
    $data = wp_get_current_user();
    $process->setIdUser($data->{'ID'});
    $process->setIdForm($entry_gforms[0]['form_id']);
    $process->setUserRole('project manager');
    $process->createProcess();


}

add_shortcode('post_processo', 'visualize_processo');

class Process
{
    private $process_name;
    private $id_process;
    private $id_user;
    private $user_role;
    private $id_form;
    private $first_position = 1;
    private $second_position = 2;
    private $third_position = 3;
    private $fourth_position = 4;
    private $first_title = "In attesa";
    private $second_title = "Pronto";
    private $third_title = "In corso";
    private $fourth_title = "Fatto";
    private $token = '';
    private $swimlanes_name = "Corsia predefinita";

    public function __construct()
    {

    }

    public function getUserRole()
    {
        return $this->user_role;
    }


    public function setUserRole($user_role)
    {
        $this->user_role = $user_role;
    }

    public function getIdUser()
    {
        return $this->id_user;
    }

    public function setIdUser($id_user)
    {
        $this->id_user = $id_user;
    }


    public function getProcessName()
    {
        return $this->process_name;
    }


    public function setProcessName($process_name)
    {
        $this->process_name = $process_name;
    }


    public function getIdProcess()
    {
        return $this->id_process;
    }


    public function setIdProcess($id_process)
    {
        $this->id_process = $id_process;
    }

    public function getIdForm()
    {
        return $this->id_form;
    }


    public function setIdForm($id_form)
    {
        $this->id_form = $id_form;
    }

    public function insertDataProcessSarala()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_processi (id_form,id_processo) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $this->id_form, $this->id_process);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function createProcess()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();


        $sql = "INSERT INTO projects (name,owner_id, token) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $this->process_name, $this->id_user, $this->token);
        $res = $stmt->execute();

        $sql = "SELECT id FROM projects WHERE name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->process_name);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $process = $res->fetch_assoc();
        $this->setIdProcess($process['id']);
        $sql = "INSERT INTO project_has_users (project_id,user_id,role) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iis", $this->id_process, $this->id_user, $this->user_role);
        $res = $stmt->execute();

        $sql = "INSERT INTO columns (project_id,position,title) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iis", $this->id_process, $this->first_position, $this->first_title);
        $res = $stmt->execute();
        $sql = "INSERT INTO columns (project_id,position,title) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iis", $this->id_process, $this->second_position, $this->second_title);
        $res = $stmt->execute();
        $sql = "INSERT INTO columns (project_id,position,title) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iis", $this->id_process, $this->third_position, $this->third_title);
        $res = $stmt->execute();
        $sql = "INSERT INTO columns (project_id,position,title) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iis", $this->id_process, $this->fourth_position, $this->fourth_title);
        $res = $stmt->execute();

        $sql = "INSERT INTO swimlanes (name,project_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $this->swimlanes_name, $this->id_process);
        $res = $stmt->execute();
        $mysqli->close();
        $this->insertDataProcessSarala();
    }


    public function deleteProcess($idProcess)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $this->setIdProcess($idProcess);
        $sql = "DELETE  FROM projects WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_process);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function updateProcess()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE projects SET name=? WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $this->process_name, $this->id_process);
        $res = $stmt->execute();
        $mysqli->close();
    }

}
