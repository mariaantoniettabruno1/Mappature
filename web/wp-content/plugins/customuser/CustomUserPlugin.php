<?php

/*
Plugin Name: Custom user plugin
Plugin URI:
Description:
Version: 0.1
Author: MG3
Author URI:

*/


add_action( 'user_new_form', 'extra_user_fields');
add_action( 'edit_user_profile', 'extra_user_fields' );
add_action( 'show_user_profile', 'extra_user_fields' );



function extra_user_fields( $user ) {
    $user_id = $user->ID;
    ?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.0.js"></script>
    <h3>Job information</h3>
    <table class="form-table">
        <tr>
            <td>Settore</td>
            <td><input type="text" name="Settore" >
            </td>
        </tr>
        <tr>
            <td>Area</td>
            <td><input type="text" name="Area" >
            </td>
        </tr>
        <tr>
            <td>Ufficio</td>
            <td><input type="text" name="Ufficio" >
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

}
add_action( 'edit_user_profile_update', 'save_extra_user_field' );
add_action( 'user_register', 'save_extra_user_field' );

function save_extra_user_field( $user_id ) {
    if(!current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    update_user_meta($user_id, 'Settore', $_POST["Settore"]);
    update_user_meta($user_id, 'Area', $_POST["Area"]);
    update_user_meta($user_id, 'Ufficio', $_POST["Ufficio"]);

}

class User extends Connection {
private $username;
private $email;
private $name;
private $surname;

public function __construct($username,$email,$name,$surname){
    $this->username = $username;
    $this->email = $email;
    $this->name = $name;
    $this->surname = $surname;
}
public function getNewUser(){
    $db = new Connection();

}
public function getUsername(){
    return $this->username;
}
    public function getName(){
        return $this->name;
    }
    public function getEmail()
    {

    }

    public function getSurname(){
        return $this->surname;
    }
public function setName($name){
    if(is_string($name) && strlen($name)>1){
        $this->name = $name;
    }
    else return 'Not a valid name';
}
    public function setUsername($username){
        if(is_string($username) && strlen($username)>1){
            $this->username = $username;
        }
        else return 'Not a valid username';
    }
    public function setEmail($email){
        if(is_string($email) && strlen($email)>1){
            $this->email = $email;
        }
        else return 'Not a valid email';
    }
    public function setSurname($surname){
        if(is_string($surname) && strlen($surname)>1){
            $this->surname = $surname;
        }
        else return 'Not a valid surname';
    }
}
class Connection{
private $url;
private $username;
private $password;
private $dbname;

protected function connect(){
    $this->url = "http://vps1.mg3.srl/phpmyadmin/";
    $this->username ="c1demomg3" ;
    $this->password = "mxnCouMD!6M8";
    $this->dbname = "c1kanboard";
    $conn = new mysqli($this->url, $this->username, $this->password, $this->dbname);
   /* if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }*/
    return $conn;
}
}
?>


