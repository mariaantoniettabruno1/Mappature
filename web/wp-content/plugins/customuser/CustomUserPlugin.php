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
    <h3>Job information</h3>
    <table class="form-table">
        <tr>
            <td>Ente</td>
            <td><input type="text" name="Ente">
            </td>
        </tr>
        <tr>
            <td>Settore</td>
            <td><input type="text" name="Settore">
            </td>
        </tr>
        <tr>
            <td>Servizio</td>
            <td><input type="text" name="Servizio">
            </td>
        </tr>
        <tr>
            <td>Ufficio</td>
            <td><input type="text" name="Ufficio">
            </td>
        </tr>
    </table>
    <script type="text/javascript">

        $('input').addClass('regular-text');
        $('input[name=Ente]').val('<?php echo get_the_author_meta('Ente', $user->ID); ?>');
        $('input[name=Settore]').val('<?php echo get_the_author_meta('Settore', $user->ID); ?>');
        $('input[name=Servizio]').val('<?php echo get_the_author_meta('Servizio', $user->ID); ?>');
        $('input[name=Ufficio]').val('<?php echo get_the_author_meta('Ufficio', $user->ID); ?>');


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
    update_user_meta($user_id, 'Settore', $_POST["Settore"]);
    update_user_meta($user_id, 'Servizio', $_POST["Servizio"]);    update_user_meta($user_id, 'Ufficio', $_POST["Ufficio"]);
    update_user_meta($user_id, 'Ente', $_POST["Ente"]);


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

class User
{
    private $username;
    private $email;
    private $name;
    private $idKanboard;

    public function __construct()
    {

    }

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


