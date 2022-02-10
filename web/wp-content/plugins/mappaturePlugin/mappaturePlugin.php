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
require_once(plugin_dir_path(__FILE__) . 'common.php');
require_once(plugin_dir_path(__FILE__) . 'includes/Connection.php');
require_once(plugin_dir_path(__FILE__) . 'includes/ConnectionSarala.php');
require_once(plugin_dir_path(__FILE__) . 'classes/Area.php');
require_once(plugin_dir_path(__FILE__) . 'classes/Servizio.php');
require_once(plugin_dir_path(__FILE__) . 'classes/Ufficio.php');
require_once(plugin_dir_path(__FILE__) . 'classes/User.php');
require_once(plugin_dir_path(__FILE__) . 'classes/Processo.php');
require_once(plugin_dir_path(__FILE__) . 'classes/Procedimento.php');
require_once(plugin_dir_path(__FILE__) . 'classes/Fase.php');
require_once(plugin_dir_path(__FILE__) . 'classes/Attivita.php');
require_once(plugin_dir_path(__FILE__) . 'classes/GetterIdUsers.php');
require_once(plugin_dir_path(__FILE__) . 'classes/UpdateThingsByRuolo.php');
require_once(plugin_dir_path(__FILE__) . 'shortcodes/ShortCodesProcesso.php');
require_once(plugin_dir_path(__FILE__) . 'shortcodes/ShortCodesProcedimento.php');
require_once(plugin_dir_path(__FILE__) . 'shortcodes/ShortCodesDipendenteProcedimento.php');
require_once(plugin_dir_path(__FILE__) . 'shortcodes/ShortCodesFase.php');
require_once(plugin_dir_path(__FILE__) . 'shortcodes/UserMetaDataOrgchart.php');
require_once(plugin_dir_path(__FILE__) . 'shortcodes/ShortCodesAttivita.php');
require_once(plugin_dir_path(__FILE__) . 'mappatureOrgChart/OrgChartImpiegati.php');
require_once(plugin_dir_path(__FILE__) . 'mappatureOrgChart/OrgChartProcessi.php');



/**
 * Aggiungo librerie javascript a wordpress
 */


function custom_scripts_method()
{
    wp_register_script('customscripts', MappatureCommon::get_base_url() . '/libs/jquery.min.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('customscripts');
}

/**
 * Action per aggiungere extra user field inerenti al ruolo comunale e allo stato 'attivo' dell'utente
 */


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
                    <option value="Dirigente" <?php if ($ruolo == 'Dirigente') echo ' selected="selected"'; ?>>
                        Dirigente
                    </option>
                    <option value="PO" <?php if ($ruolo == 'PO') echo ' selected="selected"'; ?>> PO</option>
                    <option value="Dipendente" <?php if ($ruolo == 'Dipendente') echo ' selected="selected"'; ?>>
                        Dipendente
                    </option>


                </select>
            </td>
        </tr>
        <tr>
            <th>Attivo</th>
            <td>
                <?php if ($user->ID !== NULL) {

                    $user_meta = get_user_meta($user->ID);
                    if (empty($user_meta['attivo']) || empty($user_meta['attivo'][0])) {
                        $attivo = '';
                        update_user_meta($user->ID, 'attivo', $attivo);

                    } else {
                        $attivo = $user_meta['attivo'][0];
                    }


                } else {
                    $attivo = '';
                }

                ?>

                <input type="radio" name="attivo" value="si" <?php if ($attivo == 'si') echo ' checked="checked"'; ?>>Si<br>
                <input type="radio" name="attivo" value="no" <?php if ($attivo == 'no') echo ' checked="checked"'; ?>>No<br>

            </td>
        </tr>
        </tbody>
    </table>
    <?php

}

/**
 * Action per aggiornare i cambiamenti effettuati dall'utente sugli extra user fields aggiunti
 */

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

/**
 * Action per l'aggiornamento o la creazione di un utente con i suoi rispettivi dati e metadati
 */

add_action('edit_user_profile_update', 'save_extra_user_field');
add_action('user_register', 'save_extra_user_field');
add_action('profile_update', 'on_profile_update');

function on_profile_update($user_id)
{
    KBSync::updateUser($user_id);


}
add_action('pmxi_saved_post', 'on_saved_user');
function on_saved_user($post_id)
{
    KBSync::importUser($post_id);
}
add_action('delete_user', 'my_delete_user');
function my_delete_user($user_id)
{
    KBSync::deleteUser($user_id);
}
/**
 * Action per l'inizializzazione di tutte le function collegate agli shortcode del plugin
 */


add_action('init', 'shortcodes_init');

function shortcodes_init()
{
    add_shortcode('post_addusermetadata', 'call_add_user_metadata');
    add_shortcode('post_editusermetadata', 'call_edit_user_metadata');
    add_shortcode('post_processo', 'call_create_processo');
    add_shortcode('post_editprocesso', 'call_edit_processo');
    add_shortcode('post_deleteprocesso', 'call_delete_processo');
    add_shortcode('post_procedimento', 'call_create_procedimento');
    add_shortcode('post_procedimentopostuma', 'call_create_procedimento_postuma');
    add_shortcode('post_editprocedimento', 'call_edit_procedimento');
    add_shortcode('post_deleteprocedimento', 'call_delete_procedimento');
    add_shortcode('post_assigndipendente', 'call_assign_dipendente');
    add_shortcode('post_editassigndipendente', 'call_edit_assign_dipendente');
    add_shortcode('post_fase', 'call_create_fase');
    add_shortcode('post_editfase', 'call_edit_fase');
    add_shortcode('post_deletefase', 'call_delete_fase');
    add_shortcode('post_create_attivita', 'call_create_attivita');
    add_shortcode('post_editattivita', 'call_edit_attivita');
    add_shortcode('post_deleteattivita', 'call_delete_attivita');
    add_shortcode("post_orgchartdipendenti", "call_orgchart_dipendenti");
    add_shortcode("post_orgchartprocessi", "call_orgchart_processi");
    

}

function call_add_user_metadata()
{
    \MappaturePlugin\UserMetaDataOrgchart::add_user_metadata();
}

function call_edit_user_metadata()
{
    \MappaturePlugin\UserMetaDataOrgchart::edit_user_metadata();
}

function call_create_processo()
{
    \MappaturePlugin\ShortCodesProcesso::create_processo();
}
function call_edit_processo()
{
    \MappaturePlugin\ShortCodesProcesso::edit_processo();
}

function call_delete_processo()
{
    \MappaturePlugin\ShortCodesProcesso::delete_processo();
}

function call_create_procedimento()
{
    \MappaturePlugin\ShortCodesProcedimento::create_procedimento();
}

function call_create_procedimento_postuma()
{
    \MappaturePlugin\ShortCodesProcedimento::create_procedimento_postuma();
}
function call_edit_procedimento()
{
    \MappaturePlugin\ShortCodesProcedimento::edit_procedimento();
}

function call_delete_procedimento()
{
    \MappaturePlugin\ShortCodesProcedimento::delete_procedimento();
}
function call_assign_dipendente()
{
    \MappaturePlugin\ShortCodesDipendenteProcedimento::assign_dipendente();
}
function call_edit_assign_dipendente()
{
    \MappaturePlugin\ShortCodesDipendenteProcedimento::edit_assign_dipendente();
}
function call_create_fase(){
    \MappaturePlugin\ShortCodesFase::create_fase();
}

function call_edit_fase(){
    \MappaturePlugin\ShortCodesFase::edit_fase();
}
function call_delete_fase(){
    \MappaturePlugin\ShortCodesFase::delete_fase();
}
function call_create_attivita(){
    \MappaturePlugin\ShortCodesAttivita::create_attivita();
}


function call_edit_attivita(){
    \MappaturePlugin\ShortCodesAttivita::edit_attivita();
}
function call_delete_attivita(){
    \MappaturePlugin\ShortCodesAttivita::delete_attivita();
}

function call_orgchart_dipendenti(){
    \MappaturePlugin\OrgChartImpiegati::orgchart_dipendenti();
}

function call_orgchart_processi(){
    \MappaturePlugin\OrgChartProcessi::orgchart_processi();
}