<?php
include_once 'Connection.php';
include_once 'ConnectionSarala.php';

function visualize_fase()
{
    $entry_gforms = GFAPI::get_entries(23)[0];

    /* echo "<pre>";
     print_r(GFAPI::get_entries(23));
     echo "</pre>";*/

    $fase = new Fase();
    $fase->setTitle($entry_gforms[1]);
    $fase->setNameProcedure($entry_gforms[3]);
    $fase->setNameProcess($entry_gforms[2]);
    $fase->setIdForm($entry_gforms['form_id']);
    $fase->setId($entry_gforms['id']);
    foreach ($entry_gforms as $key => $value) {
        $pattern = "[^9.]";
        if (preg_match($pattern, $key) && $value) {
            $fase->addUser($value);
        }
    }
    $fase->createFase();

}

add_shortcode('post_fase', 'visualize_fase');

function update_fase()
{
    $entry_gforms = GFAPI::get_entries(43);
    $fase = new Fase();
    $id_current_form = $entry_gforms[0]['id'];
    $results_fase = Form::getForm($id_current_form);
    $fase->setTitle($results_fase[1]);

    $entry = array('1' => $results_fase[1], '2' => $results_fase[2], '3' => $results_fase[3], '4' => $results_fase[4], '5' => $results_fase[5], '6' => $results_fase[6]);
    $entry_gforms = GFAPI::get_entries(23);
    $id_current_form = $entry_gforms[0]['id'];
    $fase->setOldTitle($entry_gforms[0][1]);
    $fase->update();
    $result = GFAPI::update_entry($entry, $id_current_form);

}

add_shortcode('post_updatefase', 'update_fase');

function delete_fase()
{
    $entry_gforms = GFAPI::get_entries(23);
    $id_current_form = $entry_gforms[0]['id'];
    $fase = new Fase();
    $fase->setTitle($entry_gforms[0][1]);
    $fase->delete();
    $result = GFAPI::delete_entry($id_current_form);
}

add_shortcode('post_deletefase', 'delete_fase');

class Fase
{
    private $id;
    private $title;
    private $user_id;
    private $users;
    private $id_procedure;
    private $name_procedure;
    private $name_process;
    private $id_process;
    private $id_form;
    private $old_title;

    public function __construct()
    {
        $this->users = [];
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

    public function insertDataFaseSarala()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_fasi (id_fase,id_form,id_processo,id_procedimento) VALUES(?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iiii", $this->id, $this->id_form, $this->id_process, $this->id_procedure);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function createFase()
    {
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
        $this->title = $this->title . $a;
        $sql = "INSERT INTO subtasks (title,task_id, user_id) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($this->users as $userId) {
            $stmt->bind_param("sii", $this->title, $this->id_procedure, $userId);
            $res = $stmt->execute();
        }

        $this->insertDataFaseSarala();
        $mysqli->close();

    }

    public function update()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $dbTitle = $this->getDbTitle($this->title);
        $dbOldTitle = $this->getDbTitle($this->old_title);

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
        $dbTitle = $this->getDbTitle($this->title);
        $sql = "DELETE FROM subtasks WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $dbTitle);
        $res = $stmt->execute();
        $mysqli->close();
    }

    private function getDbTitle($title)
    {
        return $title . " - fase";
    }

    /**
     * @return mixed
     */
    public function getOldTitle()
    {
        return $this->old_title;
    }

    /**
     * @param mixed $old_title
     */
    public function setOldTitle($old_title)
    {
        $this->old_title = $old_title;
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

    }


}