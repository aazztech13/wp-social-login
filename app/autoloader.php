<?php

defined( 'ABSPATH' ) || exit;

/**
 * Automatically loded the classes
 */
spl_autoload_register( function ( $class_name ) {
    $prefix = 'WPSL\\';

    if ( substr( $class_name, 0, strlen( $prefix ) ) == $prefix ) {
        $class_name = substr( $class_name, strlen( $prefix ) );
    }

    $file_path = WPSL_PLUGIN_PATH . "app/$class_name.php";

    if ( file_exists( $file_path ) ) {
        require_once ( $file_path );
    }
} );