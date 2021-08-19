<?php

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly.

/**

 * Org Chart Plugin Post Type Class

 *

 * All functionality pertaining to post types in Org Chart Plugin.

 *

 * @package WordPress

 * @subpackage Org_Chart

 * @category Plugin

 * @author Gangesh Matta

 * @since 1.0.0

 */

class Org_Chart_Post_Type
{

    /**

     * The post type token.

     * @access public

     * @since  1.0.0

     * @var    string

     */

    public $post_type;

    /**

     * The post type singular label.

     * @access public

     * @since  1.0.0

     * @var    string

     */

    public $singular;

    /**

     * The post type plural label.

     * @access public

     * @since  1.0.0

     * @var    string

     */

    public $plural;

    /**

     * The post type args.

     * @access public

     * @since  1.0.0

     * @var    array

     */

    public $args;

    /**

     * The taxonomies for this post type.

     * @access public

     * @since  1.0.0

     * @var    array

     */

    public $taxonomies;

    /**

     * Chart Display Templates Parameters

     */

    public $active_user_id;

    public $active_user_role;

    public $active_user_name;

    public $active_user_description;

    public $active_user_image;

    /**

     * Constructor function.

     * @access public

     * @since 1.0.0

     */

    public function __construct($post_type = 'org_chart', $singular = '', $plural = 'Org Charts', $args = array(), $taxonomies = array())
    {

        $this->post_type = $post_type;

        $this->singular = $singular;

        $this->plural = $plural;

        $this->args = $args;

        $this->taxonomies = $taxonomies;

        add_action('init', array($this, 'register_post_type'));

        // add_action('init', array($this, 'register_taxonomy'));

        if (is_admin()) {

            global $pagenow;

            add_action('admin_menu', array($this, 'meta_box_setup'), 20);

            add_action('save_post', array($this, 'org_save_metabox'));

            add_filter('enter_title_here', array($this, 'enter_title_here'));

            //add_filter('post_updated_messages', array($this, 'updated_messages'));

            if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == $this->post_type) {

                add_filter('manage_' . $this->post_type . '_posts_columns', array($this, 'register_custom_column_headings'), 10, 1);

                add_action('manage_' . $this->post_type . '_posts_custom_column', array($this, 'register_custom_columns'), 10, 2);

            }

        }

        /* Action for Templates Start */

        add_action('org_chart_active_node_start', array($this, 'org_chart_active_node_start_print'), 10);

        add_action('org_chart_active_node_popup_start', array($this, 'org_chart_active_node_popup_start_print'), 10);

        add_action('org_chart_active_node_popup_end', array($this, 'org_chart_active_node_popup_end_print'), 10);

        add_action('org_chart_active_node_popup_render', array($this, 'org_chart_active_node_popup_render_print'), 10, 3);

        add_action('org_chart_active_node_anchor_start', array($this, 'org_chart_active_node_anchor_start_print'), 10);

        add_action('org_chart_active_node_anchor_end', array($this, 'org_chart_active_node_anchor_end_print'), 10);

        add_action('org_chart_active_node_content', array($this, 'org_chart_active_node_content_print'), 10, 2);

        add_action('org_chart_active_node_image', array($this, 'org_chart_active_node_image_print'), 10);

        /* Action for Templates End */

        add_action('after_setup_theme', array($this, 'ensure_post_thumbnails_support'));
        add_action('after_theme_setup', array($this, 'register_image_sizes'));

        add_filter('the_content', array($this, 'org_default_content'));

    } // End __construct()

    /**

     * Register the post type.

     * @access public

     * @return void

     */

    public function register_post_type()
    {

        $labels = array(

            'name' => sprintf(_x('%s', 'Org Chart', 'org-chart'), $this->singular),

            'singular_name' => sprintf(_x('%s', 'Org Chart', 'org-chart'), $this->singular),

            'add_new' => _x('Add New', $this->post_type, 'org-chart'),

            'add_new_item' => sprintf(__('Add New %s', 'org-chart'), $this->singular),

            'edit_item' => sprintf(__('Edit %s', 'org-chart'), $this->singular),

            'new_item' => sprintf(__('New %s', 'org-chart'), $this->singular),

            'all_items' => sprintf(__('All %s', 'org-chart'), $this->plural),

            'update_item' => sprintf(__('Update %s', 'org-chart'), $this->singular),

            'view_item' => sprintf(__('View %s', 'org-chart'), $this->singular),

            'view_items' => sprintf(__('View %s', 'org-chart'), $this->singular),

            'search_items' => sprintf(__('Search %a', 'org-chart'), $this->plural),

            'not_found' => sprintf(__('No %s Found', 'org-chart'), $this->plural),

            'not_found_in_trash' => sprintf(__('No %s Found In Trash', 'org-chart'), $this->plural),

            'parent_item_colon' => '',

            'menu_name' => $this->plural,

            'menu_name' => sprintf(__('Org Chart', 'org-chart'), $this->singular),

            'name_admin_bar' => sprintf(__('Org Chart', 'org-chart'), $this->singular),

            'archives' => sprintf(__('Org Charts', 'org-chart'), $this->plural),

            'attributes' => sprintf(__('', 'org-chart'), $this->singular),

            'parent_item_colon' => sprintf(__('', 'org-chart'), $this->singular),

            'featured_image' => sprintf(__('Featured Image', 'org-chart'), $this->singular),

            'set_featured_image' => sprintf(__('Set featured image', 'org-chart'), $this->singular),

            'remove_featured_image' => sprintf(__('Remove featured image', 'org-chart'), $this->singular),

            'use_featured_image' => sprintf(__('Use as featured image', 'org-chart'), $this->singular),

            'insert_into_item' => sprintf(__('Insert into item', 'org-chart'), $this->singular),

            'uploaded_to_this_item' => sprintf(__('Uploaded to this item', 'org-chart'), $this->singular),

            'items_list' => sprintf(__('Items list', 'org-chart'), $this->singular),

            'items_list_navigation' => sprintf(__('Items list navigation', 'org-chart'), $this->singular),

            'filter_items_list' => sprintf(__('Filter items list', 'org-chart'), $this->singular),

        );

        $defaults = array(

            'label' => __('Org Chart', 'org-chart'),

            'labels' => $labels,

            'description' => __('Create multiple Org Charts', 'org_chart'),

            //'supports' => array( 'title', 'custom-fields' ),

            'supports' => array('title'),

            'hierarchical' => true,

            'public' => true,

            'show_ui' => true,

            'show_in_menu' => true,

            'menu_position' => 70,

            'menu_icon' => 'dashicons-image-filter',

            'show_in_admin_bar' => true,

            'show_in_nav_menus' => true,

            'can_export' => true,

            'has_archive' => true,

            'exclude_from_search' => true,

            'publicly_queryable' => true,

            'capability_type' => 'post',
            'show_in_rest' => true,
            'rest_base' => 'orgchart',
            'rest_controller_class' => 'WP_REST_Posts_Controller',

        );

        $args = wp_parse_args($this->args, $defaults);

        register_post_type($this->post_type, $args);

    } // End register_post_type()

    /**

     * Add custom columns for the "manage" screen of this post type.

     * @access public

     * @param string $column_name

     * @param int $id

     * @since  1.0.0

     * @return void

     */

    public function register_custom_columns($column_name, $id)
    {

        global $post;
        //  return $column_name;
        switch ($column_name) {

            case 'shortcode':

                echo "<span class='col-shortcode'>[orgchart id=" . $id . "] </span><span class='text-copied'>Copied!</span>";

                break;

            default:

                break;

        }

    } // End register_custom_columns()

    /**

     * Add custom column headings for the "manage" screen of this post type.

     * @access public

     * @param array $defaults

     * @since  1.0.0

     * @return void

     */

    public function register_custom_column_headings($defaults)
    {

        $new_columns = array('shortcode' => __('Shortcode', 'org-chart'));

        $last_item = array();

        if (isset($defaults['date'])) {
            //unset($defaults['date']);
        }

        if (count($defaults) > 2) {

            $last_item = array_slice($defaults, -1);

            array_pop($defaults);

        }

        $defaults = array_merge($defaults, $new_columns);

        if (is_array($last_item) && 0 < count($last_item)) {

            foreach ($last_item as $k => $v) {

                $defaults[$k] = $v;

                break;

            }

        }

        return $defaults;

    } // End register_custom_column_headings()

    /**

     * Update messages for the post type admin.

     * @since  1.0.0

     * @param  array $messages Array of messages for all post types.

     * @return array           Modified array.

     */

    public function updated_messages($messages)
    {

        global $post, $post_ID;

        $messages[$this->post_type] = array(

            0 => '', // Unused. Messages start at index 1.

            1 => sprintf(__('%3$s updated. %sView %4$s%s', 'org-chart'), '<a href="' . esc_url(get_permalink($post_ID)) . '">', '</a>', $this->singular, strtolower($this->singular)),

            2 => __('Custom field updated.', 'org-chart'),

            3 => __('Custom field deleted.', 'org-chart'),

            4 => sprintf(__('%s updated.', 'org-chart'), $this->singular),

            /* translators: %s: date and time of the revision */

            5 => isset($_GET['revision']) ? sprintf(__('%s restored to revision from %s', 'org-chart'), $this->singular, wp_post_revision_title((int) $_GET['revision'], false)) : false,

            6 => sprintf(__('%1$s published. %3$sView %2$s%4$s', 'org-chart'), $this->singular, strtolower($this->singular), '<a href="' . esc_url(get_permalink($post_ID)) . '">', '</a>'),

            7 => sprintf(__('%s saved.', 'org-chart'), $this->singular),

            8 => sprintf(__('%s submitted. %sPreview %s%s', 'org-chart'), $this->singular, strtolower($this->singular), '<a target="_blank" href="' . esc_url(add_query_arg('preview', 'true', get_permalink($post_ID))) . '">', '</a>'),

            9 => sprintf(__('%s scheduled for: %1$s. %2$sPreview %s%3$s', 'org-chart'), $this->singular, strtolower($this->singular),

                // translators: Publish box date format, see http://php.net/date

                '<strong>' . date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)) . '</strong>', '<a target="_blank" href="' . esc_url(get_permalink($post_ID)) . '">', '</a>'),

            10 => sprintf(__('%s draft updated. %sPreview %s%s', 'org-chart'), $this->singular, strtolower($this->singular), '<a target="_blank" href="' . esc_url(add_query_arg('preview', 'true', get_permalink($post_ID))) . '">', '</a>'),

        );

        return $messages;

    } // End updated_messages()

    /**

     * Setup the meta box.

     * @access public

     * @since  1.0.0

     * @return void

     */

    public function meta_box_setup()
    {

        add_meta_box($this->post_type . '-data', __('Org Chart Builder - <span class="oblock">Drag and Drop users in order to set levels.</span>', 'org-chart'), array($this, 'meta_box_content'), $this->post_type, 'normal', 'high');

        add_meta_box($this->post_type . '-data1', __('JSON URL', 'org-chart'), array($this, 'meta_box_json_url'), $this->post_type, 'normal', 'low');

        add_meta_box($this->post_type . '-data2', __('Usage', 'org-chart'), array($this, 'meta_box_design_template'), $this->post_type, 'side', 'low');

        add_meta_box($this->post_type . '-data3', __('Settings', 'org-chart'), array($this, 'meta_box_org_settings'), $this->post_type, 'side', 'high');

    } // End meta_box_setup()

    /**

     * The contents of our meta box.

     * @access public

     * @since  1.0.0

     * @return void

     */

    public function meta_box_design_template()
    {

        global $post_id;
        $html = "";
        $html .= '<b> [orgchart id="' . $post_id . '"]</b> <br/><br/><div>' . __("Use shortcode to display chart on any page or post", "org-chart") . ' <br/><br/><hr/><a class="button button-primary" id="print_chart" href="#">' . __("Print Chart", "org-chart") . '</a></div>';

        echo $html;

    }

    public function meta_box_org_settings()
    {
        global $post_id;
        $print_option = get_post_meta($post_id, 'org_print_option', true);
        $link_option = get_post_meta($post_id, 'org_link_option', true);
        $image_option = get_post_meta($post_id, 'org_image_option', true);

        ?>
<p>
    <span> <?php echo __("These options will overide global settings.", "org-chart"); ?> </span>
</p>
<div class="set_link_option">
    <p class="set-option-label-wrapper"> <label class="post-attributes-label">
            <?php echo __("User Link:", "org-chart"); ?> </label> </p>

    <select name="org_link_option" id="org_link_option">
        <option value="global" <?php selected($link_option, 'global');?>>
            <?php echo __("Global Setting", "org-chart"); ?></option>
        <option value="popup" <?php selected($link_option, 'popup');?>><?php echo __("Popup", "org-chart"); ?></option>
        <option value="wpdefault" <?php selected($link_option, 'wpdefault');?>>
            <?php echo __("Wordpress Default", "org-chart"); ?></option>
        <?php if (function_exists('bp_is_active')) {?>
        <option value="bplink" <?php selected($link_option, 'bplink');?>>
            <?php echo __("Buddypress Profile", "org-chart"); ?></option>
        <?php }?>
        <option value="customlink" <?php selected($link_option, 'customlink');?>>
            <?php echo __("Custom Link", "org-chart"); ?></option>
        <option value="nolink" <?php selected($link_option, 'nolink');?>><?php echo __("No Link", "org-chart"); ?>
        </option>
    </select>
</div>

<div class="set_image_option">
    <p class="set-option-label-wrapper"> <label
            class="post-attributes-label"><?php echo __("Image Source:", "org-chart"); ?> </label> </p>

    <select name="org_image_option" id="org_image_option">
        <option value="global" <?php selected($image_option, 'global');?>>
            <?php echo __("Global Setting", "org-chart"); ?></option>
        <option value="custom_img" <?php selected($image_option, 'custom_img');?>>
            <?php echo __("Custom", "org-chart"); ?></option>
        <?php if (function_exists('bp_is_active')) {?>
        <option value="bp_img" <?php selected($image_option, 'bp_img');?>>
            <?php echo __("Buddypress Profile", "org-chart"); ?></option>
        <?php }?>
        <option value="gavatar" <?php selected($image_option, 'gavatar');?>><?php echo __("Gavatar", "org-chart"); ?>
        </option>
        <option value="noimage" <?php selected($image_option, 'noimage');?>><?php echo __("No Image", "org-chart"); ?>
        </option>
    </select>
</div>

<div class="set_print_option">
    <p class="set-option-label-wrapper"><label class="post-attributes-label">
            <?php echo __("Allow visitors to print chart:", "org-chart"); ?> </label></p>

    <select name="org_print_option" id="org_print_option">
        <option value="global" <?php selected($print_option, 'global');?>>
            <?php echo __("Global Setting", "org-chart"); ?></option>
        <option value="yes" <?php selected($print_option, 'yes');?>><?php echo __("Yes", "org-chart"); ?></option>
        <option value="no" <?php selected($print_option, 'no');?>><?php echo __("No", "org-chart"); ?></option>
    </select>
</div>

<?php

    }

    public function org_save_metabox()
    {
        global $post_id;
        if (isset($_POST["org_print_option"])) {
            $print_option = $_POST['org_print_option'];
            update_post_meta($post_id, 'org_print_option', $print_option);
        }
        if (isset($_POST["org_link_option"])) {
            $link_option = $_POST['org_link_option'];
            update_post_meta($post_id, 'org_link_option', $link_option);
        }

        if (isset($_POST["org_image_option"])) {
            $image_option = $_POST['org_image_option'];
            update_post_meta($post_id, 'org_image_option', $image_option);
        }
        if (isset($_POST["org_json"])) {
            $org_json = $_POST['org_json'];
            update_post_meta($post_id, 'org_json', $org_json);
        }
        else {
            delete_post_meta($post_id, 'org_json');
        }
    }

    public function meta_box_json_url()
    {

        global $post_id;
        $org_json = get_post_meta($post_id, 'org_json', true);
        $check = "";
        if ($org_json == 1) { $check = "checked"; }
        $html = "";
        $html .= "<div class='json_url'><span>" . get_site_url(null, '/wp-json/orgchart/v1/chart/' . $post_id) . "</span><br/><br/></div>";
       $html  .= "<input type='checkbox' name='org_json'  " .$check. " value='1'> Enable JSON <br/>";
        $html .= "<small>*" . __("Works when post is published", "org-chart") . "</small>";

        echo $html;

    }

    public function meta_box_content()
    {

        global $post, $post_id;

        $id = $post->ID;
        $fields = get_post_custom($post_id);

        //print_r($fields);

        $field_data = $this->get_custom_fields_settings();

        $top_level_id = '';

        $user_query0 = new WP_User_Query(array('meta_key' => 'top_org_level', 'meta_value' => 1));

        if (!empty($user_query0->results)) {

            foreach ($user_query0->results as $user) {

                $top_level_id = $user->ID;

                $top_level = $user->display_name;

            }

        }

        $args = array(

            'meta_key' => 'ocp_team_member',
            'meta_value' => 'yes',

        );

        $users = get_users($args);
        $list_users = "";
        // now make your users dropdown

        foreach ($users as $userz) {

            $top_user = '';

            if ($userz->ID == $top_level_id) {

                $top_user = "selected";

            }

            $list_users .= '<option value="' . $userz->ID . '">' . $userz->display_name . '</option>';

        }

        // now get selected user id from $_POST to use in your next function

        if (isset($_POST['user_dropdown'])) {

            $userz_id = $_POST['user_dropdown'];

            $user_data = get_user_by('id', $userz_id);

        }

        $top = '';
        $top .= '<div class="set_top_level"><span class="oinline"><select name="user_dropdown" id="user_dropdown"><option value="">' . __("Select Top Level.", "org-chart") . '</option>';
        $top .= $list_users;
        $top .= '</select>';
        $top .= '<input type="hidden" value="' . $post_id . '" name="post_id" id="post_id" /><input type="hidden" value="' . admin_url('admin-ajax.php') . '" name="admin_url" id="org_admin_url" />
        <select name="setchildren" id="setchildren"><option value="">All Members</option><option value="no">No Children</option></select>
        <input type="button" name="osubmit" id="oreset" class="button" value="' . __("Reset Chart", "org-chart") . '"/></span></div>';

        echo $top;

        if (get_post_meta($id, 'org_array', true) != '') {

            $org_chart_template = 'default';

            $org_array = get_post_meta($id, 'org_array', true);

            $tree = unserialize($org_array);

            $templates = Org_Chart_Templates::instance();

            $result = $templates->parseTree($tree);

            echo '<ul id="org" style="display:none">';

            echo $templates->printTree($result, 0, $org_chart_template, $id);

            echo '</ul><div id="chart" class="orgChart chart_' . $id . '"></div>';

        } else {

            echo '<h3>' . __("Select top level user", "org-chart") . '<span class="up-arrow dashicons dashicons-undo"></span></h3>';

        }
        // echo $html;
        $html = ' ';
        $html .= '<div class="chart-bottom"><p class="submit"><input type="button" onClick="makeArrays();" class="button-primary" value="Quick Save"/></p><div class="chart_saved" style="display: none"><span>' . __("Chart Saved", "org-chart") . '!</span></div>';
        global $post_id;

        $url = str_replace("classes", "", plugin_dir_url(__FILE__)) . 'templateupload.php?post_id=' . $post_id;

        $org_array = get_post_meta($id, 'org_array', true);
        // $org_array = get_option('org_array');

        $org_array = unserialize($org_array);

        $rest = array();

        if (!empty($org_array)) {

            foreach ($users as $user) {

                if (array_key_exists($user->ID, $org_array)) {

                } else {

                    $rest[] = $user->ID;

                }

            }

        }

        $html .= '<div class="modify_nodes"><select id="comboBox"><option value="">' . __("Select User", "org-chart") . '</option>';

        $hiden_val = '';

        foreach ($rest as $rid) {

            $ud = get_userdata($rid);

            $uimg = get_user_meta($rid, 'ocp_shr_pic', true);

            $org_role = get_user_meta($rid, 'ocp_job_title', true);

            if (!empty($uimg)) {

                $image = wp_get_attachment_image_src($uimg, 'thumbnail');

                //  $img = $image[0];
                $img = "";
            } else {

                $img = "";

            }

            $html .= '<option value="' . $rid . '*' . $img . '*' . $org_role . '*' . $ud->display_name . '">' . $ud->display_name . '</option>';

        }

        $users = get_users(array('ocp_team_member' => 'yes'));

        // now make your users dropdown

        if ($users) {

            foreach ($users as $userz) {

                $rid = $userz->ID;

                $ud = get_userdata($rid);

                $uimg = get_user_meta($rid, 'ocp_shr_pic', true);

                $org_role = get_user_meta($rid, 'ocp_job_title', true);

                if (!empty($uimg)) {

                    $image = wp_get_attachment_image_src($uimg, 'thumbnail');

                    $img = $image[0];

                } else {

                    $img = "";

                }

                if ($hiden_val == "") {

                    $hiden_val = $rid . '*' . $img . '*' . $org_role . '*' . $ud->display_name;

                } else {

                    $hiden_val = $hiden_val . '$' . $rid . '*' . $img . '*' . $org_role . '*' . $ud->display_name;

                }

            }

        }

        $department = get_option('department_new');
        $ft_array = unserialize($department);

        $html .= '</select> <button id="btnAddOrg" type="button" class="button ">' . __("Add User", "org-chart") . '</button> or <button id="set_top_user" type="button" class="button button-primary">' . __("Replace Top Level", "org-chart") . '</button></div><div class="modify_nodes">';
        if(!empty($ft_array)) {
        
            $html .= '<select id="headBox"><option value="">' . __("Select Dept.", "org-chart") . '</option>';
            foreach ($ft_array as $key => $value) {
                $html .= '<option value="' . $key . '">' . $value . '</option>';
            }
            $html .= '</select><button id="addHead" type="button" class="button ">' . __("Add Dept.", "org-chart") . '</button>';

        
    }else {
            $html .= __("Please add departments from.", "org-chart") . ' <a href="' . admin_url('edit.php?post_type=org_chart&page=org-setting&tab=standard-fields') . '">' . __("settings.", "org-chart") . '</a>';
        }

        $html .= '</div>';

        $html .= '</div> <div id="load_chart" style="display:none;"></div><input type="hidden" id="hidden_val" name="hidden_val" value="' . $hiden_val . '" /><canvas id="print_canvas" width="100%" height="10" style="opacity:0;"></canvas>';

        echo $html;

    } // End meta_box_content()

    /**

     * Save meta box fields.

     * @access public

     * @since  1.0.0

     * @param int $post_id

     * @return int $post_id

     */

    // Not in use currently, might be added in future releases.
    public function meta_box_save($post_id)
    {

        $selected_template = $_POST['org_chart_templates'];
        delete_post_meta($post_id, 'org_chart_templates');
        add_post_meta($post_id, 'org_chart_templates', $selected_template, true);

    } // End meta_box_save()

    /**

     * Customise the "Enter title here" text.

     * @access public

     * @since  1.0.0

     * @param string $title

     * @return void

     */

    public function enter_title_here($title)
    {

        if (get_post_type() == $this->post_type) {

            $title = __('Enter the chart title here', 'org-chart');

        }

        return $title;

    } // End enter_title_here()

    /**

     * Get the settings for the custom fields.

     * @access public

     * @since  1.0.0

     * @return array

     */

    public function get_custom_fields_settings()
    {

    } // End get_custom_fields_settings()

    /**

     * Run on activation.

     * @access public

     * @since 1.0.0

     */

    public function activation()
    {

        $this->flush_rewrite_rules();

    } // End activation()

    /**

     * Flush the rewrite rules

     * @access public

     * @since 1.0.0

     */

    private function flush_rewrite_rules()
    {

        $this->register_post_type();

        flush_rewrite_rules();

    } // End flush_rewrite_rules()

    /**

     * Ensure that "post-thumbnails" support is available for those themes that don't register it.

     * @access public

     * @since  1.0.0

     */

    public function ensure_post_thumbnails_support()
    {

        if (!current_theme_supports('post-thumbnails')) {add_theme_support('post-thumbnails');}

    } // End ensure_post_thumbnails_support()

    /**

     * Filter default content with org chart

     * @access public

     * @since  1.0.0

     */

    public function org_default_content($content)
    {
        global $post;
        if ($post->post_type == 'org_chart') {
            $content .= '[orgchart id="' . $post->ID . '"]';
        }
        return $content;
    }

} // End Class