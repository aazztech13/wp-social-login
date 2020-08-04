<?php
namespace WPSL\Admin_Menu;

class MainMenuCallback {
    private $form_fields = [];

    /**
     * Render
     *
     * @return void
     */
    public function render() {
        $this->set_form_fields();
        $this->handle_requests();
        $this->load_form();
    }

    private function set_form_fields() {
        $this->form_fields = apply_filters( 'wpsl_main_menu_fields', [
            'wpsl_facebook_api_key' => [
                'label' => 'Facebook API Key',
                'value' => get_option( 'wpsl_facebook_api_key', '' )
            ],
            'wpsl_google_api_key' => [
                'label' => 'Google API Key',
                'value' => get_option( 'wpsl_google_api_key', '' ),
            ],
        ]);
    }

    /**
     * Handle Requests
     *
     * @return void
     */
    private function handle_requests() {
        $updated = false;

        foreach ( $this->form_fields as $field_name => $field ) {
            if ( isset( $_POST[ $field_name ] ) ) {
                update_option( $field_name, $_POST[ $field_name ] );
                $this->form_fields[ $field_name ]['value'] = $_POST[ $field_name ];
                $updated = true;
            }
        }
        
        if ( $updated ) {
            $this->show_alert();
        }
    }

    /**
     * Load Form
     *
     * @return void
     */
    private function load_form() {
        $data = [
            'form_fields' => $this->form_fields
        ];

        wpsl_load_template( 'menu_pages\main-menu', $data );
    }

    /**
     * Show Alert
     *
     * @return void
     */
    private function show_alert( array $options = [] ) {
        $message        = isset( $options['message'] ) ? $options['message'] : 'Settings saved.';
        $type           = isset( $options['type'] ) ? ' ' . $options['type'] : ' updated';
        $is_dismissible = isset( $options['is_dismissible'] ) ? ' ' . $options['is_dismissible'] : ' is-dismissible';

        echo "<div class='notice{$type}{$is_dismissible}'><p><b>{$message}</b></p></div>";
    }
}