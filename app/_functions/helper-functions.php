<?php
/**
 * Template Loader
 *
 * @since 1.0
 */
function wpsl_load_template( string $template_path = '', $data = [], string $return_type = 'echo' ) {
    $path = WPSL_PLUGIN_PATH . "templates/$template_path.php";

    ob_start();

    if ( file_exists( $path ) ) {
        include $path;
    }

    $content = ob_get_clean();

    if ( 'echo' !== $return_type ) {
        return $content;
    }

    echo $content;
};

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @since 1.0
 * @param string $string String to convert.
 * @return bool
 */
function wpsl_string_to_bool( $string ) {
    return is_bool( $string ) ? $string : ( 'yes' === strtolower( $string ) || 1 === $string || 'true' === strtolower( $string ) || '1' === $string );
}

// wpsl_social_login
function wpsl_social_login( array $atttibutes = [] ) {
    WPSL\Shortcodes\WP_Social_Login::render( $atttibutes );
}
