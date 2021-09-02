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
    $user_id = $user->ID;
    $user_email = $user->user_email;

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

    <?php
    $user_data = get_userdata($user_id);

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
    $data = array(get_userdata($user_id));
    $user_meta = array(get_user_meta($user_id));
    $user = new User(isset($data["user_login"]), isset($user_meta["first_name"]), isset($data["user_email"]));
    $user->setName(isset($user_meta["first_name"]));
    $user->updateUser();
}

include_once 'includes/Connection.php';
class User
{
    private $username;
    private $email;
    private $name;


    public function __construct($username, $name, $email)
    {
        $this->username = $username;
        $this->email = $email;
        $this->name = $name;

    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getName($name)
    {
        if (name_exist($this, $name)) {
            return $this->name;
        }

    }

    public function updateUser()
    {
        $conn = new Connection();
        $conn->connect();
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        //create a template
        $sql = "UPDATE users SET name =? WHERE email=?";
        //create a prepared statement
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            echo "Record updated successfully";
            //bind parameters to placeholders
            mysqli_stmt_bind_param($stmt,"ss",$name,$email);
            //run parameters inside database
            mysqli_stmt_execute($stmt);
            $this->closeConnection();
        } else {
            echo "Error updating record: ";
        }

    }
}



?>


