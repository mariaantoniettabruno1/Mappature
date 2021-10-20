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
    <br class="container">
    <td>Settore</td>
    <div>
        <?php

        $settore = new Settore();
        $results_settore = $settore->selectSettore();
        ?>
        <select name='settore' id='settore'>
            <?php if ($user->ID !== NULL) {
                $user_meta = get_user_meta($user->ID);
                $settore = $user_meta['settore'][0];
            } else {
                $settore = '';
            }
            foreach ($results_settore as $result): ?>
                <option <?php if ($result === $settore) echo 'selected' ?>
                        value='<?= $result ?>'> <?= $result ?></option>
            <?php endforeach; ?>
        </select>


    </div>

    </br>
    <br class="container" id="id_servizio">
    <td>Servizio</td>
    <div>
        <?php
        $servizio = new Servizio();
        $results_servizio = $servizio->selectServizio();
        ?>
        <select name='servizio' id='servizio'>
            <?php if ($user_meta['servizio'][0] !== NULL) {
                $servizio = $user_meta['servizio'][0];
            } else {
                $servizio = '';
            }foreach ($results_servizio as $result): ?>
                <option <?php if ($result === $servizio) echo 'selected' ?>
                        value='<?= $result ?>'> <?= $result ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <br class="container">
    <td>Ufficio</td>
    <div>
        <?php
        $ufficio = new Ufficio();
        $results_ufficio = $ufficio->selectUfficio();
        ?>
        <select name='ufficio' id='ufficio'>
            <?php if ($user_meta['ufficio'][0] !== NULL) {
                $ufficio = $user_meta['ufficio'][0];
            } else {
                $ufficio = '';
            }foreach ($results_ufficio as $result): ?>
                <option <?php if ($result === $ufficio) echo 'selected' ?>
                        value='<?= $result ?>'> <?= $result ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <br>
    <tr>
        <td>Ruolo</td>
        <td><input type="text" name="Ruolo">
            <script>
                $('input').addClass('regular-text');
                $('input[name=Ruolo]').val('<?php echo get_the_author_meta('Ruolo', $user->ID); ?>');
            </script>
        </td>
    </tr>
    </div>
    </body>


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
    update_user_meta($user_id, 'settore', $_POST["settore"]);
    update_user_meta($user_id, 'servizio', $_POST["servizio"]);
    update_user_meta($user_id, 'ufficio', $_POST["ufficio"]);


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
        $user->setValueSettore($user_meta[0]['settore'][0]);
        $user->setValueServizio($user_meta[0]['servizio'][0]);
        $user->setValueUfficio($user_meta[0]['ufficio'][0]);
        $user->setIdKanboard($user_meta[0]['id_kanboard'][0]);
        $idKanboard = $user->getIdKanboard();
        if ($idKanboard != NULL) {
            $user->updateUser();
        } else {
            $user->createUser();
            $idKanboard = $user->getIdKanboard();
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
include_once 'includes/ConnectionSarala.php';
include_once 'includes/Processo.php';
include_once 'includes/Procedimento.php';
include_once 'includes/Fase.php';
include_once 'includes/Atto.php';
class User
{
    private $username;
    private $email;
    private $name;
    private $idKanboard;
    private $settore = "settore";
    private $servizio = "servizio";
    private $ufficio = "ufficio";
    private $value_settore;
    private $value_servizio;
    private $value_ufficio;

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


    public function getValueSettore()
    {
        return $this->value_settore;
    }


    public function setValueSettore($value_settore)
    {
        $this->value_settore = $value_settore;
    }

    public function getValueServizio()
    {
        return $this->value_servizio;
    }

    public function setValueServizio($value_servizio)
    {
        $this->value_servizio = $value_servizio;
    }

    public function getValueUfficio()
    {
        return $this->value_ufficio;
    }


    public function setValueUfficio($value_ufficio)
    {
        $this->value_ufficio = $value_ufficio;
    }


    public function updateUser()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE users SET name=?, email=? WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssi", $this->name, $this->email, $this->idKanboard);
        $res = $stmt->execute();
        $sql = "UPDATE user_has_metadata SET  value=? WHERE user_id=? AND name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $this->value_settore, $this->idKanboard, $this->settore);
        $res = $stmt->execute();
        $sql = "UPDATE user_has_metadata SET value=?  WHERE user_id=? AND name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $this->value_servizio, $this->idKanboard, $this->servizio);
        $res = $stmt->execute();
        $sql = "UPDATE user_has_metadata SET  value=?  WHERE user_id=? AND name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $this->value_ufficio, $this->idKanboard, $this->ufficio);
        $res = $stmt->execute();
        $mysqli->close();


    }

    public function createUser()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
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
        $sql = "INSERT INTO user_has_metadata (user_id,name,value) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iss", $this->idKanboard, $this->settore, $this->value_settore);
        $res = $stmt->execute();
        $sql = "INSERT INTO user_has_metadata (user_id,name,value) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iss", $this->idKanboard, $this->servizio, $this->value_servizio);
        $res = $stmt->execute();
        $sql = "INSERT INTO user_has_metadata (user_id,name,value) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iss", $this->idKanboard, $this->ufficio, $this->value_ufficio);
        $res = $stmt->execute();
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


?>


