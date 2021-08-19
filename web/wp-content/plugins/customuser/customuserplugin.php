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
add_action( 'personal_options_update', 'save_extra_user_field' );
function save_extra_user_field( $user_id ) {
    if(!current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    update_user_meta($user_id, 'Settore', $_POST["Settore"]);
    update_user_meta($user_id, 'Area', $_POST["Area"]);
    update_user_meta($user_id, 'Ufficio', $_POST["Ufficio"]);
}

?>