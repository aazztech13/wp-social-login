<?php
/**
 * WP Social Login
 *
 * @package           WP_Social_Login
 * @author            AazzTech
 * @copyright         2020 AazzTech
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       WP Social Login
 * Description:       A social login plugin for wordpress
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            AazzTech
 * Author URI:        https://directorist.com/
 * Text Domain:       wp-social-login
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */


defined( 'ABSPATH' ) || exit;

// Load Config
$config = plugin_dir_path( __FILE__ ) . '/config.php';
if ( file_exists( $config  ) ) {
    require_once( $config );
}

// Load The App
$app = WPSL_PLUGIN_PATH . 'app/app.php';
if ( ! class_exists( 'WP_Social_Login' ) && file_exists( $app  ) ) {
    require_once( $app );
}

// Load Helper Functions
$helper_functions = WPSL_PLUGIN_PATH . 'app/_functions/helper-functions.php';
if ( file_exists( $helper_functions  ) ) {
    include( $helper_functions );
}