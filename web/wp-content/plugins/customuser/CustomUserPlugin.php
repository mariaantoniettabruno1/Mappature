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
                <option value="Dirigente"> Dirigente</option>
                <option value="PO"> PO</option>
                <option value="Dipendente"> Dipendente</option>
            </select>

            <script>
                if (localStorage.getItem('ruolo') === null) {
                    localStorage.setItem('ruolo', "Dirigente");
                    document.write("Dirigente");
                }
                else {
                    if (localStorage.getItem('ruolo') === "Dirigente") {
                        document.write("Dirigente");
                    } else if (localStorage.getItem('ruolo') === "PO") {
                        document.write("PO");
                    } else if (localStorage.getItem('ruolo') === "Dipendente") {
                        document.write("Dipendente");
                    }
                $(document).ready(function(){
                    $('#ruolo').change(function(){
                        localStorage.setItem('ruolo', $(this).val());
                        $('#ruolo').value(localStorage.getItem('ruolo'));
                    });
                });
            </script>
            <br>
            <form>
                <br>
                <p> Attivo: </p>
                <input type="radio" name="choice" id="choice" value="si">Si<br>
                <input type="radio" name="choice" id="choice" value="no">No<br>
                <script>
                    $(document).ready(function () {
                        var radios = document.getElementsByName("choice");
                        var val = localStorage.getItem('choice');
                        for (var i = 0; i < radios.length; i++) {
                            if (radios[i].value == val) {
                                radios[i].checked = true;
                            }
                        }
                        $('input[name="choice"]').on('change', function () {
                            localStorage.setItem('choice', $(this).val());

                        });
                    });

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
include_once 'classes/OrgChartProcess.php';
include_once 'classes/Area.php';
include_once 'classes/Servizio.php';
include_once 'classes/Ufficio.php';
include_once 'shortcodes/SCOrgChartProcess.php';
include_once 'shortcodes/userMetaData.php';
include_once 'classes/SubtaskAttivita.php';
include_once 'shortcodes/shortcodeOrgChartView.php';
include_once 'classes/User.php';
include_once 'shortcodes/Prova.php';
require_once 'common.php';

add_action('edit_user_profile_update', 'save_extra_user_field');
add_action('user_register', 'save_extra_user_field');

function save_extra_user_field($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    update_user_meta($user_id, 'ruolo', $_POST["ruolo"]);
    update_user_meta($user_id, 'choice', $_POST["choice"]);


}

add_action('profile_update', 'on_profile_update');


function on_profile_update($user_id)
{
    $user_data = array(get_userdata($user_id));
    $user_meta = array(get_user_meta($user_id));

    if (isset($user_data[0]->data) && isset($user_meta[0])) {
        $user = new User();
        $user->setEmail($user_data[0]->data->user_email);
        $first_and_second_name = $user_meta[0]['first_name'][0] . ' ' . $user_meta[0]['last_name'][0];
        $user->setName($first_and_second_name);
        $user->setUsername($user_data[0]->data->user_login);
        $user->setPassword($user_data[0]->data->user_pass);
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
    $area = new Area();
    $servizio = new Servizio();
    $ufficio = new Ufficio();
    $area->setArea($user_meta[0]['area'][0]);
    $area->setUserArea($idKanboard);
    $servizio->setServizio($user_meta[0]['servizio'][0]);
    $servizio->setUserServizio($idKanboard);
    $ufficio->setUfficio($user_meta[0]['ufficio'][0]);
    $ufficio->setUserUfficio($idKanboard);


}

add_action('delete_user', 'my_delete_user');
function my_delete_user($user_id)
{
    $user_meta = array(get_user_meta($user_id));
    $user = new User();
    $id_kan = $user_meta[0]['id_kanboard'][0];
    $user->deleteUser($id_kan);
}

add_action('pmxi_saved_post', 'on_saved_user');
function on_saved_user($post_id, $xml_node, $is_update)
{
    $user_data = array(get_userdata($post_id));
    $user_meta = array(get_user_meta($post_id));

    if (isset($user_data[0]->data) && isset($user_meta[0])) {
        $user = new User();
        $user->setEmail($user_data[0]->data->user_email);
        $first_and_second_name = $user_meta[0]['first_name'][0] . ' ' . $user_meta[0]['last_name'][0];
        $user->setName($first_and_second_name);
        $user->setUsername($user_data[0]->data->user_login);
        $user->setPassword($user_data[0]->data->user_pass);
        $user->setIdKanboard($user_meta[0]['id_kanboard'][0]);
        $idKanboard = $user->getIdKanboard();
        if ($idKanboard != NULL) {
            $user->updateUser();

        } else {
            $user->createUser();
            $idKanboard = $user->getIdKanboard();
            update_user_meta($post_id, 'id_kanboard', $idKanboard);

        }

    }
}


?>


