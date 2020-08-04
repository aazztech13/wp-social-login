<?php
namespace WPSL\Ajax;

class Login_Request_Handler {
    // run
    public function run() {
        $data = $this->parse_data();
        $this->handle_user_data( $data );
    }

    // parse_data
    protected function parse_data() {
        $form_data = [
            'profile_id'      => '',
            'email'           => '',
            'first_name'      => '',
            'last_name'       => '',
            'full_name'       => '',
            'profile_picture' => '',
        ];

        // Get the the values from request
        foreach ( $form_data as $field => $value ) {
            if ( isset( $_POST[ $field ] ) ) {
                $form_data[ $field ] = $_POST[ $field ];
            }
        }

        return $form_data;
    }

    // handle_user_data
    protected function handle_user_data( array $user_data = [] ) {
        $redirect_url = get_option( 'wpsl_redirect_url', get_home_url() );
        $redirect_url = apply_filters( 'wpsl_redirect_url', $redirect_url );

        // Status
        $status = [
            'user_id'      => null,
            'status'       => false,
            'status_log'   => [],
            'redirect_url' => $redirect_url,
        ];

        // Register user
        $user = $this->register_user( $user_data );
    
        if ( ! $user['user_id'] ) {
            if ( isset( $user['status_log']['registration_failed'] ) ) {
                unset( $user['status_log']['registration_failed'] );
                $user['status_log']['login_failed'] =  [
                    'message' => 'Login is failed, please try again...',
                ];
            }

            $status['status']     = false;
            $status['status_log'] = $user['status_log'];

            wp_send_json( $status, 200 );
        }

        // Login the user
        $this->login( $user['user_id'] );

        $status['status']    = true;
        $status['email']     = $user_data['email'];
        $status['user_id']   = $user['user_id'];
        $status['user_name'] = $user['user_name'];

        $status['status_log'][] = [
            'status_key' => 'login_successfull',
            'message'    => 'Login is successfull, redirecting...',
        ];

        wp_send_json( $status, 200 );
    }

    // register_usern
    protected function register_user( array $user_data = [] ) {
        $status = [
            'user_id'    => null,
            'status'     => false,
            'status_log' => [],
        ];

        // Validate Email
        if ( empty( $user_data['email'] ) ) {
            $status['status'] = false;
            $status['status_log']['email_is_required'] = [
                'message' => 'Email is required',
            ];

            return $status;
        }

        $email = sanitize_email( $user_data['email'] );
        if ( ! is_email( $email ) ) {
            $status['status'] = false;
            $status['status_log']['email_is_invalid'] = [
                'message' => 'Email is invalid',
            ];

            return $status;
        }

        // Check if user is already registered
        $email_exists = email_exists( $email );
        $existed_user = $email_exists;

        if ( ! $email_exists && ! empty( $user_data['profile_id'] ) ) {
            $users = get_users(array(
                'meta_key'   => 'wpsl_profile_id',
                'meta_value' => $user_data['profile_id']
            ));

            if ( ! empty( $users ) ) {
                $existed_user = $users[0]->ID;
            }
        }

        if ( $existed_user ) {
            $status['status']  = true;
            $status['user_id'] = $existed_user;
            $status['status_log']['user_exists'] = [
                'message' => 'User is already registered',
            ];

            return $status;
        }

        // Register the user
        $user_name = preg_replace( '/[@].+$/', '', $email );
        $number    = rand( 100, 90000 );
        $user_name = "{$user_name}_{$number}";

        if ( username_exists( $user_name ) ) {
            $status['status'] = false;
            $status['status_log']['username_exists'] = [
                'message' => 'Something went wrong, please try again...',
            ];

            return $status;
        }

        $password = wp_generate_password( 6 );

        $the_user_data = [
            'user_login'   => $user_name,
            'user_email'   => $email,
            'user_pass'    => $password,
            'display_name' => ! empty( $user_data['full_name'] ) ? $user_data['full_name'] : '',
            'first_name'   => ! empty( $user_data['first_name'] ) ? $user_data['first_name'] : '',
            'last_name'    => ! empty( $user_data['last_name'] ) ? $user_data['last_name'] : '',
        ];

        $user_id         = wp_insert_user( $the_user_data );
        $profile_id      = ! empty( $user_data['profile_id'] ) ? $user_data['profile_id'] : '';
        $profile_picture = ! empty( $user_data['profile_picture'] ) ? $user_data['profile_picture'] : '';

        if ( ! $user_id ) {
            $status['status'] = false;
            $status['status_log']['registration_failed'] = [
                'message' => 'Something went wrong, please try again...',
            ];

            return $status;
        }

        update_user_meta($user_id, 'wpsl_profile_id', $profile_id);
        update_user_meta($user_id, 'wpsl_profile_picture', $profile_picture);

        $status['user_id']   = $user_id;
        $status['user_name'] = $user_name;
        $status['password']  = $password;
        $status['status']    = true;

        $status['status_log']['registration_successfull'] = [
            'message' => 'User is registered successfully',
        ];

        $data = [
            'user_id'         => $user_id,
            'user_name'       => $user_name,
            'profile_id'      => $profile_id,
            'profile_picture' => $profile_picture,
            'password'        => $password,
        ];

        add_action( 'wpsl_after_registration', $data );

        return $status;
    }

    // login
    protected function login( $user_id = '' ) {
        if ( ! empty( $user_id ) && ! is_string( $user_id ) ) { return; }

        wp_clear_auth_cookie();
        wp_set_current_user( $user_id );
        wp_set_auth_cookie( $user_id, true );
    }
}