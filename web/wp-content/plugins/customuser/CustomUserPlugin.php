<?php

/*
Plugin Name: Custom User Plugin
Plugin URI:
Description:
Version: 0.1
Author: MG3
Author URI:

*/

/*if (!function_exists('write_log')) {

    function write_log($log)
    {

        if (is_array($log) || is_object($log)) {
            error_log(print_r($log, true));
        } else {
            error_log($log);
        }
    }


}*/

add_action('user_new_form', 'extra_user_fields');
add_action('edit_user_profile', 'extra_user_fields');
add_action('show_user_profile', 'extra_user_fields');


function extra_user_fields($user)
{

    ?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.0.js"></script>
    <body>
    <h3>Job information</h3>
    <br class="container">
    <td>Settore</td>
    <div>
        <?php
        $settore = new Settore();
        $results_settore = $settore->selectSettore();
        echo "<select name= 'settore' >";
        foreach ($results_settore as $result) {
            echo "<option selected='selected' value='$result'> $result</option>";
            echo "<br>";
        }
        echo "</select>";
        ?>
    </div>

    <br class="container">
    <td>Servizio</td>
    <div>
        <?php
        $servizio = new Servizio();
        $results_servizio = $servizio->selectServizio();
        echo "<select name='servizio'>";
        foreach ($results_servizio as $result) {
            echo "<option selected='selected' value='$result'> $result</option>";
        }
        echo "</select>";
        ?>
    </div>
    <br class="container">
    <td>Ufficio</td>
    <div>
        <?php
        $ufficio = new Ufficio();
        $results_ufficio = $ufficio->selectUfficio();
        echo "<select name='ufficio'>";
        foreach ($results_ufficio as $result) {
            echo "<option selected='selected'  value='$result'> $result</option>";
        }
        echo "</select>";
        ?>
    </div>
    <br>
    <tr>
        <td>Ruolo</td>
        <td><input type="text" name="Ruolo">
        </td>
    </tr>
    </div>
    </body>
    <script type="text/javascript">
        $('input').addClass('regular-text');
        $('input[name=Ruolo]').val('<?php echo get_the_author_meta('Ruolo', $user->ID); ?>');
    </script>

    <?php

}

add_action('edit_user_profile_update', 'save_extra_user_field');
add_action('user_register', 'save_extra_user_field');

function save_extra_user_field($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    update_user_meta($user_id, 'Ruolo', $_POST["Ruolo"]);
    if(!empty($_POST['settore'])) {
        update_user_meta($user_id, 'Settore', $_POST['settore']);
    }
    else{
        echo "Error Post Settore";
    }
    if(!empty($_POST['servizio'])) {
        update_user_meta($user_id, 'Servizio', $_POST['servizio']);
    }
    else{
        echo "Error Post Servizio";
    }
    if(!empty($_POST['ufficio'])) {
        update_user_meta($user_id, 'Ufficio', $_POST['ufficio']);
    }
    else{
        echo "Error Post Ufficio";
    }

}

add_action('profile_update', 'on_profile_update');


function on_profile_update($user_id)
{
    $user_data = array(get_userdata($user_id));
    $user_meta = array(get_user_meta($user_id));

    if (isset($user_data[0]->data) && isset($user_meta[0])) {
        $user = new User();
        $user->setEmail($user_data[0]->data->user_email);
        $user->setName($user_meta[0]['first_name'][0]);
        $user->setUsername($user_data[0]->data->user_login);
        $user->setIdKanboard($user_meta[0]['id_kanboard'][0]);
        $user->updateUser();
        $idKanboard = $user->getIdKanboard();
        if ($idKanboard != NULL) {
            update_user_meta($user_id, 'id_kanboard', $idKanboard);
        }

        echo "<pre>";
        print_r($user_meta);
        echo "</pre>";
        throw  new Exception();
    }

}

add_action('delete_user', 'my_delete_user');
function my_delete_user($user_id)
{
    $user_meta = array(get_user_meta($user_id));
    $user = new User();
    $id_kan = $user_meta[0]['id_kanboard'][0];
    $user->deleteUser($id_kan);
}


add_action('admin_post_add_project', 'admin_add_project');
function admin_add_project()
{
    $project = new Project();
    $project->setProjectName("Prova Wordpress 2");
    $project->setIdUser(wp_get_current_user());
    $project->setUserRole('project manager');
    $project->updateProject();


}

include_once 'includes/Connection.php';
include_once 'includes/ConnectionSarala.php';
class User
{
    private $username;
    private $email;
    private $name;
    private $idKanboard;


    public function setIdKanboard($idKanboard)
    {
        $this->idKanboard = $idKanboard;
    }

    public function getIdKanboard()
    {
        return $this->idKanboard;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {

        return $this->username;

    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }


    public function getEmail()
    {
        return $this->email;

    }


    public function updateUser()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();


        if ($this->idKanboard != NULL) {
            $sql = "UPDATE users SET name=?, email=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssi", $this->name, $this->email, $this->idKanboard);
            $res = $stmt->execute();
        } else {
            $sql = "INSERT INTO users (username,name,email) VALUES(?,?,?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sss", $this->username, $this->name, $this->email);
            $res = $stmt->execute();
            $sql = "SELECT id FROM users WHERE username=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $this->username);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $user = $res->fetch_assoc();
            $this->setIdKanboard($user['id']);
        }
        $mysqli->close();

    }

    public function deleteUser($id_kan)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $this->setIdKanboard($id_kan);
        $sql = "DELETE  FROM users WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->idKanboard);
        $res = $stmt->execute();
        $mysqli->close();
    }


}


class Settore
{
    private $formidSettore = 17;

    public function __construct()
    {

    }

    public function getFormidSettore()
    {
        return $this->formidSettore;
    }


    public function setFormidSettore(int $formidSettore)
    {
        $this->formidSettore = $formidSettore;
    }

    public function selectSettore()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE form_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->formidSettore);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = array();
        foreach ($res as $lines) {
            array_push($result, $lines["meta_value"]);
        }
        $mysqli->close();
        return $result;

    }
}

class Servizio
{
    private $formidServizio = 21;
    private $metakeyServizio = 1;

    public function __construct()
    {

    }

    public function getFormidServizio()
    {
        return $this->formidServizio;
    }


    public function setFormidServizio(int $formidServizio)
    {
        $this->formidServizio = $formidServizio;
    }

    public function getMetakeyServizio(): int
    {
        return $this->metakeyServizio;
    }

    public function setMetakeyServizio(int $metakeyServizio)
    {
        $this->metakeyServizio = $metakeyServizio;
    }

    public function selectServizio()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE form_id=? and meta_key=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $this->formidServizio, $this->metakeyServizio);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = array();
        foreach ($res as $lines) {
            array_push($result, $lines["meta_value"]);
        }
        $mysqli->close();
        return $result;

    }
}

class Ufficio
{
    private $formidUfficio = 20;
    private $metakeyUfficio = 4;

    public function __construct()
    {

    }


    public function getFormidUfficio()
    {
        return $this->formidUfficio;
    }


    public function setFormidUfficio(int $formidUfficio)
    {
        $this->formidUfficio = $formidUfficio;
    }


    public function getMetakeyUfficio()
    {
        return $this->metakeyUfficio;
    }


    public function setMetakeyUfficio(int $metakeyUfficio)
    {
        $this->metakeyUfficio = $metakeyUfficio;
    }

    public function selectUfficio()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE form_id=? and meta_key=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $this->formidUfficio, $this->metakeyUfficio);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = array();
        foreach ($res as $lines) {
            array_push($result, $lines["meta_value"]);
        }
        $mysqli->close();
        return $result;

    }
}

class Project
{
    private $project_name;
    private $id_project;
    private $id_user;
    private $user_role;

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


    public function getProjectName()
    {
        return $this->project_name;
    }


    public function setProjectName($project_name)
    {
        $this->project_name = $project_name;
    }


    public function getIdProject()
    {
        return $this->id_project;
    }


    public function setIdProject($id_project)
    {
        $this->id_project = $id_project;
    }

    public function updateProject()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        if ($this->id_project != NULL) {
            $sql = "UPDATE projects SET name=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $this->project_name, $this->id_project);
            $res = $stmt->execute();
        } else {
            $sql = "INSERT INTO projects (name,owner_id) VALUES(?,?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $this->project_name, $this->id_user);
            $res = $stmt->execute();
            $sql = "SELECT id FROM projects WHERE name=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $this->project_name);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $project = $res->fetch_assoc();
            $this->setIdProject($project['id']);
            $sql = "INSERT INTO project_has_users (project_id,user_id,role) VALUES(?,?,?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("iis", $this->id_project, $this->id_user, $this->user_role);
            $res = $stmt->execute();

        }
        $mysqli->close();
    }

    public function deleteProject($idProject)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $this->setIdProject($idProject);
        $sql = "DELETE  FROM projects WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_project);
        $res = $stmt->execute();
        $mysqli->close();
    }
}

class Task
{
    private $id_task;
    private $title;
    private $description;
    private $date_creation;
    private $date_completed;

    public function __construct()
    {

    }


    public function getIdTask()
    {
        return $this->id_task;
    }


    public function setIdTask($id_task)
    {
        $this->id_task = $id_task;
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

    public function updateTask()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        if ($this->id_task != NULL) {
            $sql = "UPDATE tasks SET title=?,description=?,date_creation=?,date_completed=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssiii", $this->title, $this->description, $this->date_creation, $this->date_completed, $this->id_task);
            $res = $stmt->execute();
        } else {
            $sql = "INSERT INTO tasks (title,description,date_creation,date_completed) VALUES(?,?,?,?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssii", $this->title, $this->description, $this->date_creation, $this->date_completed);
            $res = $stmt->execute();
        }
        $mysqli->close();
    }

    public function deleteTask($idTask)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $this->setIdTask($idTask);
        $sql = "DELETE  FROM tasks WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_task);
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

?>


