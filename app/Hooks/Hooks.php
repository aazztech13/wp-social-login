<?php
namespace WPSL\Hooks;

class Hooks {
    public $hooks = [];

    public function register() {
        $this->hooks =  $this->get_hooks();
        $this->register_hooks( $this->hooks );
    }

    /**
     * Get Action Hooks
     *
     * @return array
     */
    public function get_hooks() {
        return [
            'hook_id' => [
                'hook_name' => 'the_title',
                'type'      => 'action',
                'callback'  => Hook_Callback::class,
                'priority'  => 10,
                'args'      => 2,
            ],
        ];
    }

    /**
     * Register Hooks
     *
     * @return void
     */
    private function register_hooks( array $hooks ) {
        if ( ! count( $hooks ) ) { return; }

        foreach ( $hooks as $hook ) {
            self::add_hook( $hook );
        }
    }

    /**
     * Add Hook
     *
     * @return void
     */
    public static function add_hook( array $hook = [] ) {
        if ( class_exists( $hook['callback'] ) ) {
            if ( method_exists( $hook['callback'], 'run' ) ) {
                $class_name    = $hook['callback'];
                $callback      = new $class_name();
                $priority      = ( isset( $hook['priority'] ) ) ? $hook['priority'] : 10;
                $accepted_args = ( isset( $hook['args'] ) ) ? $hook['args'] : 1;

                if ( 'action' === $hook['type'] ) {
                    add_action( $hook['name'], [$callback, 'run'], $priority, $accepted_args );
                }

                if ( 'filter' === $hook['type'] ) {
                    add_filter( $hook['name'], [$callback, 'run'], $priority, $accepted_args );
                }

            }
        }
    }
}