<?php

if (!defined('ABSPATH')) {

    exit;

}

/* Variables available :
 * $active_user_id - user id of particular node.
 * $chart_id - chart id (custom post type chart)
 */

/* All actions are defined in org-chart/classes/class-org-chart-templates.php */

/* Popup html start */

/* Area before popup description */
do_action('before_popup_content', $active_user_id);

/**
 * Hook: popup_content.
 *
 * @hooked array(Org_Chart_Templates::instance(), 'show_name_title') - 5
 * @hooked array(Org_Chart_Templates::instance(), 'show_description') - 10
 */

do_action('popup_content', $chart_id, $active_user_id); // Popup Content

/* Area after popup description */
do_action('after_popup_content', $active_user_id);

/* Popup html end */

/* Node html start */
do_action('before_org_node', $chart_id, $active_user_id);

/**
 * Hook: org_node.
 *
 * @hooked array(Org_Chart_Templates::instance(), 'user_image') - 5
 * @hooked array(Org_Chart_Templates::instance(), 'node_content') - 10
 */

do_action('org_node', $chart_id, $active_user_id);

do_action('after_org_node', $chart_id, $active_user_id);

/* Node html end */