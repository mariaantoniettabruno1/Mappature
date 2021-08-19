<?php

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

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

final class Org_Chart_Templates
{

    /**

     * Org_Chart_Admin The single instance of Org_Chart_Admin.

     * @var     object

     * @access  private

     * @since     1.0.0

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
        // add_action('rest_api_init', array($this, 'my_register_route'));
        add_action('before_popup_content', array($this, 'before_popup_layout'), 5, 1);
        add_action('after_popup_content', array($this, 'after_popup_layout'), 5, 1);
        add_action('popup_content', array($this, 'show_name_title'), 5, 2);
        add_action('popup_content', array($this, 'show_description'), 10, 2);
        add_action('before_org_node', array($this, 'before_node'), 5, 2);
        add_action('after_org_node', array($this, 'after_node'), 5, 2);
        add_action('org_node', array($this, 'user_image'), 5, 2);
        add_action('org_node', array($this, 'node_content'), 10, 2);

    } // End __construct()

    /**

     * Main Org_Chart_Templates Instance

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

    /**

     * Parse the Tree for display.

     * @access public

     * @since  1.0.0

     * @return array

     */

    public function parseTree($tree, $root = null)
    {

        $return = array();

        # Traverse the tree and search for direct children of the root

        if (!empty($tree)) {

            foreach ($tree as $child => $parent) {

                # A direct child is found

                if ($parent == $root) {

                    # Remove item from tree (we don't need to traverse this again)

                    unset($tree[$child]);

                    # Append the child into result array and parse its children

                    $return[] = array(

                        'name' => $child,

                        'children' => $this->parseTree($tree, $child),

                    );

                }

            }

        }

        return empty($return) ? null : $return;

    }

    /**

     * Print the Tree to display.

     * @access public

     * @since  1.0.0

     * @return array

     */

    public function printTree($tree, $count = 0, $org_chart_template, $chart_id)
    {
        $output = "";
        if (!is_null($tree) && count($tree) > 0) {

            if ($count == 0) {
                $output .= '';
            } else {
                $output .= '<ul class="2">';
            }

            foreach ($tree as $node) {
                //if(is_string($node['name']) && substr($node['name'], 0, 1) === 'h') {
                if (is_string($node['name'])) {
                    $node_name = str_replace('_', ' ', $node['name']);
                    $output .= '<li class="org_dept_node" id="' . $node['name'] . '"> <span class="org_dept">' . $node_name . '</span> ';
                    if ($count != 0 && is_admin()) {$output .= '<span class="name_c org_dept" id="' . $node['name'] . '"></span><a class="rmv-nd close" href="javascript:void(0);">Remove</a>';}

                } else {

                    $userid = (int) $node['name'];

                    $user_info = get_userdata($userid);

                    //$ojt = get_user_meta($userid, 'org_job_title', true);

                    $user_data = get_user_by('id', $userid);

                    //$org_date = date("m Y", strtotime(get_userdata($userid)->user_registered));;

                    /* Store Values for Template */

                    $active_user_id = $userid;
                    $check_uimg = $this->check_user_image($chart_id, $active_user_id);
                    if ($check_uimg) {
                        $uimg_class = "has-image";
                    } else { $uimg_class = "has-no-image";}

                    $output .= '<li class="' . $uimg_class . '" id="' . $active_user_id . '">';
                    // $output .= $child;
                    if (file_exists(get_stylesheet_directory() . "/org-chart/default.php")) {
                        ob_start();
                        wp_enqueue_style('orgchart-template-style', get_stylesheet_directory_uri() . '/org-chart/style.css');

                        include get_stylesheet_directory() . '/org-chart/default.php';
                        $output .= ob_get_clean();
                    } else {
                        ob_start();
                        wp_enqueue_style('orgchart-template-style', str_replace("/classes/", "", plugin_dir_url(__FILE__)) . '/templates/style.css');
                        include str_replace("classes", "templates", dirname(__FILE__)) . '/default.php';
                        $output .= ob_get_clean();
                    }

                    if ($count != 0 && is_admin()) {$output .= '<span class="name_c" id="' . $userid . '"></span><a class="rmv-nd close" href="javascript:void(0);">Remove</a>';}

                }

                $output .= $this->printTree($node['children'], 1, $org_chart_template, $chart_id);

                $output .= '</li>';

            }

            $output .= '</ul>';

        }

        return $output;

    }

    public function before_popup_layout($user_id)
    {

        $out = "";

        $out = ' <div data-id="bio' . $user_id . '" id="bio' . $user_id . '" class="overlay1 white-popup mfp-hide">
        <div class="popup1"> <div class="content1">';

        echo $out;

    }

    public function after_popup_layout($user_id)
    {

        $out = "";

        $out = ' </div>
        </div>
        </div>';

        echo $out;

    }

    public function before_node($chart_id, $user_id)
    {

        $user_link_setting = get_option('org-chart-welcome-section', array());
        $user_link_class = get_post_meta($chart_id, 'org_link_option', true);
        $user_link = "";
        $new_tab = "";
        if (isset($user_link_setting['link_new_tab'])) {
            $new_tab = "_blank";
        }
        $add_popup_class = "";

        if (isset($user_link_setting['user_link'])) {

            if (($user_link_setting['user_link'] == 'popup' && $user_link_class == "global") || ($user_link_class == "popup")) {
                $add_popup_class = "open-popup";
            }
        } else {
            $user_link_class = "global";
            $add_popup_class = "open-popup";
        }
        $link_classes = 'org_' . $user_link_class . ' ' . $user_id . " " . $add_popup_class;
        $user_link = $this->user_link($chart_id, $user_id);

        $out = '<a href="' . $user_link . '" target="' . $new_tab . '" class="bio ' . $link_classes . '">';

        echo $out;
    }

    public function after_node()
    {

        $out = '</a>';

        echo $out;
    }

    public function node_content($chart_id, $user_id)
    {
        $user_data = get_user_by('id', $user_id);
        $job_title = get_user_meta($user_id, 'ocp_job_title', true);
        $out = "";
        $out .= ' <span class="node-title">' . __($user_data->display_name, "org-chart") . '</span>';
        $out .= ' <small>' . __($job_title, "org-chart") . '</small>';
        echo $out;
    }

    public function show_name_title($chart_id, $user_id)
    {
        $user_data = get_user_by('id', $user_id);
        $job_title = get_user_meta($user_id, 'ocp_job_title', true);
        $out = "";
        $out .= ' <h3> ' . __($user_data->display_name, "org-chart") . '
        <small> ' . __($job_title, "org-chart") . ' </small>
    </h3>';
        echo $out;
    }

    public function show_description($chart_id, $user_id)
    {
        $user_desc = $this->user_description($user_id);
        echo $out = __($user_desc, "org-chart");

    }

    public function user_link($chart_id, $user_id)
    {

        $user_link = "#";
        $user_link_setting = get_option('org-chart-welcome-section', array());
        $link_option = get_post_meta($chart_id, 'org_link_option', true);
        $custom_link = get_user_meta($user_id, 'org_custom_link', true);

        if (is_array($user_link_setting) && isset($user_link_setting['user_link'])) {
            $user_link_class = $user_link_setting['user_link'];

            if ($user_link_setting['user_link'] == 'popup') {
                $user_link = "#bio" . $user_id;
            } elseif ($user_link_setting['user_link'] == 'wpdefault') {

                $user_link = get_author_posts_url($user_id);
            } elseif ($user_link_setting['user_link'] == 'customlink') {
                if ($custom_link != '') {
                    $user_link = $custom_link;
                } else { $user_link = "#chart";}
            } elseif ($user_link_setting['user_link'] == 'bplink' && function_exists('bp_is_active')) {
                $user_link = bp_core_get_user_domain($user_id);
            } else {
                $user_link = "#chart";
            }
        } else {
            $user_link = "#bio" . $user_id;
        }

        if ($link_option != "") {

            if ($link_option == "popup") {
                $user_link = "#bio" . $user_id;
            } elseif ($link_option == 'wpdefault') {
                $user_link = get_author_posts_url($user_id);
            } elseif ($link_option == 'customlink') {
                if ($custom_link != '') {
                    $user_link = $custom_link;
                } else { $user_link = "#";}
            } elseif ($link_option == 'bplink' && function_exists('bp_is_active')) {
                $user_link = bp_core_get_user_domain($user_id);
            } elseif ($link_option == "nolink") {
                $user_link = "#chart";

            } else {
                $user_link = $user_link;

            }

        }

        return $user_link = apply_filters('org_user_link', $user_link);

    }

    public function check_user_image($chart_id, $user_id)
    {

        $user_link_setting = get_option('org-chart-welcome-section', array());
        $image_option = get_post_meta($chart_id, 'org_image_option', true);
        // $user_image_html = "";
        // $user_image = "";
        //$uimg = "";
        $user_has_img = false;
        $uimg = get_user_meta($user_id, 'ocp_shr_pic', true);
        if (is_array($user_link_setting) && isset($user_link_setting['user_image'])) {

            if ($user_link_setting['user_image'] == 'custom_img' && $uimg != "") {
                $user_has_img = true;
            } else {
                $user_has_img = false;
            }
        } else {
            $user_has_img = true;
        }

        if (isset($image_option)) {

            if ($image_option == 'custom_img' && $uimg != '') {

                $user_has_img = true;
            } else {
                $user_has_img = false;
            }
        }

        return $user_has_img;

    }

    public function user_image($chart_id, $user_id)
    {

        $user_link_setting = get_option('org-chart-welcome-section', array());
        $image_option = get_post_meta($chart_id, 'org_image_option', true);
        $user_image_html = "";
        $user_image = "";
        $uimg = "";

        if (is_array($user_link_setting) && isset($user_link_setting['user_image'])) {

            if ($user_link_setting['user_image'] == 'custom_img') {

                $uimg = get_user_meta($user_id, 'ocp_shr_pic', true);
                $image = wp_get_attachment_image_src($uimg, 'thumbnail');
                $user_image = "custom_img";
            } elseif ($user_link_setting['user_image'] == 'gavatar') {
                $user_image = "gavatar";
            } elseif ($user_link_setting['user_image'] == 'bp_img') {
                $user_image = 'bp_img';
            } else {
                $user_image = "";
            }
        }

        if (isset($image_option)) {

            if ($image_option == 'custom_img') {

                $uimg = get_user_meta($user_id, 'ocp_shr_pic', true);
                $image = wp_get_attachment_image_src($uimg, 'thumbnail');
                $user_image = "custom_img";
            } elseif ($image_option == 'gavatar') {

                $user_image = "gavatar";
            } elseif ($image_option == 'bp_img') {

                $user_image = 'bp_img';
            } elseif ($image_option == "noimage") {
                $user_image = "";
            } else {
                $user_image = $user_image;
            }
        }

        if (!empty($uimg) && $user_image == "custom_img") {

            $user_image_html = '<img src="' . $image[0] . '">';

        } elseif ($user_image == "gavatar") {

            $user_image_html = '<img src="' . get_avatar_url($user_id) . '" />';

        } elseif ($user_image == "bp_img") {

            $user_image_html = ' <img src="' . bp_get_displayed_user_avatar(array(
                'item_id' => $user_id,
                'type' => 'full',
                'html' => false)) . '" />';

        } else {

        }

        echo $user_image_html = apply_filters('org_user_image', $user_image_html);

    }

    public function user_description($user_id)
    {

        $user_desc = '';
        $user_desc = get_the_author_meta('description', $user_id);

        return $user_desc = apply_filters('org_user_desc', $user_desc);
    }

} // End Class