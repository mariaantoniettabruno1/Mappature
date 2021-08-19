<?php

/**
 * Plugin Name: Org Chart Pro
 * Plugin URI: http://wporgchart.com/
 * Description: Build Org chart by dragging users in required order.
 * Version: 1.0.3.10
 * Author: WP OrgChart
 * Author URI: http://wporgchart.com/
 * Text Domain: org-chart
 * @package Org_Chart

 */

if (!defined('ABSPATH')) {
    exit;
}

define( 'EDD_ORG_CHART_URL', 'https://wporgchart.com/' ); // IMPORTANT: change the name of this constant to something unique to prevent conflicts with other plugins using this system
// the download ID. This is the ID of your product in EDD and should match the download ID visible in your Downloads list (see example below)
define( 'EDD_ORG_CHART_ID', 16022 );

define( 'EDD_OCP_ITEM_NAME', 'Org Chart Pro - Life Time updates' );

define( 'EDD_OCP_PLUGIN_LICENSE_PAGE', 'ocp-license' );

if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
// load our custom updater if it doesn't already exist
include dirname( __FILE__ ) . '/lib/EDD_SL_Plugin_Updater.php';
}

function edd_ocp_license_menu() {
	add_submenu_page( 'edit.php?post_type=org_chart', 'License', 'License', 'manage_options', EDD_OCP_PLUGIN_LICENSE_PAGE, 'edd_ocp_license_page', 5 );
}
add_action('admin_menu', 'edd_ocp_license_menu');

$license_key = trim( get_option( 'edd_ocp_license_key' ) );
// setup the updater
$edd_updater = new EDD_SL_Plugin_Updater( EDD_ORG_CHART_URL, __FILE__, array(
  'version' 	=> '1.0.3.10',		// current version number
  'license' 	=> $license_key,	// license key (used get_option above to retrieve from DB)
  'item_id'       => EDD_ORG_CHART_ID,	// id of this plugin
  'author' 	=> 'Org Chart Pro',	// author of this plugin
        'beta'          => false                // set to true if you wish customers to receive update notifications of beta releases
) );

// Exit if accessed directly

function edd_ocp_license_page() {
	$license = get_option( 'edd_ocp_license_key' );
	$status  = get_option( 'edd_ocp_license_status' );
	?>
	<div class="wrap">
		<h2><?php _e('WP Org Chart License'); ?></h2>
		<form method="post" action="options.php">

			<?php settings_fields('edd_ocp_license'); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e('License Key'); ?>
						</th>
						<td>
							<input id="edd_ocp_license_key" name="edd_ocp_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
							<label class="description" for="edd_ocp_license_key"><?php _e('Enter your license key'); ?></label>
						</td>
					</tr>
					<?php if( false !== $license ) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('Activate License'); ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 'valid' ) { ?>
									<span style="color:green;"><?php _e('active'); ?></span>
									<?php wp_nonce_field( 'edd_ocp_nonce', 'edd_ocp_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
								<?php } else {
									wp_nonce_field( 'edd_ocp_nonce', 'edd_ocp_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License'); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php submit_button(); ?>

		</form>
	<?php
}

function edd_ocp_register_option() {
	// creates our settings in the options table
	register_setting('edd_ocp_license', 'edd_ocp_license_key', 'edd_sanitize_license' );
}
add_action('admin_init', 'edd_ocp_register_option');

function edd_sanitize_license( $new ) {
	$old = get_option( 'edd_ocp_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'edd_ocp_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}



/************************************
* this illustrates how to activate
* a license key
*************************************/

function edd_ocp_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_activate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'edd_ocp_nonce', 'edd_ocp_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'edd_ocp_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'  => 'activate_license',
			'license'     => $license,
			'item_name'   => urlencode( EDD_OCP_ITEM_NAME ), // the name of our product in EDD
			'url'         => home_url(),
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_ORG_CHART_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled' :
					case 'revoked' :

						$message = __( 'Your license key has been disabled.' );
						break;

					case 'missing' :

						$message = __( 'Invalid license.' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), EDD_OCP_ITEM_NAME );
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.' );
						break;

					default :

						$message = __( 'An error occurred, please try again.' );
						break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'plugins.php?page=' . EDD_OCP_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"

		update_option( 'edd_ocp_license_status', $license_data->license );
		wp_redirect( admin_url( 'plugins.php?page=' . EDD_OCP_PLUGIN_LICENSE_PAGE ) );
		exit();
	}
}
add_action('admin_init', 'edd_ocp_activate_license');


/***********************************************
* Illustrates how to deactivate a license key.
* This will decrease the site count
***********************************************/

function edd_ocp_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_deactivate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'edd_ocp_nonce', 'edd_ocp_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'edd_ocp_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action'  => 'deactivate_license',
			'license'     => $license,
			'item_name'   => urlencode( EDD_OCP_ITEM_NAME ), // the name of our product in EDD
			'url'         => home_url(),
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_ORG_CHART_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

			$base_url = admin_url( 'plugins.php?page=' . EDD_OCP_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' ) {
			delete_option( 'edd_ocp_license_status' );
		}

		wp_redirect( admin_url( 'plugins.php?page=' . EDD_OCP_PLUGIN_LICENSE_PAGE ) );
		exit();

	}
}
add_action('admin_init', 'edd_ocp_deactivate_license');


/************************************
* this illustrates how to check if
* a license key is still valid
* the updater does this for you,
* so this is only needed if you
* want to do something custom
*************************************/

function edd_ocp_check_license() {

	global $wp_version;

	$license = trim( get_option( 'edd_ocp_license_key' ) );

	$api_params = array(
		'edd_action'  => 'check_license',
		'license'     => $license,
		'item_name'   => urlencode( EDD_OCP_ITEM_NAME ),
		'url'         => home_url(),
		'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
	);

	// Call the custom API.
	$response = wp_remote_post( EDD_ORG_CHART_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	if ( is_wp_error( $response ) )
		return false;

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if( $license_data->license == 'valid' ) {
		echo 'valid'; exit;
		// this license is still valid
	} else {
		echo 'invalid'; exit;
		// this license is no longer valid
	}
}

/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function edd_ocp_admin_notices() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

		switch( $_GET['sl_activation'] ) {

			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo $message; ?></p>
				</div>
				<?php
				break;

			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;

		}
	}
}
add_action( 'admin_notices', 'edd_ocp_admin_notices' );

/**

 * Returns the main instance of Org_Chart to prevent the need to use globals.

 *

 * @since  1.0.0

 * @return object Org_Chart

 */

function Org_Chart()
{

    return Org_Chart::instance();
} // End Org_Chart()

add_action('plugins_loaded', 'Org_Chart');

/**

 * Main Org_Chart Class

 *

 * @class Org_Chart

 * @version    1.0.0

 * @since 1.0.0

 * @package    Org_Chart

 * @author Matty

 */

final class Org_Chart
{

    /**

     * Org_Chart The single instance of Org_Chart.

     * @var     object

     * @access  private

     * @since     1.0.0

     */

    private static $_instance = null;

    /**

     * The token.

     * @var     string

     * @access  public

     * @since   1.0.0

     */

    public $token;

    /**

     * The version number.

     * @var     string

     * @access  public

     * @since   1.0

     */

    public $version;

    /**

     * The plugin directory URL.

     * @var     string

     * @access  public

     * @since   1.0.0

     */

    public $plugin_url;

    /**

     * The plugin directory path.

     * @var     string

     * @access  public

     * @since   1.0.0

     */

    public $plugin_path;

    // Admin - Start

    /**

     * The admin object.

     * @var     object

     * @access  public

     * @since   1.0.0

     */

    public $admin;

    /**

     * The settings object.

     * @var     object

     * @access  public

     * @since   1.0.0

     */

    public $settings;

    /**

     * The template object.

     * @var     object

     * @access  public

     * @since   1.0.0

     */

    public $templates;

    // Admin - End

    // Post Types - Start

    /**

     * The post types we're registering.

     * @var     array

     * @access  public

     * @since   1.0

     */

    public $post_types = array();

    // Post Types - End

    /**

     * Constructor function.

     * @access  public

     * @since   1.0

     */

    public function __construct()
    {

        // require_once plugin_dir_path(__FILE__) . 'lib/wp-package-updater/class-wp-package-updater.php';
        //
        // $dummy_plugin = new WP_Package_Updater(
        //     'https://wporgchart.com',
        //     wp_normalize_path(__FILE__),
        //     wp_normalize_path(plugin_dir_path(__FILE__))
        // );

        $this->token = 'org-chart';

        $this->plugin_url = plugin_dir_url(__FILE__);

        $this->plugin_path = plugin_dir_path(__FILE__);

        $this->version = '1.0.3.6';

        // Emqueue Script & Stylehseets
        add_action('admin_enqueue_scripts', array($this, 'orgchart_styles'));
        add_action('admin_enqueue_scripts', array($this, 'orgchart_enqueue'));

        // Admin - Start
        require_once 'classes/class-org-chart-settings.php';
        $this->settings = Org_Chart_Settings::instance();

        if (is_admin()) {
            require_once 'classes/class-org-chart-admin.php';
            $this->admin = Org_Chart_Admin::instance();
        }

        // Admin - End

        // Template - Start

        require_once 'classes/class-org-chart-templates.php';
        $this->templates = Org_Chart_Templates::instance();

        // Intilialize Shortcode

        add_shortcode('orgchart', array($this, 'orgchart_display'));

        // Ajax Request Setup

        add_action('wp_ajax_generate_chart', array($this, 'generate_chart'));
        //add_action('wp_ajax_nopriv_generate_chart', array($this, 'generate_chart'));

        add_action('wp_ajax_save_chart', array($this, 'save_chart'));
        //add_action('wp_ajax_nopriv_save_chart', array($this, 'save_chart'));

        //  Post Type Include
        require_once 'classes/class-org-chart-post-type.php';

        // Register an  post type. To register other post types, duplicate this line.

        $this->post_types['org_chart'] = new Org_Chart_Post_Type('org_chart', __('Org Chart', 'org-chart'), __('Org Charts', 'org-chart'), array('menu_icon' => 'dashicons-image-filter'));

        register_activation_hook(__FILE__, array($this, 'install'));
        register_deactivation_hook(__FILE__, array($this, 'uninstall'));

        // JSON url register
        add_action('rest_api_init', array($this, 'org_custom_route'));

        // Activate default settings
        add_action('init', array($this, 'default_settings'));
        add_action('init', array($this, 'load_plugin_textdomain'));
    } // End __construct()

    /**

     * Main Org_Chart Instance

     *

     * Ensures only one instance of Org_Chart is loaded or can be loaded.

     *

     * @since 1.0.0

     * @static

     * @see Org_Chart()

     * @return Main Org_Chart instance

     */

    public static function instance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    } // End instance()

    /**

     * Load the stylesheet files.

     * @access  public

     * @since   1.0.0

     */

    public function orgchart_styles()
    {

        if (is_admin()) {
            $current_page = get_current_screen()->post_type;
            $post_types = array("org_chart");
            if (in_array($current_page, $post_types)) {
                wp_enqueue_style('orgchart-style1', plugin_dir_url(__FILE__) . 'assets/css/jquery.jOrgChart.css');
                wp_enqueue_style('orgchart-style2', plugin_dir_url(__FILE__) . 'assets/css/custom.css');
                wp_enqueue_style('FontAwesome', plugin_dir_url(__FILE__) . 'assets/css/font-awesome.min.css');
                wp_enqueue_style('magnific-popup', plugin_dir_url(__FILE__) . 'assets/css/magnific.css');
                wp_register_style('select2css', plugin_dir_url(__FILE__) . 'assets/css/select2.css', false, '1.0', 'all');
                wp_enqueue_style('select2css');
            }
        } else {
            wp_enqueue_style('orgchart-style1', plugin_dir_url(__FILE__) . 'assets/css/jquery.jOrgChart.css');
            wp_enqueue_style('orgchart-style2', plugin_dir_url(__FILE__) . 'assets/css/custom.css');
            wp_enqueue_style('FontAwesome', plugin_dir_url(__FILE__) . 'assets/css/font-awesome.min.css');
            wp_enqueue_style('magnific-popup', plugin_dir_url(__FILE__) . 'assets/css/magnific.css');
        }
    }

    /**

     * Load the script files.

     * @access  public

     * @since   1.0.0

     */

    public function orgchart_enqueue()
    {

        // wp_enqueue_script('jquery-ui-draggable');

        if (is_admin()) {
            wp_enqueue_media();
            wp_enqueue_script('jquery-ui-droppable');
            $get_base = get_current_screen()->base;
            if (in_array($get_base, array("user", "user-edit", "profile"))) {
                wp_enqueue_script('org_user_edit', plugin_dir_url(__FILE__) . 'assets/js/user-edit.js');
            }
            $current_page = get_current_screen()->post_type;
            $post_types = array("org_chart");
            if (in_array($current_page, $post_types)) {

                wp_enqueue_script('magnific-popup', plugin_dir_url(__FILE__) . 'assets/js/magnific.min.js');
                wp_register_script('select2', plugin_dir_url(__FILE__) . 'assets/js/select2.js', array('jquery'), '1.0', true);
                wp_enqueue_script('select2');
                wp_enqueue_script('org_cha', plugin_dir_url(__FILE__) . 'assets/js/jOrgChart.min.js');
                wp_enqueue_script('org_cha1', plugin_dir_url(__FILE__) . 'assets/js/custom.min.js');
                wp_enqueue_script('org_htm2canvas', plugin_dir_url(__FILE__) . 'assets/js/html2canvas.min.js');
                wp_enqueue_script('org_filesaver', plugin_dir_url(__FILE__) . 'assets/js/FileSaver.min.js');
                wp_enqueue_script('org_canvas2blog', plugin_dir_url(__FILE__) . 'assets/js/canvas-toBlob.min.js');
                wp_enqueue_script('org_print', plugin_dir_url(__FILE__) . 'assets/js/print.js');
            }
        } else {
            wp_enqueue_script('jquery-ui-droppable');
            wp_enqueue_script('magnific-popup', plugin_dir_url(__FILE__) . 'assets/js/magnific.min.js');
            wp_enqueue_script('org_back_js', plugin_dir_url(__FILE__) . 'assets/js/front.jOrgChart.min.js');
            wp_enqueue_script('org_front_js', plugin_dir_url(__FILE__) . 'assets/js/front_custom.min.js');
            wp_enqueue_script('org_htm2canvas', plugin_dir_url(__FILE__) . 'assets/js/html2canvas.min.js');
            wp_enqueue_script('org_filesaver', plugin_dir_url(__FILE__) . 'assets/js/FileSaver.min.js');
            wp_enqueue_script('org_canvas2blog', plugin_dir_url(__FILE__) . 'assets/js/canvas-toBlob.min.js');
            wp_enqueue_script('org_print', plugin_dir_url(__FILE__) . 'assets/js/print.js');
        }
    }

    public function default_settings()
    {

        //$templates =array('Default','New 1','New 2','New 3');

        $templates = 'default';
        add_option('org_chart_templates', $templates);
    }

    public function orgchart_display($atts)
    {
        $output = "";
        $id = $atts['id'];
        $this->orgchart_styles();
        $this->orgchart_enqueue();
        $print_option = get_post_meta($id, 'org_print_option', true);
        $user_link_setting = get_option('org-chart-welcome-section', array());
        $org_chart_template = 'default';
        $org_chart_template = strtolower($org_chart_template);
        $org_array = get_post_meta($id, 'org_array', true);
        //print_r($org_array);

        $tree = unserialize($org_array);
        //print_r($tree);
        $result = $this->templates->parseTree($tree);
        $print_chart = "";

        if (isset($print_option)) {
            if ($print_option == "yes") {
                $print_chart = "yes";
            } elseif ($print_option == "no") {
                $print_chart = "";
            } elseif ($print_option == "global") {
                if (isset($user_link_setting['print_chart'])) {
                    $print_chart = "yes";
                } else {
                    $print_chart = "no";
                }
            }
        }

        if ($print_chart == "yes") {

            $output .= '<a class="button button-primary print_chart_front" data-chartid="' . $id . '" id="print_chart_' . $id . '" href="#">
   ' . __("Print Chart", "org-chart") . '</a>';
        }
        $output .= '<ul id="org_' . $id . '" class="ocp_pro" style="display:none">';
        $output .= $this->templates->printTree($result, 0, $org_chart_template, $id);
        $output .= '</ul>';
        $output .= '<div id="chart_' . $id . '" data-chartid="' . $id . '" class="orgChart chart_' . $id . '"></div>';
        $output .= '<canvas id="print_canvas" width="100%" height="100" style="opacity:0"></canvas>';

        return $output;
    }

    /**

     * Load the localisation file.

     * @access public

     * @since 1.0.0

     */

    public function load_plugin_textdomain()
    {

        load_plugin_textdomain('org-chart', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    } // End load_plugin_textdomain()

    /**

     * Cloning is forbidden.

     * @access public

     * @since 1.0.0

     */

    public function __clone()
    {

        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), '1.0');
    } // End __clone()

    /**

     * Unserializing instances of this class is forbidden.

     * @access public

     * @since 1.0.0

     */

    public function __wakeup()
    {

        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), '1.0');
    } // End __wakeup()

    /**

     * Installation. Runs on activation.

     * @access public

     * @since 1.0.0

     */

    public function install()
    {

        $this->_log_version_number();
    } // End install()

    public function uninstall()
    {

        delete_option('org_chart_templates');
    }

    /**

     * Log the plugin version number.

     * @access private

     * @since 1.0.0

     */

    private function _log_version_number()
    {

        // Log the version number.

        update_option($this->token . '-version', $this->version);
    } // End _log_version_number()

    /**

     * Reset Top Level of Chart

     * @access private

     * @since 1.0.0

     */

    public function generate_chart()
    {

        $users = get_users();

        foreach ($users as $user) {
            delete_user_meta($user->ID, 'top_org_level');
        }

        update_user_meta($_POST['user_dropdown'], "top_org_level", 1);

        $user_query0 = new WP_User_Query(array('meta_key' => 'top_org_level', 'meta_value' => 1));

        if (!empty($user_query0->results)) {

            foreach ($user_query0->results as $user) {
                $top_level_id = $user->ID;
                $top_level = $user->display_name;
            }
        }

        $otree = '';
        if (get_the_author_meta('description', $top_level_id) != '') {
            $user_b = $user_b;
        } else {
            $user_b = '';
        }

        echo '<ul id="org" style="display:none">';
        $org_chart_template = 'default';
        $active_user_id = $top_level_id;
?>
        <li id='<?php echo $active_user_id; ?>'>
            <?php
            /* Load Templates  */
            if (file_exists(get_stylesheet_directory() . "/org-chart/default.php")) {
                wp_enqueue_style('orgchart-template-style', get_stylesheet_directory() . '/org-chart/style.css');
                include get_stylesheet_directory() . '/org-chart/default.php';
            } else {

                wp_enqueue_style('orgchart-template-style', plugin_dir_url(__FILE__) . '/templates/style.css');
                include plugin_dir_path(__FILE__) . '/templates/default.php';
            }
            if ($_POST['setchildren'] != "no") {
                echo '<ul class="2">';

                $user_query1 = new WP_User_Query(array('exclude' => array($top_level_id), 'meta_key' => 'ocp_team_member', 'meta_value' => 'yes'));

                if (!empty($user_query1->results)) {

                    $user_b = '';

                    foreach ($user_query1->results as $user) {

                        $org_job_title = get_user_meta($user->ID, 'ocp_job_title', true);
                        $uimg = get_user_meta($user->ID, 'ocp_shr_pic', true);
                        $image = wp_get_attachment_image_src($uimg, 'thumbnail');
                        $user_data = get_user_by('id', $user->ID);
                        $org_role = get_user_meta($user->ID, 'ocp_job_title', true);
                        $org_date = date("m Y", strtotime(get_userdata($user->ID)->user_registered));
                        $description = nl2br(get_the_author_meta('description', $top_level_id));

                        if (get_the_author_meta('description', $user->ID) != '') {

                            $user_b = $user_b;
                        } else {

                            $user_b = '';
                        }

                        /* Store Values for Template */

                        $active_user_id = $user->ID;
                        $active_user_role = $org_role;
                        $active_user_name = $user_data->display_name;
                        $active_user_description = $description;
            ?>
        <li id='<?php echo $active_user_id; ?>'>
<?php
                        if (!empty($uimg)) {

                            $active_user_image = '<img src="' . $image[0] . '">';
                        } else {
                            // $active_user_image = get_avatar($top_level_id);
                        }

                        /* Load Templates  */

                        if (file_exists(get_stylesheet_directory() . "/org-chart/default.php")) {
                            wp_enqueue_style('orgchart-template-style', get_stylesheet_directory_uri() . '/org-chart/style.css');
                            include get_stylesheet_directory() . '/org-chart/default.php';
                        } else {

                            wp_enqueue_style('orgchart-template-style', plugin_dir_url(__FILE__) . '/templates/style.css');
                            include plugin_dir_path(__FILE__) . '/templates/default.php';
                        }

                        echo "</li>";
                    }
                }
                $otree .= '</ul>';
            }
            $otree .= '</li></ul>';

            echo '<div id="chart" class="orgChart">' . $otree . '</div>';
        }

        /**

         * Save Chart For individual Post

         * @access  private

         * @since   1.0.0

         */

        public function save_chart()
        {

            $tree = array();
            foreach ($_POST['tree'] as $val) {
                foreach ($val as $va => $v) {
                    $tree[$va] = $v;
                }
            }

            if (!is_serialized($tree)) :
                $tree = serialize($tree);

            endif;

            if (get_post_meta($_POST['post_id'], 'org_array', true)) {
                //update_option('org_array', $tree, 'no');
                update_post_meta($_POST['post_id'], 'org_array', $tree);
            } else {

                //add_option('org_array', $tree, '', 'no');
                add_post_meta($_POST['post_id'], 'org_array', $tree, true);
            }

            die();
        }

        // JSON endpoint

        public function make_json($data)
        {
            $posts = get_posts(array(
                'p' => $data['id'],
                'post_type' => 'org_chart',
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'org_json',
                        'value' => 1,
                        'compare' => '=',
                    ),
                )
            ));

            if (empty($posts)) {
                return null;
            }

            return $this->org_json($data['id']);
            // return $posts[0];
        }

        public function make_json_forall($data)
        {
            $posts = get_posts(array(
                'post_type' => 'org_chart',
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'org_json',
                        'value' => 1,
                        'compare' => '=',
                    ),
                ),
            ));

            if (empty($posts)) {
                return null;
            }

            return $posts;
        }

        public function org_custom_route()
        {
            register_rest_route('orgchart/v1', '/chart/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'make_json'),
            ));

            register_rest_route('orgchart/v1', '/chart/', array(
                'methods' => 'GET',
                'callback' => array($this, 'make_json_forall'),
            ));
        }

        public function org_json_route()
        {
            register_rest_route(
                'orgchart',
                'json',
                array(
                    'methods' => 'GET',
                    'callback' => $this->org_json,
                )
            );
        }

        private function org_json($post_id)
        {
            $org_array = get_post_meta($post_id, 'org_array', true);
            $tree = unserialize($org_array);
            return $result = $this->parseJSON($tree);
            rest_ensure_response($result);
        }

        private function parseJSON($tree, $root = null)
        {
            $return = array();
            # Traverse the tree and search for direct children of the root
            foreach ($tree as $child => $parent) {
                # A direct child is found
                if ($parent == $root) {
                    # Remove item from tree (we don't need to traverse this again)
                    unset($tree[$child]);
                    # Append the child into result array and parse its children
                    $user_info = get_userdata($child);
                    //var_dump($user_info);
                    $return[] = array(
                        'id' => $child,
                        'role' => get_user_meta($child, 'org_job_title', true),
                        'name' => get_user_meta($child, 'first_name', true),
                        'children' => $this->parseJSON($tree, $child),
                    );
                }
            }
            return empty($return) ? null : $return;
        }
    } // End Class
