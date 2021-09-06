<?php

/*
Plugin Name: Custom User Plugin
Plugin URI:
Description:
Version: 0.1
Author: MG3
Author URI:

*/

if (!function_exists('write_log')) {

    function write_log($log)
    {

        if (is_array($log) || is_object($log)) {
            error_log(print_r($log, true));
        } else {
            error_log($log);
        }
    }


}

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
            <td>Settore</td>
            <td><input type="text" name="Settore">
            </td>
        </tr>
        <tr>
            <td>Area</td>
            <td><input type="text" name="Area">
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
        $('input[name=Settore]').val('<?php echo get_the_author_meta('Settore', $user->ID); ?>');
        $('input[name=Area]').val('<?php echo get_the_author_meta('Area', $user->ID); ?>');
        $('input[name=Ufficio]').val('<?php echo get_the_author_meta('Ufficio', $user->ID); ?>');

    </script>
    <pre>
        <?php
        $data = array(get_userdata($user->ID));
        print_r($data);
        $user_meta = array(get_user_meta($user->ID));
        print_r($user_meta);
        ?>
    </pre>


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
    update_user_meta($user_id, 'Area', $_POST["Area"]);
    update_user_meta($user_id, 'Ufficio', $_POST["Ufficio"]);


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
        print_r($this->idKanboard);

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


?>


