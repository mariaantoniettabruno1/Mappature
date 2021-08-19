<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**

 * Org_Chart_Admin Class

 *

 * @class Org_Chart_Admin

 * @package WordPress

 * @subpackage Org_Chart

 * @category Plugin

 * @author Gangesh Matta

 * @since 1.0.0

 */

final class Org_Chart_Admin
{

    /**

     * Org_Chart_Admin The single instance of Org_Chart_Admin.

     * @var     object

     * @access  private

     * @since     1.0

     */

    private static $_instance = null;

    /**

     * The string containing the dynamically generated hook token.

     * @var     string

     * @access  private

     * @since   1.0.0

     */

    private $_hook;

    /**

     * Constructor function.

     * @access  public

     * @since   1.0.0

     */

    public function __construct()
    {

        // Register the settings with WordPress.
        add_action('admin_init', array($this, 'register_settings'));

        // Register the settings screen within WordPress.
        add_action('admin_menu', array($this, 'register_settings_screen'));

        /* Display User Settings Fields for chart*/
        add_action('show_user_profile', array($this, 'user_interests_fields'));
        add_action('edit_user_profile', array($this, 'user_interests_fields'));
        add_action('user_new_form', array($this, 'user_interests_fields'));

        /* Save Use Custom Settings for chart*/
        add_action('personal_options_update', array($this, 'user_interests_fields_save'));
        add_action('edit_user_profile_update', array($this, 'user_interests_fields_save'));
        add_action('user_register', array($this, 'user_interests_fields_save'));

        /* Profile Image Settings fields for chart*/
        add_action('show_user_profile', array($this, 'shr_extra_profile_fields'));
        add_action('edit_user_profile', array($this, 'shr_extra_profile_fields'));
        add_action('user_new_form', array($this, 'shr_extra_profile_fields'));

        /* Save User Profile Picture */
        add_action('profile_update', array($this, 'shr_profile_update'));
        add_action('user_register', array($this, 'shr_profile_update'));

        /* Settings AJAX */

        add_action('wp_ajax_add_department', array($this, 'add_department'));
        //add_action('wp_ajax_nopriv_add_department', array($this, 'add_department'));

        add_action('wp_ajax_remove_department', array($this, 'remove_department'));
        //add_action('wp_ajax_nopriv_remove_department', array($this, 'remove_department'));

        add_action('wp_ajax_add_team_member', array($this, 'add_team_member'));
        //add_action('wp_ajax_nopriv_add_team_member', array($this, 'add_team_member'));

    } // End __construct()

    /**

     * Main Org_Chart_Admin Instance

     *

     * Ensures only one instance of Org_Chart_Admin is loaded or can be loaded.

     *

     * @since 1.0.0

     * @static

     * @return Main Org_Chart_Admin instance

     */

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    } // End instance()

    public function user_interests_fields($user)
    {
        $team = get_user_meta($user->ID, 'ocp_team_member', true);

        $org_job_title = get_user_meta($user->ID, 'ocp_job_title', true);
        $org_custom_link = get_user_meta($user->ID, 'org_custom_link', true);

        ?>

<h2><?php echo _e("Org Chart Settings", "org-chart"); ?> </h2>

<table class="form-table">
    <tr>
        <th><?php echo _e("Team Member", "org-chart"); ?> :</th>
        <td>
            <p><label for="team_member"><input id="team_member" name="ocp_team_member" type="checkbox" value="yes"
                        <?php if ($team == "yes") {echo "checked";}?> />Yes</label></p>
        </td>

    </tr>

    <tr>
        <th>Job Title:</th>
        <td>
            <p><label for="org_job_title"><input id="org_job_title" name="ocp_job_title" type="text"
                        value="<?php echo $org_job_title; ?>" /></label></p>
        </td>
    </tr>

    <tr>

        <th>Custom Link</th>

        <td>
            <p><label for="org_custom_link"><input id="org_custom_link" name="org_custom_link" type="text"
                        value="<?php echo $org_custom_link; ?>" /></label></p>
        </td>

    </tr>

</table>

<?php
}

    public function user_interests_fields_save($user_id)
    {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        if (isset($_POST['ocp_team_member']) && $_POST['ocp_team_member'] == "yes") {
            update_user_meta($user_id, 'ocp_team_member', $_POST['ocp_team_member']);
        } else {
            delete_user_meta($user_id, 'ocp_team_member');
        }

        if (!empty($_POST['ocp_job_title'])) {
            update_user_meta($user_id, 'ocp_job_title', trim($_POST['ocp_job_title']));
        } else {
            delete_user_meta($user_id, 'ocp_job_title');
        }
        if (!empty($_POST['org_custom_link'])) {
            update_user_meta($user_id, 'org_custom_link', trim($_POST['org_custom_link']));
        } else {
            delete_user_meta($user_id, 'org_custom_link');
        }

    }

    public function shr_extra_profile_fields($user)
    {
        $profile_pic = ($user !== 'add-new-user') ? get_user_meta($user->ID, 'ocp_shr_pic', true) : false;

        if (!empty($profile_pic)) {
            $image = wp_get_attachment_image_src($profile_pic, 'thumbnail');
        }?>

<table class="form-table fh-profile-upload-options">

    <tr>
        <th><label for="image"><?php _e('Main Profile Image', 'shr')?></label></th>
        <td>
            <input type="button" data-id="shr_image_id" data-src="shr-img" class="button shr-image" name="ocp_shr_image"
                id="shr-image" value="Upload" />
            <input type="hidden" class="button" name="ocp_shr_image_id" id="shr_image_id"
                value="<?php echo !empty($profile_pic) ? $profile_pic : ''; ?>" />

            <img id="shr-img" src="<?php echo !empty($profile_pic) ? $image[0] : ''; ?>"
                style="<?php echo empty($profile_pic) ? 'display:none;' : '' ?> max-width: 100px; max-height: 100px;" />

        </td>
    </tr>
</table>

<?php
}

    public function shr_profile_update($user_id)
    {
        if (current_user_can('edit_users')) {
            $profile_pic = empty($_POST['ocp_shr_image_id']) ? '' : $_POST['ocp_shr_image_id'];
            update_user_meta($user_id, 'ocp_shr_pic', $profile_pic);
        }
    }

    public function add_team_member()
    {
        global $wpdb;
        $selected_user_id = $_POST['user_id'];
        //print_r($selected_user_id);

        $authors = $wpdb->get_results("SELECT ID, user_nicename from $wpdb->users ORDER BY display_name");
        foreach ($authors as $author) {
            $user_id = $author->ID;
            delete_user_meta($user_id, 'ocp_team_member');
        }
        foreach ($selected_user_id as $user) {
            echo $user;
            update_user_meta($user, 'ocp_team_member', 'yes');
        }
        die();
    }

    public function list_deptt()
    {

        echo "<ul>";

        $department_fetch = get_option('department_new');
        $department_fetch = unserialize($department_fetch);
        //print_r($department_fetch);
        if (is_array($department_fetch)) {
            foreach ($department_fetch as $key => $value) {
                ?>
<li>
    <h3><?php echo $value; ?></h3>
    <span><input class="current_dep" type="button" id="current_dep_<?php echo $key; ?>"
            dep_value="<?php echo $key; ?>" value="Delete" /></span>
</li>

<?php	}
        }

        echo "</ul>";

    }

    public function add_department()
    {
        //delete_option( 'department_new' );

        $department_value = $_POST['department'];
        $department = get_option('department_new');
        $ft_array = unserialize($department);

        if (is_array($ft_array) && $department != null) {

            $ft_array = unserialize($department);
            $dept_key = preg_replace('/\s+/', '_', $department_value);
            $dep_value[$dept_key] = $department_value;
            $dep_value = array_merge($dep_value, $ft_array);
            $dep_value = serialize($dep_value);
            update_option('department_new', $dep_value, "no");
            if (array_key_exists($department_value, $ft_array)) {
                echo "1";
                exit;
            }

        } else {

            $dept_key = preg_replace('/\s+/', '_', $department_value);
            $dep_value[$dept_key] = $department_value;
            $dep_value = serialize($dep_value);

            update_option('department_new', $dep_value, "no");
        }

        $this->list_deptt();

        die();
    }

    public function remove_department()
    {

        $department_value = $_POST['department'];
        $department = get_option('department_new');
        $ft_array = unserialize($department);
        unset($ft_array[$department_value]);
        $dep_value = serialize($ft_array);
        update_option('department_new', $dep_value);
        $this->list_deptt();
        die();
    }

    public function get_user_role($user_id = "")
    {
        $user_meta = get_userdata($user_id);
        $user_roles = $user_meta->roles;
        $user_role_list = "";
        foreach ($user_roles as $key => $value) {
            $user_role_list .= $value . " ";
        }
        return $user_role_list;
    }

    public function ocp_list_all_roles()
    {
        global $wp_roles;
        $roles = $wp_roles->get_names();
        $role_list = '<div class="ocp-filter-users"> <b>' . __("Select / Filter", "org-chart") . '</b> : <span class="ocp-role"><input name="filter-user-role" type="radio" data-filter="all" class="ocp-check-non all" value="all"> All </span>';
        foreach ($roles as $key => $value) {
            $role_list .= '<span class="ocp-role"><input name="filter-user-role" type="radio" data-filter="' . $key . '" class="ocp-check-non ' . $key . '" value="' . $key . '"> ' . $value . '</span>';
        }
        $role_list .= '<span class="ocp-role"><input name="filter-user-role" type="radio" data-filter="" class="ocp-check-non none" value="none"> None </span>';
        $role_list .= "</div>";

        return $role_list;
    }

    public function ocp_uncheck_roles()
    {
        global $wp_roles;
        $roles = $wp_roles->get_names();
        $role_list = '<div class="ocp-uncheck-users"> <b>' . __("Un-Check", "org-chart") . '</b> : <span class="ocp-role"><input name="uncheck-user-role" type="radio" data-filter="all" class="ocp-uncheck-mem all" value="all"> All </span>';
        foreach ($roles as $key => $value) {
            $role_list .= '<span class="ocp-role"><input name="uncheck-user-role" type="radio" data-filter="' . $key . '" class="ocp-uncheck-mem ' . $key . '" value="' . $key . '"> ' . $value . '</span>';
        }
        $role_list .= '<span class="ocp-role"><input name="uncheck-user-role" type="radio" data-filter="" class="ocp-uncheck-mem none" value="none"> None </span>';
        $role_list .= "</div>";

        return $role_list;
    }

    /**

     * Register the admin screen.

     * @access  public

     * @since   1.0.0

     * @return  void

     */

    public function register_settings_screen()
    {
        $this->_hook = add_submenu_page('edit.php?post_type=org_chart', __('Org Chart Settings', 'org-chart'), __('Settings', 'org-chart'), 'manage_options', 'org-setting', array($this, 'settings_screen'), 2);
    } // End register_settings_screen()

    /**

     * Output the markup for the settings screen.

     * @access  public

     * @since   1.0.0

     * @return  void

     */

    public function settings_screen()
    {
        global $title;

        $sections = Org_Chart()->settings->get_settings_sections();

        $tab = $this->_get_current_tab($sections);?>

<div class="wrap org-chart-wrap">
    <style>
    .wrap.org-chart-wrap form>h2 {
        display: none !important;
    }
    </style>
    <?php

        echo $this->get_admin_header_html($sections, $title); ?>

    <?php

        if ($tab == "welcome-section") {

        }

        if ($tab == "standard-fields") {
            ?>

    <h2 class="admin_section_title"><?php echo _e("Add/Remove Departments", "org-chart"); ?> </h2>
    <div class="design_dep">
        <input type="hidden" id="org_admin_url" value="<?php echo get_home_url(); ?>/wp-admin/admin-ajax.php" />
        <input type="text" name="department" class="form-input-tip" placeholder="Enter Department Name"
            id="department" />
        <input type="button" id="adddep" name="adddep" class="button button-primary"
            value="<?php echo _e("Add Departments", "org-chart"); ?>" />

    </div>
    <div class="dep_table" id="dep_table">

        <?php $this->list_deptt();?>

    </div>

    <?php

        }

        if ($tab == "special-fields") {
            global $wpdb;

            $authors = $wpdb->get_results("SELECT ID, user_nicename from $wpdb->users ORDER BY display_name");
            foreach ($authors as $author) {
                $user_id = $author->ID;
                $team_member = get_user_meta($user_id, 'ocp_team_member', true);
                if ($team_member == 'yes') {
                    $members[] = $user_id;
                } else {
                    $non_members[] = $user_id;
                }
            }
            //print_r($members);
            //print_r($non_members);?>

    <h2 class="admin_section_title"> <?php echo _e("Enable/Disable users as Team Member", "org-chart"); ?>
        <input type="button" id="save_team_memeber" class="button button-primary" name="save_team_memeber"
            value="<?php echo _e("Save Team Member", "org-chart"); ?>" />
        <i class="fa fa-refresh fa-spin"></i>
        <span class="user_update_notice"></span>
    </h2>

    <div class="user_management">

        <?php

            if (!empty($members)) {
                $count = 1;

                echo $this->ocp_uncheck_roles();

                foreach ($members as $member) {
                    $user_roles = $this->get_user_role($member);
                    $uimg = get_user_meta($member, 'ocp_shr_pic', true);

                    if (($count % 6) == 1) {
                        //echo $count/6;
                        $its_first = "first-col";
                    } else { $its_first = "";}
                    ?>

        <li class="two columns <?=$its_first;?>">
            <?php $user_id = $member;
                    $uimg = get_user_meta($user_id, 'ocp_shr_pic', true);
                    $image = wp_get_attachment_image_src($uimg, 'thumbnail');
                    if (!empty($uimg)) {?>
            <!--  <img src="<?php echo $image[0]; ?>" src="user_uploaded"> -->

            <?php } else {}

                    $team_member = get_user_meta($user_id, 'ocp_team_member', true);?>
            <span class="enable_user">
                <input name="selector[]" id="user_selected_<?php echo $user_id; ?>" <?php if ($team_member == 'yes') {
                        ?> checked="checked" <?php

                    }?>" class="users_checkbox ocp-member all <?php echo $user_roles; ?> " type="checkbox"
                    value="<?php echo $user_id; ?>" /></span>
            <h3> <a
                    href="<?php echo get_edit_user_link($user_id); ?>"><?php the_author_meta('display_name', $member);?></a>
            </h3>


        </li>
        <?php
$count++;
                }
            }?>

        <li class="seperator clear">
            <h2> <?php echo _e("Non-Members", "org-chart"); ?> </h2>
            <?php if (!empty($non_members)) {
                echo $this->ocp_list_all_roles();
            }
            ?>
        </li>


        <?php
if (!empty($non_members)) {
                $count = 1;
                foreach ($non_members as $non_member) {

                    $user_roles = $this->get_user_role($non_member);

                    if (($count % 6) == 1) {
                        //echo $count/6;
                        $its_first = "first-col";
                    } else { $its_first = "";}
                    ?>
        <li class="two columns <?=$its_first;?>">
            <?php $user_id = $non_member;
                    $uimg = get_user_meta($user_id, 'ocp_shr_pic', true);
                    $image = wp_get_attachment_image_src($uimg, 'thumbnail');
                    if (!empty($uimg)) {?>
            <!--  <img src="<?php echo $image[0]; ?>" src="user_uploaded"> -->

            <?php } else {}

                    $team_member = get_user_meta($user_id, 'ocp_team_member', true);?>
            <span class="enable_user">
                <input name="selector[]" id="user_selected_<?php echo $user_id; ?>" <?php if ($team_member == 'yes') {
                        ?> checked="checked" <?php

                    }?>" class="users_checkbox ocp-non-member all <?php echo $user_roles; ?> " type="checkbox"
                    value="<?php echo $user_id; ?>" /></span>
            <h3> <a
                    href="<?php echo get_edit_user_link($user_id); ?>"><?php the_author_meta('display_name', $non_member);?></a>
            </h3>
        </li>
        <?php
$count++;}
            }?>

        <div class="clear"></div>
        <input type="hidden" id="org_admin_url" value="<?php echo get_home_url(); ?>/wp-admin/admin-ajax.php" />

    </div>
    <?php
}?>
    <form action="options.php" method="post">

        <?php

        settings_fields('org-chart-settings-' . $tab);
        do_settings_sections('org-chart-' . $tab);
        if ($tab != "special-fields" && $tab != "standard-fields") {
            submit_button(__('Save Changes', 'org-chart'));
        }

        ?>

    </form>

</div>
<!--/.wrap-->

<?php
}
// End settings_screen()

    /**

     * Register the settings within the Settings API.

     * @access  public

     * @since   1.0.0

     * @return  void

     */

    public function register_settings()
    {
        $sections = Org_Chart()->settings->get_settings_sections();

        if (0 < count($sections)) {
            foreach ($sections as $k => $v) {
                register_setting('org-chart-settings-' . sanitize_title_with_dashes($k), 'org-chart-' . $k, array($this, 'validate_settings'));
                add_settings_section(sanitize_title_with_dashes($k), $v, array($this, 'render_settings'), 'org-chart-' . $k, $k, $k);
            }
        }
    } // End register_settings()

    /**

     * Render the settings.

     * @access  public

     * @param  array $args arguments.

     * @since   1.0.0

     * @return  void

     */

    public function render_settings($args)
    {
        $token = $args['id'];

        $fields = Org_Chart()->settings->get_settings_fields($token);

        if (0 < count($fields)) {
            foreach ($fields as $k => $v) {
                $args = $v;

                $args['id'] = $k;
                add_settings_field($k, $v['name'], array(Org_Chart()->settings, 'render_field'), 'org-chart-' . $token, $v['section'], $args);
            }
        }
    } // End render_settings()

    /**

     * Validate the settings.

     * @access  public

     * @since   1.0.0

     * @param   array $input Inputted data.

     * @return  array        Validated data.

     */

    public function validate_settings($input)
    {
        $sections = Org_Chart()->settings->get_settings_sections();
        $tab = $this->_get_current_tab($sections);
        return Org_Chart()->settings->validate_settings($input, $tab);
    } // End validate_settings()

    /**

     * Return marked up HTML for the header tag on the settings screen.

     * @access  public

     * @since   1.0.0

     * @param   array  $sections Sections to scan through.

     * @param   string $title    Title to use, if only one section is present.

     * @return  string              The current tab key.

     */

    public function get_admin_header_html($sections, $title)
    {
        $defaults = array(

            'tag' => 'h2',

            'atts' => array('class' => 'org-chart-wrapper'),

            'content' => $title,

        );

        $args = $this->_get_admin_header_data($sections, $title);

        $args = wp_parse_args($args, $defaults);

        $atts = '';

        if (0 < count($args['atts'])) {
            foreach ($args['atts'] as $k => $v) {
                $atts .= ' ' . esc_attr($k) . '="' . esc_attr($v) . '"';
            }
        }

        $response = '<' . esc_attr($args['tag']) . $atts . '>' . $args['content'] . '</' . esc_attr($args['tag']) . '>' . "\n";

        return $response;
    } // End get_admin_header_html()

    /**

     * Return the current tab key.

     * @access  private

     * @since   1.0.0

     * @param   array  $sections Sections to scan through for a section key.

     * @return  string              The current tab key.

     */

    private function _get_current_tab($sections = array())
    {
        if (isset($_GET['tab'])) {
            $response = sanitize_title_with_dashes($_GET['tab']);
        } else {
            if (is_array($sections) && !empty($sections)) {
                list($first_section) = array_keys($sections);

                $response = $first_section;
            } else {
                $response = '';
            }
        }

        return $response;
    } // End _get_current_tab()

    /**

     * Return an array of data, used to construct the header tag.

     * @access  private

     * @since   1.0.0

     * @param   array  $sections Sections to scan through.

     * @param   string $title    Title to use, if only one section is present.

     * @return  array              An array of data with which to mark up the header HTML.

     */

    private function _get_admin_header_data($sections, $title)
    {
        $response = array('tag' => 'h2', 'atts' => array('class' => 'org-chart-wrapper'), 'content' => $title);

        if (is_array($sections) && 1 < count($sections)) {
            $response['content'] = '';

            $response['atts']['class'] = 'nav-tab-wrapper';

            $tab = $this->_get_current_tab($sections);

            foreach ($sections as $key => $value) {
                $class = 'nav-tab';

                if ($tab == $key) {
                    $class .= ' nav-tab-active';
                }

                $response['content'] .= '<a href="' . admin_url('edit.php?post_type=org_chart&page=org-setting&tab=' . sanitize_title_with_dashes($key)) . '" class="' . esc_attr($class) . '">' . esc_html($value) . '</a>';
            }
        }

        return (array) apply_filters('org-chart-get-admin-header-data', $response);
    } // End _get_admin_header_data()
} // End Class