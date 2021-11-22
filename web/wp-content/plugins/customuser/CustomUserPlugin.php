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
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <br>
    <h3>Job information</h3>
    </br>
    <tr>
        <td>Ruolo</td>
        <div>
            <select name='ruolo' id='ruolo'>
                <option> Dirigente</option>
                <option> PO</option>
                <option> Dipendente</option>
            </select>

            <script>
                $(function () {
                    var genderValue = localStorage.getItem("ruoloValue");
                    if (genderValue != null) {
                        $("select[name=ruolo]").val(genderValue);
                    }

                    $("select[name=ruolo]").on("change", function () {
                        localStorage.setItem("ruoloValue", $(this).val());
                    });
                })

            </script>
        </div>
        </td>
    </tr>
    </div>
    </body>


    <?php

}

include_once 'includes/Connection.php';
include_once 'includes/ConnectionSarala.php';
include_once 'classes/Processo.php';
include_once 'classes/Procedimento.php';
include_once 'classes/Fase.php';
include_once 'classes/Attività.php';
include_once 'classes/OrgChartProcess.php';
include_once 'classes/Area.php';
include_once 'classes/Servizio.php';
include_once 'classes/Ufficio.php';
include_once 'shortcodes/SCOrgChartProcess.php';

add_action('edit_user_profile_update', 'save_extra_user_field');
add_action('user_register', 'save_extra_user_field');

function save_extra_user_field($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    update_user_meta($user_id, 'ruolo', $_POST["ruolo"]);
    /* echo "<pre>";
     print_r(get_user_meta($user_id));
     throw new Exception();
     echo "</pre>";*/


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
        $user->setPassword($user_data[0]->data->user_pass);
        $user->setIdKanboard($user_meta[0]['id_kanboard'][0]);
        $idKanboard = $user->getIdKanboard();
        if ($idKanboard != NULL) {
            $user->updateUser();
        } else {
            $user->createUser();
            $idKanboard = $user->getIdKanboard();
            print_r($idKanboard);
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

function add_user_metadata()
{
    $entry_gforms = GFAPI::get_entries(52)[0];
    $area = new Area();
    $servizio = new Servizio();
    $ufficio = new Ufficio();
    $array_servizio = array();
    $array_ufficio = array();
    $area->setArea($entry_gforms[3]);
    $old_value_servizio = '';
    $old_value_ufficio = '';
    foreach ($entry_gforms as $key => $value) {
        $pattern = "[^1.]";
        if (preg_match($pattern, $key) && $value && $value != '') {
            $wp_userid = $value;
            update_user_meta($value, 'area', $area->getArea());
            $user_meta = array(get_user_meta($value));
            $area->setUserArea($user_meta[0]['id_kanboard'][0]);
            foreach ($entry_gforms as $key => $value) {
                $pattern = "[^6.]";
                if (preg_match($pattern, $key) && $value && $value != '') {
                    if ($old_value_servizio != $value)
                        array_push($array_servizio, $value);
                    $old_value_servizio = $value;
                }
            }

            foreach ($entry_gforms as $key => $value) {
                $pattern = "[^7.]";
                if (preg_match($pattern, $key) && $value && $value != '') {
                    if ($old_value_ufficio != $value) {
                        array_push($array_ufficio, $value);
                    }
                    $old_value_ufficio = $value;

                }
            }

            update_user_meta($wp_userid, 'servizio', implode(",", $array_servizio));
            $user_meta = array(get_user_meta($wp_userid));
            array_push($user_meta[0]['servizio'], $array_servizio);
            //delete_user_meta($wp_userid,['servizio']);
            $servizio->setServizio(implode(",", $array_servizio));
            $servizio->setUserServizio($user_meta[0]['id_kanboard'][0]);

            update_user_meta($wp_userid, 'ufficio', implode(",", $array_ufficio));
            $user_meta = array(get_user_meta($wp_userid));
            array_push($user_meta[0]['ufficio'], $array_ufficio);
            $ufficio->setUfficio(implode(",", $array_ufficio));
            $ufficio->setUserUfficio($user_meta[0]['id_kanboard'][0]);


        }

    }


}

add_shortcode('post_addusermetadata', 'add_user_metadata');

function edit_user_metadata()
{
    $entry_gforms = GFAPI::get_entries(55)[0];
    $area = new Area();
    $servizio = new Servizio();
    $ufficio = new Ufficio();
    $array_servizio = array();
    $array_ufficio = array();
    $area->setArea($entry_gforms[3]);
    $old_value_servizio = '';
    $old_value_ufficio = '';
    foreach ($entry_gforms as $key => $value) {
        $pattern = "[^1.]";
        if (preg_match($pattern, $key) && $value && $value != '') {
            $wp_userid = $value;
            update_user_meta($value, 'area', $area->getArea());
            $user_meta = array(get_user_meta($value));
            $area->editUserArea($user_meta[0]['id_kanboard'][0]);
            foreach ($entry_gforms as $key => $value) {
                $pattern = "[^4.]";
                if (preg_match($pattern, $key) && $value && $value != '') {
                    if ($old_value_servizio != $value)
                        array_push($array_servizio, $value);
                    $old_value_servizio = $value;
                }
            }
            foreach ($entry_gforms as $key => $value) {
                $pattern = "[^6.]";
                if (preg_match($pattern, $key) && $value && $value != '') {
                    if ($old_value_ufficio != $value) {
                        array_push($array_ufficio, $value);

                    }
                    $old_value_ufficio = $value;

                }
            }

            update_user_meta($wp_userid, 'servizio', $array_servizio);
            $user_meta = array(get_user_meta($wp_userid));
            array_push($user_meta[0]['servizio'], $array_servizio);
            //delete_user_meta($wp_userid,['servizio']);
            $servizio->setServizio(implode(",", $array_servizio));
            $servizio->editUserServizio($user_meta[0]['id_kanboard'][0]);

            update_user_meta($wp_userid, 'ufficio', $array_ufficio);
            $user_meta = array(get_user_meta($wp_userid));
            array_push($user_meta[0]['ufficio'], $array_ufficio);
            //delete_user_meta($wp_userid,['ufficio']);
            $ufficio->setUfficio(implode(",", $array_ufficio));
            $ufficio->editUserUfficio($user_meta[0]['id_kanboard'][0]);

        }

    }

    if ($user_meta[0]['Ruolo'][0] == 'Dirigente') {
        $ownerId = $user_meta[0]['id_kanboard'][0];
        $entry_gforms_processo = GFAPI::get_entries(1);
        for ($i = 0; $i < sizeof($entry_gforms_processo); $i++) {
            foreach ($entry_gforms_processo[$i] as $key => $value) {
                $pattern = "[^9.]";
                if ($entry_gforms_processo[$i][2] == $area->getArea()) {

                    if (preg_match($pattern, $key) && $value && $value != '') {
                       $id_processo = Processo::aggiornaProcesso($ownerId, $value);
                        Procedimento::aggiornaOwnerIdProcedimento($ownerId,$id_processo);
                    }
                }
            }
        }
    }

    elseif($user_meta[0]['Ruolo'][0] == 'PO'){
        $creatorId = $user_meta[0]['id_kanboard'][0];
        $entry_gforms_procedimento = GFAPI::get_entries(50);
        for ($i = 0; $i < sizeof($entry_gforms_procedimento); $i++) {
            foreach ($entry_gforms_procedimento[$i] as $key => $value) {
                $pattern = "[^22.]";
                if ($entry_gforms_procedimento[$i][18] == $area->getArea()) {
                    print_r("Sono nel primo if");
                   if (preg_match($pattern, $key) && $value && $value != '') {
                       print_r("Sono nel secondo if");
                        Procedimento::aggiornaProcedimento($creatorId, $value);
                    }
                }
            }
        }
    }



}

add_shortcode('post_editusermetadata', 'edit_user_metadata');


class User
{
    private $username;
    private $email;
    private $name;
    private $idKanboard;
    private $password;

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }


    public function setIdKanboard($idKanboard)
    {
        $this->idKanboard = (int)$idKanboard;
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
        $sql = "UPDATE users SET name=?, email=?, password=? WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssi", $this->name, $this->email, $this->password, $this->idKanboard);
        $res = $stmt->execute();
//
//        $sql = "UPDATE user_has_metadata SET value=?  WHERE user_id=? AND name=?";
//        $stmt = $mysqli->prepare($sql);
//        $stmt->bind_param("sis", $this->value_servizio, $this->idKanboard, $this->servizio);
//        $res = $stmt->execute();
//        $sql = "UPDATE user_has_metadata SET  value=?  WHERE user_id=? AND name=?";
//        $stmt = $mysqli->prepare($sql);
//        $stmt->bind_param("sis", $this->value_ufficio, $this->idKanboard, $this->ufficio);
//        $res = $stmt->execute();
        $mysqli->close();
    }

    public function createUser()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO users (username,name,email,password) VALUES(?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssss", $this->username, $this->name, $this->email, $this->password);
        $res = $stmt->execute();
        $sql = "SELECT id FROM users WHERE username=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->username);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdKanboard($result['id']);
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


