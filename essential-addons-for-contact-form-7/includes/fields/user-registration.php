<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class User_Registration {
    /**
     * @var null
     */
    protected static $instance = null;

    public function __construct() {
        add_action( 'wpcf7_admin_init', [ $this, 'add_tag_generator' ], 99 );
        add_action( 'wpcf7_init', [ $this, 'add_data_handler' ] );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_filter( 'wpcf7_validate_password', [ $this, 'validate' ], 10, 2 );
        add_filter( 'wpcf7_validate_password*', [ $this, 'validate' ], 10, 2 );

        add_action( 'wpcf7_before_send_mail', [ $this, 'handle_insert_user' ] );
        add_action( 'eacf7_send_mail_notification', [ $this, 'send_mail' ], 10, 2 );

        add_action( 'wp_ajax_eacf7_user_registration_auto_login', [ $this, 'auto_login_user' ] );
        add_action( 'wp_ajax_nopriv_eacf7_user_registration_auto_login', [ $this, 'auto_login_user' ] );

        add_action( 'wpcf7_after_save', array( $this, 'user_registration_save_data' ) );

        // add post-submission data to localize script
        add_filter( 'eacf7_localize_data', array( $this, 'add_localize_data' ) );

        add_filter( 'wpcf7_form_elements', array( $this, 'print_user_registration_js_data' ) );

    }

    public function add_localize_data( $data ) {
        if ( eacf7_is_editor_page() ) {
            $data['userRegistrationData'] = $this->get_user_registration_data();
            $data['userRoles']            = $this->get_roles();
            $data['getPostType']          = get_post_types( array( 'public' => true ) );
        }

        return $data;
    }

    public function print_user_registration_js_data( $form_html ) {

        $form_id = eacf7_get_current_form_id();

        $data = $this->get_user_registration_data( $form_id );

        if ( empty( $data ) ) {
            return $form_html;
        }

        // Encode PHP array/object to JSON for safe JS usage
        $json_data = wp_json_encode( $data );

        $js = <<<HTML
<script>
var eacf7UserRegistrationData_{$form_id} = {$json_data};
</script>
HTML;

        return $form_html . $js;
    }

    function get_user_registration_data( $form_id = null ) {

        if ( ! $form_id ) {
            $form_id = eacf7_get_current_form_id();
        }

        $data = get_post_meta( $form_id, 'eacf7_user_registration_data', true );

        return ! empty( $data ) ? $data : [];
    }

    /**
     * Get Roles
     */
    public function get_roles() {
        global $wp_roles;
        $roles = $wp_roles->roles;
        $data  = [];

        foreach ( $roles as $key => $value ) {
            $data[ $key ] = $value['name'];
        }

        unset( $data['administrator'] );

        return $data;
    }

    /**
     * Tag Generator
     */
    public function add_tag_generator() {
        $tag_generator = \WPCF7_TagGenerator::get_instance();

        if ( version_compare( WPCF7_VERSION, '6.0', '>=' ) ) {
            $tag_generator->add(
                    'password',
                    __( 'Password', 'essential-addons-for-contact-form-7' ),
                    [ $this, 'tag_generator_body_v6' ],
                    [
                            'version' => 2,
                    ]
            );
        } else {
            $tag_generator->add(
                    'password',
                    __( 'Password', 'essential-addons-for-contact-form-7' ),
                    [ $this, 'tag_generator_body' ]
            );
        }
    }

    public function tag_generator_body_v6( $contact_form, $options ) {
        $tgg = new \WPCF7_TagGeneratorGenerator( $options['content'] );
        ?>
        <header class="description-box">
            <div class="eacf7-notice eacf7-notice-info">
                <p>
                    <span class="dashicons dashicons-info-outline"></span>
                    <?php
                    echo sprintf(
                            __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">User Registration</a>', 'essential-addons-for-contact-form-7' ),
                            'https://softlabbd.com/docs/'
                    );
                    ?>
                </p>
            </div>
        </header>
        <div class="control-box">
            <?php
            $tgg->print( 'field_type', array(
                    'with_required'  => true,
                    'select_options' => array(
                            'password' => esc_html__( 'Password', 'essential-addons-for-contact-form-7' ),
                    ),
            ) );

            $tgg->print( 'field_name' );

            $tgg->print( 'class_attr' );

            $tgg->print( 'id_attr' );
            ?>
        </div>
        <footer class="insert-box">
            <?php
            $tgg->print( 'insert_box_content' );

            $tgg->print( 'mail_tag_tip' );
            ?>
        </footer>
        <?php
    }

    public function tag_generator_body( $contact_form, $args = '' ) {
        $args = wp_parse_args( $args, [] );
        $type = isset( $args['id'] ) ? $args['id'] : '';
        ?>
        <div class="control-box">
            <fieldset>
                <div class="eacf7-notice eacf7-notice-info">
                    <p>
                        <span class="dashicons dashicons-info-outline"></span>
                        <?php
                        echo sprintf(
                                __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">User Registration</a>', 'essential-addons-for-contact-form-7' ),
                                'https://softlabbd.com/docs-category/contact-form-7-extended/'
                        );
                        ?>
                    </p>
                </div>

                <table class="form-table">
                    <tbody>

                    <!-- Field Type -->
                    <tr>
                        <th scope="row"><?php echo esc_html__( 'Field type', 'essential-addons-for-contact-form-7' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php echo esc_html__( 'Field type', 'essential-addons-for-contact-form-7' ); ?></legend>
                                <label>
                                    <input type="checkbox"
                                           name="required"/> <?php echo esc_html__( 'Required field', 'essential-addons-for-contact-form-7' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <!-- Name -->
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>">
                                <?php echo esc_html__( 'Name', 'essential-addons-for-contact-form-7' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline"
                                   id="<?php echo esc_attr( $args['content'] . '-name' ); ?>"/>
                        </td>
                    </tr>

                    <!-- Class -->
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>">
                                <?php echo esc_html__( 'Class', 'essential-addons-for-contact-form-7' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" name="class" class="class value oneline option"
                                   id="<?php echo esc_attr( $args['content'] . '-class' ); ?>"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="<?php echo esc_attr( $type ); ?>" class="tag code" readonly="readonly"
                   onfocus="this.select()"/>

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag"
                       value="<?php echo esc_attr__( 'Insert Tag', 'essential-addons-for-contact-form-7' ); ?>"/>
            </div>

            <br class="clear"/>

            <p class="description mail-tag">
                <label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>">
                    <?php printf( 'To display the %s in the email, insert the mail-tag (%s) in the Mail tab.', $type, '<strong><span class="mail-tag"></span></strong>' ); ?>
                    <input type="text" class="mail-tag code eacf7-hidden" readonly="readonly"
                           id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"/>
                </label>
            </p>
        </div>
        <?php
    }

    /**
     * Add shortcode handler to CF7.
     */
    public function add_data_handler() {
        if ( function_exists( 'wpcf7_add_form_tag' ) ) {
            $tags = [
                    'password' => 'render_password',
            ];

            foreach ( $tags as $tag => $callback ) {
                $attributes = [ 'name-attr' => true ];
                wpcf7_add_form_tag( [ $tag, $tag . '*' ], [ $this, $callback ], $attributes );
            }
        }
    }

    /**
     * Render password
     * @since 1.0.0
     * @author monzuralam
     */
    public function render_password( $tag ) {

        $tag = new \WPCF7_FormTag( $tag );

        // check and make sure tag name is not empty
        if ( empty( $tag->name ) ) {
            return '';
        }

        // Validate our fields
        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = $tag->get_class_option( 'wpcf7-form-control eacf7-user-password' );

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = [
                'class'         => $class,
                'id'            => $tag->get_id_option(),
                'name'          => $tag->name,
                'aria-invalid'  => $validation_error ? 'true' : 'false',
                'aria-required' => $tag->is_required() ? 'true' : 'false',
        ];


        $atts = wpcf7_format_atts( $atts );

        ob_start();
        ?>
        <span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr( $tag->name ); ?>">
            <input type="password" <?php echo $atts; ?> />
            <?php echo $validation_error; ?>
        </span>
        <?php
        return ob_get_clean();
    }

    /**
     * Assets
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 'eacf7-frontend' );

        wp_enqueue_script( 'eacf7-frontend' );
    }

    /**
     * Validate
     */
    public function validate( $result, $tag ) {
        $tag   = new \WPCF7_FormTag( $tag );
        $name  = $tag->name;
        $type  = $tag->basetype;
        $value = isset( $_POST[ $name ] ) ? $_POST[ $name ] : '';

        if ( $tag->is_required() && empty( $value ) ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
        }

        return $result;
    }

    /**
     * create user
     */
    public function handle_insert_user( $contact_form ) {
        $submission = \WPCF7_Submission::get_instance();
        $formid     = $contact_form->id();

        $data = get_post_meta( $formid, 'eacf7_user_registration_data', true );

        $eacf7_user_registration     = isset( $data['userRegistration'] ) ? $data['userRegistration'] : false;
        $eacf7_user_role             = isset( $data['userRole'] ) ? sanitize_text_field( $data['userRole'] ) : 'subscriber';
        $eacf7_user_notification     = isset( $data['mailNotification'] ) ? $data['mailNotification'] : false;
        $eacf7_user_field_map_status = isset( $data['userFieldMapping'] ) ? $data['userFieldMapping'] : false;
        $eacf7_user_first_name       = isset( $data['userFirstName'] ) ? sanitize_text_field( $data['userFirstName'] ) : '';
        $eacf7_user_last_name        = isset( $data['userLastName'] ) ? sanitize_text_field( $data['userLastName'] ) : '';
        $eacf7_user_mail             = isset( $data['userEmail'] ) ? sanitize_text_field( $data['userEmail'] ) : '';
        $eacf7_user_password         = isset( $data['userPassword'] ) ? sanitize_text_field( $data['userPassword'] ) : '';

        if ( ! $eacf7_user_registration || ! $eacf7_user_field_map_status ) {
            return;
        }

        $posted_data = $submission->get_posted_data();

        $first_name = isset( $posted_data[ $eacf7_user_first_name ] ) ? sanitize_text_field( $posted_data[ $eacf7_user_first_name ] ) : '';
        $last_name  = isset( $posted_data[ $eacf7_user_last_name ] ) ? sanitize_text_field( $posted_data[ $eacf7_user_last_name ] ) : '';
        $username   = isset( $posted_data[ $eacf7_user_mail ] ) ? sanitize_email( $posted_data[ $eacf7_user_mail ] ) : '';
        $email      = isset( $posted_data[ $eacf7_user_mail ] ) ? sanitize_email( $posted_data[ $eacf7_user_mail ] ) : '';
        $password   = isset( $posted_data[ $eacf7_user_password ] ) ? sanitize_text_field( $posted_data[ $eacf7_user_password ] ) : '';

        if ( ! username_exists( $username ) && ! email_exists( $email ) ) {
            $user_id = wp_create_user( $username, $password, $email );

            if ( ! is_wp_error( $user_id ) ) {
                $user = new \WP_User( $user_id );
                $user->set_role( $eacf7_user_role );

                // set name
                update_user_meta( $user_id, 'first_name', $first_name );
                update_user_meta( $user_id, 'last_name', $last_name );

                // set display name
                wp_update_user( [
                        'ID'           => $user_id,
                        'display_name' => $first_name . ' ' . $last_name
                ] );

                // send notification
                if ( $eacf7_user_notification ) {
                    do_action( 'eacf7_send_mail_notification', $first_name, $email );
                    error_log( 'mail notification' );
                }
            }
        }
    }

    /**
     * Send Mail
     * @since 1.0.0
     */
    public function send_mail( $first_name, $email ) {
        $first_name = ucfirst( $first_name );
        $reset_link = wp_lostpassword_url();
        $subject    = esc_html( 'Your account has been successfully created!', 'essential-addons-for-contact-form-7' );

        // HTML Message
        $message = sprintf(
                "
            <html>
            <body>
                <h2>%s</h2>
                <p>%s</p>
                <p><strong>%s</strong> %s</p>
                <p>%s</p>
                <p><a href='%s'>%s</a></p>
                <br>
                <p>%s</p>
            </body>
            </html>
            ",
                sprintf( __( 'Hello %s,', 'essential-addons-for-contact-form-7' ), $first_name ),
                __( 'Your account has been created successfully.', 'essential-addons-for-contact-form-7' ),
                __( 'Email:', 'essential-addons-for-contact-form-7' ),
                $email,
                __( 'To set your password, please click the link below:', 'essential-addons-for-contact-form-7' ),
                esc_url( $reset_link ),
                __( 'Set Your Password', 'essential-addons-for-contact-form-7' ),
                __( 'Thanks!', 'essential-addons-for-contact-form-7' )
        );

        // Set headers for HTML email
        $headers = [
                'Content-Type: text/html; charset=UTF-8',
        ];

        // Send the email
        wp_mail( $email, $subject, $message, $headers );
    }

    /**
     * Auto Login via Ajax Request
     * @since 1.0.0
     */
    public function auto_login_user() {
        if ( ! wp_verify_nonce( $_POST['nonce'], 'eacf7' ) ) {
            exit( esc_html__( "Security error", 'essential-addons-for-contact-form-7' ) );
        }

        $mail     = isset( $_POST['mail'] ) ? sanitize_email( $_POST['mail'] ) : '';
        $password = isset( $_POST['password'] ) ? sanitize_text_field( $_POST['password'] ) : '';

        $credentials = [
                'user_login'    => $mail,
                'user_password' => $password,
                'remember'      => true,
        ];

        $user = wp_signon( $credentials, false );

        $data = [];

        if ( ! is_wp_error( $user ) ) {
            // Set the authentication cookies for the user
            wp_set_current_user( $user->ID );
            wp_set_auth_cookie( $user->ID, true );

            $data['url'] = admin_url();
        }

        echo wp_send_json_success( $data );
    }

    /**
     * Save meta
     */
    public function user_registration_save_data( $contact_form ) {
        $post_id = $contact_form->id();

        if ( empty( $_POST['eacf7_user_registration_data'] ) ) {
            return;
        }

        $user_registration_data = stripslashes( $_POST['eacf7_user_registration_data'] );
        $user_registration_data = json_decode( $user_registration_data, true );

        update_post_meta( $post_id, 'eacf7_user_registration_data', $user_registration_data );
    }

    /**
     * @return User_Registration|null
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

User_Registration::instance();
