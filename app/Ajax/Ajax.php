<?php
namespace WPSL\Ajax;

class Ajax {

    public function register() {
        $request_handlers =  $this->get_ajax_handlers();
        $this->register_ajax_handlers( $request_handlers );
    }

    /**
     * Get Request Handlers
     *
     * @return array
     */
    public function get_ajax_handlers() {
        return [
            'wpsl_login_request_handler' => [
                'callback'   => Login_Request_Handler::class,
                'permission' => 'both',
            ],
        ];
    }

    /**
     * Register Ajax Handlers
     *
     * @return void
     */
    private function register_ajax_handlers( array $handlers ) {
        if ( ! count( $handlers ) ) { return; }

        foreach ( $handlers as $handler => $args ) {
            self::add_ajax_handler( $handler, $args );
        }
    }

    /**
     * Add Ajax Handler
     *
     * @return void
     */
    public static function add_ajax_handler( string $handler = '', array $args = [] ) {
        if ( class_exists( $args['callback'] ) ) {
            if ( method_exists( $args['callback'], 'run' ) ) {
                $class_name = $args['callback'];
                $callback   = new $class_name();
                $permission = ( isset( $args['permission'] ) ) ? $args['permission'] : 'both';

                if ( 'admin' === $permission ) {
                    add_action( "wp_ajax_{$handler}", [$callback , 'run'] );
                }
                
                if ( 'guset' === $permission ) {
                    add_action( "wp_ajax_nopriv_{$handler}", [$callback , 'run'] );
                }
                
                if ( 'both' === $permission ) {
                    add_action( "wp_ajax_{$handler}", [$callback , 'run'] );
                    add_action( "wp_ajax_nopriv_{$handler}", [$callback , 'run'] );
                }
            }
        }
    }
}