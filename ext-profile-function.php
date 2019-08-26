<?php
/*
Plugin Name: extend_profile_add_field
Description: Плагин разширяеющий профиль пользователя
Version: 1.0
Author: Inna Shapoval
*/

include_once (plugin_dir_path(__FILE__) . 'functions.php');

add_action('show_user_profile', 'ext_profile_change_user_field');
add_action('edit_user_profile', 'ext_profile_change_user_field');

add_action('personal_options_update', 'ext_profile_change_user_update');
add_action('edit_user_profile_update', 'ext_profile_change_user_update');

add_action('edit_category_form_fields', 'ext_profile_category');

add_action( 'wp_insert_post', 'ext_profile_post', 10, 2 );

add_action('wp_enqueue_scripts', 'ext_profile_add_script');

add_filter('the_content', 'ext_profile_content' );

add_filter ('template_include', 'ext_profile_page_template');

add_filter( 'query_vars', 'ext_profile_new_query_vars', 10, 1 );

add_filter('generate_rewrite_rules','ext_profile_when_rewrite_rules');
