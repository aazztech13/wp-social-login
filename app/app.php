<?php

defined( 'ABSPATH' ) || exit;

$autoloader = WPSL_PLUGIN_PATH . 'app/autoloader.php';

if ( file_exists( $autoloader ) ) {
    require_once $autoloader;
}

use WPSL\Assets\Enqueue;
use WPSL\Ajax\Ajax;
use WPSL\Admin_Menu\Admin_Menu_Pages;
use WPSL\Shortcodes\Shortcodes;

final class WP_Social_Login {
    /**
     * Instance
     *
     * @return WP_Social_Login
     */
    protected static $instance = null;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        add_action( 'init', [$this, 'register_services'] );
    }

    /**
     * Instance
     *
     * @return WP_Social_Login
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get Services
     *
     * @return array
     */
    public function get_services() {
        return [
            Enqueue::class,
            Ajax::class,
            Admin_Menu_Pages::class,
            Shortcodes::class,
        ];
    }

    /**
     * Register Services
     *
     * @return void
     */
    public function register_services() {
        $services = $this->get_services();

        if ( ! count( $services ) ) {return;}

        foreach ( $services as $class_name ) {
            if ( class_exists( $class_name ) ) {
                if ( method_exists( $class_name, 'register' ) ) {
                    $service = new $class_name();
                    $service->register();
                }
            }
        }
    }
}

function WP_Social_Login() {
    return WP_Social_Login::instance();
}

WP_Social_Login();