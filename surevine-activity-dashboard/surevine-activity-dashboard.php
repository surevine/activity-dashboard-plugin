<?php
/*
Plugin Name: Surevine Realtime Activity Dashboard
Plugin URI: https://github.com/surevine/activity-dashboard-plugin
Description: A plugin to place a realtime activity dashboard / feed on your website
Version: 0.1.0
Author: Surevine Ltd
Author URI: https://www.surevine.com
License: GPLv3
*/

global $wpdb;

include __DIR__ . '/src/DashboardDatabaseInterface.php';
include __DIR__ . '/src/DashboardDatabase.php';
include __DIR__ . '/src/ActivityDashboard.php';

require_once __DIR__ . '/lib/Mustache/Autoloader.php';

$dashboardDatabase  = new DashboardDatabase($wpdb, $wpdb->prefix.'dashboard_activities');
$activityDashboard = new ActivityDashboard($dashboardDatabase, __DIR__);

add_shortcode('surevine-activity-dashboard', array($activityDashboard, 'display'), 1);
register_activation_hook(__FILE__, array($activityDashboard, 'activate'), 1);

if (true === is_admin()) {
    add_action('admin_menu', array($activityDashboard, 'adminMenu'), 1);
}

add_action('the_posts', array($activityDashboard, 'isDashboardPage'));

add_action('wp_ajax_load_activities', array($activityDashboard, 'ajax_load_activities'));
add_action('wp_ajax_nopriv_load_activities', array($activityDashboard, 'ajax_load_activities'));
