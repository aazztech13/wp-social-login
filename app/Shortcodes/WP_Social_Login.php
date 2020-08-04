<?php
namespace WPSL\Shortcodes;

class WP_Social_Login {

    /**
     * Attributes.
     *
     * @since 1.0
     * @var   array
     */
    protected $attributes = [];

    /**
     * Render
     *
     * @return void
     */
    public static function render( $atttibutes = '' ) {
        $data = '';

        if ( is_array( $atttibutes ) ) {
            $data = $atttibutes['show_all'];
        }

        return self::get_contents( $data );
    }

    /**
     * Get Contents
     *
     * @return void
     */
    public static function get_contents( $data = ''  ) {
        return wpsl_load_template( 'shortcodes/login-buttons', '', 'return' );
	}
}