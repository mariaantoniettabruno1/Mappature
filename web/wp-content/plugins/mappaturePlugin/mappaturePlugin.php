<?php

/***
Plugin Name: Mappature Plugin
Plugin URI:
Description:
Version: 0.1
Author: MG3
Author URI:

*/

use MappaturePlugin\KBSync;

require_once( plugin_dir_path( __FILE__ ) . 'kanboardSync/KanboardSync.php' );
require_once( plugin_dir_path( __FILE__ ) . 'classes/User.php' );
require_once( plugin_dir_path( __FILE__ ) . 'common.php' );

/**
 * Aggiungo librerie javascript a wordpress
*/



function custom_scripts_method()
{
    wp_register_script('customscripts', MappatureCommon::get_base_url().'/libs/jquery.min.js', array('jquery'), '1.0.0', true);
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
                    <option value="Dirigente"> Dirigente</option>
                    <option value="PO"> PO</option>
                    <option value="Dipendente"> Dipendente</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Attivo </th>
            <td>
                <input type="radio" name="attivo"  value="si">Si<br>
                <input type="radio" name="attivo"  value="no">No<br>
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
    update_user_meta($user_id, 'attivo', $_POST["attivo"]);


}

add_action('edit_user_profile_update', 'save_extra_user_field');
add_action('user_register', 'save_extra_user_field');




function on_profile_update($user_id)
{

KBSync::updateUser($user_id);

}

add_action('profile_update', 'on_profile_update');


