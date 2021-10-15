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
    public function insertDataProcessSarala(){
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_processi (id_form,id_processo) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $this->id_form, $this->id_process);
        $res = $stmt->execute();
    }

    public function createProcess()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO projects (name,owner_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $this->process_name, $this->id_user);
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



class SubTask
{

    private $id_subtask;
    private $title;
    private $status;
    private $time_estimated;
    private $time_spent;
    private $position;

    public function __construct()
    {

    }

    public function getIdSubtask()
    {
        return $this->id_subtask;
    }


    public function setIdSubtask($id_subtask)
    {
        $this->id_subtask = $id_subtask;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getStatus()
    {
        return $this->status;
    }


    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getTimeEstimated()
    {
        return $this->time_estimated;
    }


    public function setTimeEstimated($time_estimated)
    {
        $this->time_estimated = $time_estimated;
    }


    public function getTimeSpent()
    {
        return $this->time_spent;
    }


    public function setTimeSpent($time_spent)
    {
        $this->time_spent = $time_spent;
    }


    public function getPosition()
    {
        return $this->position;
    }


    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function setDateCompleted($date_completed)
    {
        $this->date_completed = $date_completed;
    }

    public function updateSubTask()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        if ($this->id_subtask != NULL) {
            $sql = "UPDATE subtasks SET title=?,status=?,time_estimated=?,time_spent=?, position=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("siiiii", $this->title, $this->status, $this->time_estimated, $this->time_spent, $this->position, $this->id_subtask);
            $res = $stmt->execute();
        } else {
            $sql = "INSERT INTO subtasks (title,status,time_estimated,time_spent,position ) VALUES(?,?,?,?,?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("siiii", $this->title, $this->status, $this->time_estimated, $this->time_spent, $this->position);
            $res = $stmt->execute();
        }
        $mysqli->close();
    }

    public function deleteSubTask($idSubtask)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $this->setIdSubtask($idSubtask);
        $sql = "DELETE  FROM subtasks WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_subtask);
        $res = $stmt->execute();
        $mysqli->close();
    }

}
