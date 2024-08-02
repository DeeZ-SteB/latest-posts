<?php
/**
 * Plugin Name: Latest Posts Test Task
 * Description: A widget to display latest posts with ratings.
 * Version: 1.0
 * Author: Steblyuk Kostya
 */

if (!defined('ABSPATH')) {
    exit;
}

include(plugin_dir_path(__FILE__) . 'inc/latest-posts-widget.php');

include(plugin_dir_path(__FILE__) . 'inc/rating-system.php');

function lprw_register_widgets() {
    register_widget('LPRW_Latest_Posts_Widget');
}
add_action('widgets_init', 'lprw_register_widgets');

function lprw_enqueue_scripts() {
    wp_enqueue_style('lprw-styles', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('lprw-scripts', plugin_dir_url(__FILE__) . 'assets/js/script.js', '', '', true);
    wp_localize_script('lprw-scripts', 'lprw_ajax', array('url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'lprw_enqueue_scripts');

