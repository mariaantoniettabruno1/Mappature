<?php

/***
 * Plugin Name: Mappature Plugin
 * Plugin URI:
 * Description:
 * Version: 0.1
 * Author: MG3
 * Author URI:
 */

use MappaturePlugin\KBSync;

require_once(plugin_dir_path(__FILE__) . 'kanboardSync/KanboardSync.php');
require_once(plugin_dir_path(__FILE__) . 'classes/User.php');
require_once(plugin_dir_path(__FILE__) . 'common.php');
require_once(plugin_dir_path(__FILE__) . 'includes/Connection.php');
require_once(plugin_dir_path(__FILE__) . 'includes/ConnectionSarala.php');
require_once(plugin_dir_path(__FILE__) . 'classes/Area.php');
require_once(plugin_dir_path(__FILE__) . 'classes/Servizio.php');
require_once(plugin_dir_path(__FILE__) . 'classes/Ufficio.php');

/**
 * Aggiungo librerie javascript a wordpress
 */


function custom_scripts_method()
{
    wp_register_script('customscripts', MappatureCommon::get_base_url() . '/libs/jquery.min.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('customscripts');
}

add_action('wp_enqueue_scripts', 'custom_scripts_method');

function extra_user_fields($user)
{
    ?>
    <h2>Job information</h2>
    <table class="form-table" role="presentation">
        <tbody>
        <tr>
            <th>Ruolo</th>
            <td>
                <select name='ruolo' id='ruolo'>
                    <?php if ($user->ID !== NULL) {
                        $user_meta = get_user_meta($user->ID);

                        $ruolo = $user_meta['Ruolo'][0];
                    } else {
                        $ruolo = '';
                    }
                    ?>
                    <option value="Dirigente" <?php if ($ruolo == 'Dirigente') echo ' selected="selected"'; ?>> Dirigente</option>
                    <option value="PO" <?php if ($ruolo == 'PO') echo ' selected="selected"'; ?>> PO</option>
                    <option value="Dipendente"  <?php if ($ruolo == 'Dipendente') echo ' selected="selected"'; ?>> Dipendente</option>


                </select>
            </td>
        </tr>
        <tr>
            <th>Attivo</th>
            <td>
                <?php
                $user_meta = get_user_meta($user->ID);
                $attivo = $user_meta['attivo'][0]; ?>
                <input type="radio" name="attivo" value="si" <?php if ($attivo == 'si') echo ' checked="checked"'; ?>>Si<br>
                <input type="radio" name="attivo" value="no" <?php if ($attivo == 'no') echo ' checked="checked"'; ?>>No<br>

            </td>
        </tr>
        </tbody>
    </table>
    <?php

}

add_action('user_new_form', 'extra_user_fields');
add_action('edit_user_profile', 'extra_user_fields');
add_action('show_user_profile', 'extra_user_fields');


function save_extra_user_field($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    update_user_meta($user_id, 'ruolo', $_POST["ruolo"]);
    if (isset($_POST['attivo'])) {
        update_user_meta($user_id, 'attivo', $_POST["attivo"]);
    }


}

add_action('edit_user_profile_update', 'save_extra_user_field');
add_action('user_register', 'save_extra_user_field');


function on_profile_update($user_id)
{

    KBSync::updateUser($user_id);

}

add_action('profile_update', 'on_profile_update');


