<?php
namespace WPSL\Admin_Menu;

class Admin_Menu_Pages {
    public $menu_pages = [];

    /**
     * Register
     *
     * @return void
     */
    public function register() {
        $this->set_menu_pages();
        add_action( 'admin_menu', [ $this, 'register_menu_pages' ] );
    }

    /**
     * Set Menu Pages
     *
     * @return void
     */
    public function set_menu_pages() {
        $this->menu_pages = apply_filters( 'wpsl_menu_pages', [
            'main_menu' => [
                'page_title' => 'WP Social Login',
                'menu_title' => 'WP Social Login',
                'capability' => 'manage_options',
                'menu_slug'  => 'wpsl-settings',
                'function'   => MainMenuCallback::class,
                'icon_url'   => 'dashicons-migrate',
                'position'   => 10,
                'submenu' => [
                    'settings' => [
                        'menu_slug' => 'wpsl-settings',
                    ],
                ]
            ]
        ]);
    }

    /**
     * Register Menu Pages
     *
     * @return void
     */
    public function register_menu_pages() {
        foreach ( $this->menu_pages as $menu_page ) {
            $class_name = $menu_page['function'];
            $class = new $class_name();

            $page_title = ( ! empty( $menu_page['page_title'] ) ) ? $menu_page['page_title'] : '';
            $menu_title = ( ! empty( $menu_page['menu_title'] ) ) ? $menu_page['menu_title'] : '';
            $capability = ( ! empty( $menu_page['capability'] ) ) ? $menu_page['capability'] : '';
            $menu_slug  = ( ! empty( $menu_page['menu_slug'] ) ) ? $menu_page['menu_slug'] : '';
            $icon_url   = ( ! empty( $menu_page['icon_url'] ) ) ? $menu_page['icon_url'] : '';
            $position   = ( ! empty( $menu_page['position'] ) ) ? $menu_page['position'] : null;
            $callback   = '';

            if ( ! empty( $menu_page['function'] ) && class_exists( $menu_page['function'] ) ) {
                $class_name = $menu_page['function'];
                $class = new $class_name();

                if ( method_exists( $class_name, 'render' ) ) {
                    $callback = [ $class, 'render' ];
                }
            }
            
            if (
                ! empty( $page_title ) && 
                ! empty( $menu_title ) && 
                ! empty( $capability ) && 
                ! empty( $menu_slug )
            ) {
                add_menu_page(
                    $page_title,
                    $menu_title,
                    $capability,
                    $menu_slug,
                    $callback,
                    $icon_url,
                    $position,
                );
            }

            // If has submenu
            if ( ! empty( $menu_page['submenu'] ) ) {
                foreach ( $menu_page['submenu'] as $submenu ) {
                    $parent_slug    = $menu_slug;
                    $sub_menu_slug  = ( ! empty( $submenu['menu_slug'] ) ) ? $submenu['menu_slug'] : '';
                    $sub_page_title = ( ! empty( $submenu['page_title'] ) ) ? $submenu['page_title'] : '';
                    $sub_menu_title = ( ! empty( $submenu['menu_title'] ) ) ? $submenu['menu_title'] : '';
                    $sub_capability = ( ! empty( $submenu['capability'] ) ) ? $submenu['capability'] : '';
                    $sub_position   = ( ! empty( $submenu['position'] ) ) ? $submenu['position'] : null;
                    $sub_callback   = '';
                    
                    if ( ! empty( $submenu['function'] ) && class_exists( $submenu['function'] ) ) {
                        $sub_class_name = $submenu['function'];
                        $sub_class = new $sub_class_name();

                        if ( method_exists( $sub_class_name, 'render' ) ) {
                            $sub_callback = [ $sub_class, 'render' ];
                        }
                    }

                    if ( $parent_slug === $sub_menu_slug ) {
                        $sub_page_title = empty( $sub_page_title ) ? $page_title : $sub_page_title;
                        $sub_menu_title = empty( $sub_menu_title ) ? $menu_title : $sub_menu_title;
                        $sub_capability = empty( $sub_capability ) ? $capability : $sub_capability;
                        $sub_callback   = '';
                    }

                    if ( 
                        ! empty( $parent_slug ) && 
                        ! empty( $sub_page_title ) && 
                        ! empty( $sub_menu_title ) && 
                        ! empty( $sub_capability ) && 
                        ! empty( $sub_menu_slug )
                    ) {
                        add_submenu_page(
                            $parent_slug,
                            $sub_page_title,
                            $sub_menu_title,
                            $sub_capability,
                            $sub_menu_slug,
                            $sub_callback,
                            $sub_position,
                        );
                    }
                }
            }
        }
    }
}