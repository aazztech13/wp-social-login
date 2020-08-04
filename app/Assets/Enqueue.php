<?php
namespace WPSL\Assets;

class Enqueue {
    /**
     * Register
     *
     * @return void
     */
    public function register() {
        $this->load_scripts();
        $this->localize_script();
        add_action( 'wp_footer', [ $this, 'load_api_script' ] );
    }

    /**
     * Get Frontend Styles
     * array params id, src, dep, ver, media
     * @return array
     */
    public function get_frontend_styles() {
        $styles = [
            'bootstrap' => [
                'src'     => '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
                'ver'     => '4.0',
                'desable' => false,
            ],
        ];

        return apply_filters( 'tdapp_frontend_styles', $styles );
    }

    /**
     * Get Frontend Scripts
     * array params id, src, dep, ver, in_footer
     * @return array
     */
    public function get_frontend_scripts() {
        $scripts = [
            'popper'    => [
                'src' => '//cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js',
            ],
            'bootstrap' => [
                'src' => '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js',
                'dep' => ['jquery', 'popper'],
                'ver' => '4.0',
            ],
            'wpsl_main' => [
                'src' => WPSL_JS_URI . 'wpsl-main.js',
                'dep' => ['jquery'],
                'ver' => '1.0',
            ],
        ];

        return apply_filters( 'tdapp_frontend_scripts', $scripts );
    }

    /**
     * Register Styles
     *
     * @return void
     */
    public function register_styles( array $styles ) {
        foreach ( $styles as $id => $args ) {

            if ( ! empty( $args['desable'] ) ) {
                continue;
            }

            $defaults = [
                'src' => '', 'dep' => [], 'ver' => false, 'media' => 'all',
            ];

            $args = array_merge( $defaults, $args );
            wp_register_style( $id, $args['src'], $args['dep'], $args['ver'], $args['media'] );
        }
    }

    /**
     * Register Scripts
     *
     * @return void
     */
    public function register_scripts( array $scripts ) {
        foreach ( $scripts as $id => $args ) {

            if ( ! empty( $args['desable'] ) ) {
                continue;
            }

            $defaults = [
                'src' => '', 'dep' => [], 'ver' => false, 'in_footer' => true,
            ];

            $args = array_merge( $defaults, $args );
            wp_register_script( $id, $args['src'], $args['dep'], $args['ver'], $args['in_footer'] );
        }
    }

    /**
     * Enqueue Styles
     *
     * @return void
     */
    public function enqueue_styles( array $styles ) {
        foreach ( $styles as $id => $args ) {
            wp_enqueue_style( $id );
        }
    }

    /**
     * Enqueue Scripts
     *
     * @return void
     */
    public function enqueue_scripts( array $scripts ) {
        foreach ( $scripts as $id => $args ) {
            wp_enqueue_script( $id );
        }
    }

    /**
     * Upgrade JQuery
     *
     * @return void
     */
    public function upgrade_jquery() {
        wp_dequeue_script( 'jquery' );
        wp_deregister_script( 'jquery' );

        wp_register_script( 'jquery', '//code.jquery.com/jquery-3.5.1.min.js', false, '3.5.1', 'true' );
        wp_enqueue_script( 'jquery' );
    }

    /**
     * Load Scripts
     *
     * @return void
     */
    public function load_scripts() {
        // Load Frontend Styles
        $frontend_styles = $this->get_frontend_styles();
        $this->register_styles( $frontend_styles );
        $this->enqueue_styles( $frontend_styles );

        // Upgrade JQuery
        $upgrade_jquery = apply_filters( 'wpsl_upgrade_jquery', false );
        if ( $upgrade_jquery ) {
            $this->upgrade_jquery();
        }

        // Load Frontend Scripts
        $frontend_scripts = $this->get_frontend_scripts();
        $this->register_scripts( $frontend_scripts );
        $this->enqueue_scripts( $frontend_scripts );
    }

    /**
     * Localize Script
     *
     * @return void
     */
    public function localize_script() {

        // localize wpsl_main script
        $data = array(
            'ajax_url'    => admin_url( 'admin-ajax.php' ),
            'fb_app_id'   => get_option( 'wpsl_facebook_api_key', ''),
            'google_api'  => get_option( 'wpsl_google_api_key', '' ),
            'debug_mode'  => get_option( 'wpsl_debug_mode', true ),
            'error_msg'   => __('Sorry, something went wrong', 'wp-social-login'),
            'success_msg' => __('Login successful, redirecting...', 'wp-social-login'),
            'wait_msg'    => __('Please wait...', 'wp-social-login'),
        );

        wp_localize_script( 'wpsl_main', 'wpsl_options', $data);
    }

    /**
     * Load API Scripts
     *
     * @return void
     */
    public function load_api_script() {
        echo '<script defer src="https://connect.facebook.net/en_US/sdk.js"></script>';
        echo '<script defer src="https://apis.google.com/js/platform.js?onload=initGAPI"></script>';
    }
}
