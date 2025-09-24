<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Submission_Id {
    /**
     * @var null
     */
    protected static $instance = null;

    /**
     * constructor
     */
    public function __construct() {
        add_action( 'wpcf7_init', array( $this, 'add_data_handler' ) );
        add_action( 'wpcf7_admin_init', array( $this, 'add_tag_generator' ), 99 );
        add_action( 'wpcf7_submit', array( $this, 'update_submission_id' ) );
        add_filter( 'wpcf7_mail_components', [ $this, 'submission_id_on_mail_subject' ], 10, 2 );
        add_action( 'wpcf7_after_save', array( $this, 'eacf7_save_meta' ) );

        // add submission id data to localize script
        add_filter( 'eacf7_localize_data', array( $this, 'add_localize_data' ) );
    }

    public function add_localize_data( $data ) {
        if ( eacf7_is_editor_page() ) {
            $data['submissionIdData'] = $this->get_submission_id_data();
        }

        return $data;
    }

    function get_submission_id_data( $form_id = null ) {

        if ( ! $form_id ) {
            $form_id = eacf7_get_current_form_id();
        }

        $data = get_post_meta( $form_id, 'eacf7_submission_id_data', true );

        return ! empty( $data ) ? $data : [];

    }

    /**
     * Add Contact Form Tag
     */
    public function add_data_handler() {
        if ( function_exists( 'wpcf7_add_form_tag' ) ) {
            wpcf7_add_form_tag(
                    array( 'submission_id', 'submission_id*' ),
                    array( $this, 'render_submission_id' ),
                    array( 'name-attr' => true )
            );
        }
    }

    /**
     * Handler callback
     *
     * @return void
     */
    public function render_submission_id( $tag ) {

        // check and make sure tag name is not empty
        if ( empty( $tag->name ) ) {
            return '';
        }

        /** Enable / Disable Submission ID */
        $wpcf7                       = \WPCF7_ContactForm::get_current();
        $formid                      = $wpcf7->id();
        $submission_id_data          = get_post_meta( $formid, 'eacf7_submission_id_data', true );
        $eacf7_submission_id_status  = ( isset( $submission_id_data['submissionId'] ) && $submission_id_data['submissionId'] ) ? intval( $submission_id_data['submissionId'] ) : 0;
        $eacf7_submission_id_current = isset( $submission_id_data['submissionIdCurrent'] ) ? intval( $submission_id_data['submissionIdCurrent'] ) : 1;

        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = wpcf7_form_controls_class( $tag->type );

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = [
                'type'  => 'hidden',
                'class' => $tag->get_class_option( $class ),
                'id'    => $tag->get_id_option(),
                'name'  => $tag->name,
        ];

        if ( $eacf7_submission_id_status ) {
            $atts['value'] = $eacf7_submission_id_current;
        }

        if ( $tag->is_required() ) {
            $atts['aria-required'] = 'true';
        }

        $atts = wpcf7_format_atts( $atts );

        ob_start();
        $html = sprintf( '<input %1$s />%2$s', $atts, $validation_error );

        ?>
        <span class="cf7-submission-id wpcf7-form-control-wrap <?php echo sanitize_html_class( $tag->name ); ?>"
              data-name="<?php echo sanitize_html_class( $tag->name ); ?>">
			<?php echo $html; ?>
		</span>
        <?php
        return ob_get_clean();
    }

    /**
     * Tag Generator
     *
     * @return void
     */
    public function add_tag_generator() {
        $tag_generator = \WPCF7_TagGenerator::get_instance();

        if ( version_compare( WPCF7_VERSION, '6.0', '>=' ) ) {
            $tag_generator->add(
                    'submission_id',
                    __( 'Submission ID', 'essential-addons-for-contact-form-7' ),
                    [ $this, 'tag_generator_body_v6' ],
                    [
                            'version' => 2,
                    ]
            );
        } else {
            wpcf7_add_tag_generator(
                    'submission_id',
                    __( 'Submission ID', 'essential-addons-for-contact-form-7' ),
                    'submission_id',
                    [ $this, 'tag_generator_body' ]
            );
        }
    }

    /**
     * Tag Generator callback method v6
     * @return void
     * @since 1.0.0
     */
    public function tag_generator_body_v6( $contact_form, $options ) {
        $tgg = new \WPCF7_TagGeneratorGenerator( $options['content'] );
        ?>
        <header class="description-box">
            <div class="eacf7-notice eacf7-notice-info">
                <p>
                    <span class="dashicons dashicons-info-outline"></span>
                    <?php
                    echo sprintf(
                            __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Submission ID</a>', 'essential-addons-for-contact-form-7' ),
                            'https://softlabbd.com/docs/submission-id/'
                    );
                    ?>
                </p>
            </div>
        </header>
        <div class="control-box">
            <?php
            $tgg->print( 'field_type', array(
                    'with_required'  => false,
                    'select_options' => array(
                            'submission_id' => esc_html__( 'Submission ID', 'essential-addons-for-contact-form-7' ),
                    ),
            ) );

            $tgg->print( 'field_name' );

            $tgg->print( 'class_attr' );
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

    /**
     * Tag Generator callback method
     * @return void
     * @since 1.0.0
     */
    public function tag_generator_body( $contact_form, $args = '' ) {
        $args       = wp_parse_args( $args, array() );
        $field_type = 'submission_id';
        ?>
        <div class="control-box">
            <fieldset>
                <div class="eacf7-notice eacf7-notice-info">
                    <p>
                        <span class="dashicons dashicons-info-outline"></span>
                        <?php
                        echo sprintf(
                                __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Submission ID</a>', 'essential-addons-for-contact-form-7' ),
                                'https://softlabbd.com/docs/submission-id/'
                        );
                        ?>
                    </p>
                </div>

                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html__( 'Name', 'essential-addons-for-contact-form-7' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline"
                                   id="<?php echo esc_attr( $args['content'] . '-name' ); ?>">
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="class-attributes"><?php echo esc_html__( 'Class', 'essential-addons-for-contact-form-7' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="class" class="class-attributes oneline option"
                                   id="class-attributes" placeholder="">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="insert-box">
            <input type="text" name="<?php echo esc_attr( $field_type ) ?>" class="tag code" readonly
                   onfocus="this.select()">

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag"
                       value="<?php echo esc_attr( __( 'Insert Tag', 'essential-addons-for-contact-form-7' ) ); ?>">
            </div>

            <br class="clear"/>

            <p class="description mail-tag">
                <label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>">
                    <?php printf( 'To display the submission id in the email, insert the mail-tag (%s) in the Mail tab.', '<strong><span class="mail-tag"></span></strong>' ); ?>
                    <input type="text" class="mail-tag code eacf7-hidden" readonly="readonly"
                           id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"/>
                </label>
            </p>
        </div>
        <?php
    }

    /**
     * Update Submission ID
     * @since 1.0.0
     */
    public function update_submission_id( $contact_form ) {
        $post_id                        = $contact_form->id();
        $eacf7_submission_id_data       = get_post_meta( $post_id, 'eacf7_submission_id_data', true );
        $eacf7_submission_id_status     = isset( $eacf7_submission_id_data['submissionId'] ) ? $eacf7_submission_id_data['submissionId'] : false;
        $eacf7_submission_id_start      = isset( $eacf7_submission_id_data['submissionIdStart'] ) ? $eacf7_submission_id_data['submissionIdStart'] : 1;
        $eacf7_submission_id_current    = isset( $eacf7_submission_id_data['submissionIdCurrent'] ) ? $eacf7_submission_id_data['submissionIdCurrent'] : 1;
        $eacf7_is_first_submission_id   = ( $eacf7_submission_id_start > $eacf7_submission_id_current ) ? true : false;
        $eacf7_submission_id_increment  = isset( $eacf7_submission_id_data['submissionIdIncrement'] ) ? $eacf7_submission_id_data['submissionIdIncrement'] : 1;
        $eacf7_updated_submission_value = ( $eacf7_is_first_submission_id ) ? intval( $eacf7_submission_id_start ) + intval( $eacf7_submission_id_increment ) : intval( $eacf7_submission_id_current ) + intval( $eacf7_submission_id_increment );

        if ( $eacf7_submission_id_status ) {
            $eacf7_submission_id_data['submissionIdCurrent'] = $eacf7_updated_submission_value;
            update_post_meta( $post_id, 'eacf7_submission_id_data', $eacf7_submission_id_data );
        }
    }

    /**
     * Submission id on mail subject
     * @since 1.0.0
     * @author monzuralam
     */
    public function submission_id_on_mail_subject( $components, $contact_form ) {
        $post_id                           = $contact_form->id();
        $eacf7_submission_id_data          = get_post_meta( $post_id, 'eacf7_submission_id_data', true );
        $eacf7_submission_id_status        = isset( $eacf7_submission_id_data['submissionId'] ) ? $eacf7_submission_id_data['submissionId'] : false;
        $eacf7_submission_id_start         = isset( $eacf7_submission_id_data['submissionIdStart'] ) ? $eacf7_submission_id_data['submissionIdStart'] : 1;
        $eacf7_submission_id_current       = isset( $eacf7_submission_id_data['submissionIdCurrent'] ) ? $eacf7_submission_id_data['submissionIdCurrent'] : 1;
        $eacf7_submission_id_increment     = isset( $eacf7_submission_id_data['submissionIdIncrement'] ) ? $eacf7_submission_id_data['submissionIdIncrement'] : 1;
        $eacf7_is_first_submission_id      = ( $eacf7_submission_id_start > $eacf7_submission_id_current ) ? true : false;
        $eacf7_show_id_on_the_mail_subject = isset( $eacf7_submission_id_data['submissionIdOnMail'] ) ? $eacf7_submission_id_data['submissionIdOnMail'] : false;
        $eacf7_submission_id_placement     = isset( $eacf7_submission_id_data['submissionIdPlacement'] ) ? $eacf7_submission_id_data['submissionIdPlacement'] : 'start';
        $eacf7_updated_submission_value    = ( $eacf7_is_first_submission_id ) ? intval( $eacf7_submission_id_start ) + intval( $eacf7_submission_id_increment ) : intval( $eacf7_submission_id_current ) + intval( $eacf7_submission_id_increment );

        if ( $eacf7_submission_id_status && $eacf7_show_id_on_the_mail_subject ) {
            if ( $eacf7_submission_id_placement === 'start' ) {
                $components['subject'] = $eacf7_updated_submission_value . ' ' . $components['subject'];
            } elseif ( $eacf7_submission_id_placement === 'end' ) {
                $components['subject'] = $components['subject'] . ' ' . $eacf7_updated_submission_value;
            } else {
                $components['subject'] = $eacf7_updated_submission_value;
            }
        }

        return $components;
    }

    /**
     * Register Panel
     */
    public function eacf7_register_panel( $panels ) {
        $panels['eacf7-submission-id-panel'] = array(
                'title'    => __( 'Submission ID', 'essential-addons-for-contact-form-7' ),
                'callback' => array( $this, 'eacf7_submission_id_panel_fields' ),
        );

        return $panels;
    }

    public function eacf7_submission_id_panel_fields( $post ) {
        $post_id = $post->id();

        $eacf7_submission_id_status        = get_post_meta( $post_id, 'eacf7_submission_id_status', true );
        $eacf7_submission_id_start         = ! empty( get_post_meta( $post_id, 'eacf7_submission_id_start', true ) ) ? get_post_meta( $post_id, 'eacf7_submission_id_start', true ) : 1;
        $eacf7_submission_id_increment     = ! empty( get_post_meta( $post_id, 'eacf7_submission_id_increment', true ) ) ? get_post_meta( $post_id, 'eacf7_submission_id_increment', true ) : 1;
        $eacf7_show_id_on_the_mail_subject = ! empty( get_post_meta( $post_id, 'eacf7_show_id_on_the_mail_subject', true ) ) ? get_post_meta( $post_id, 'eacf7_show_id_on_the_mail_subject', true ) : false;
        $eacf7_submission_id_placement     = ! empty( get_post_meta( $post_id, 'eacf7_submission_id_placement', true ) ) ? get_post_meta( $post_id, 'eacf7_submission_id_placement', true ) : 'end';
        ?>
        <h2><?php echo esc_html__( 'Unique Submission Id Settings', 'essential-addons-for-contact-form-7' ); ?></h2>
        <p><?php echo esc_html__( 'Add an unique id to every form submission to keep a record of each submission. The ID can be added on the "Subject Line" of your form. ', 'essential-addons-for-contact-form-7' ); ?></p>
        <fieldset>
            <!-- enable submission id start here -->
            <div class="eacf7-wrapper">
                <h3>
                    <?php echo esc_html__( 'Enable Submission ID', 'essential-addons-for-contact-form-7' ); ?>
                </h3>
                <div class="eacf7-fieldset">
                    <input type="checkbox" class="eacf7-checkbox eacf7_submission_id_status"
                           id="eacf7_submission_id_status" name="eacf7_submission_id_status"
                           value="true" <?php checked( 'true', $eacf7_submission_id_status, true ); ?>>
                    <label for="eacf7_submission_id_status" class="eacf7-switch"></label>
                </div>
            </div>
            <!-- enable submission id stop here -->

            <div class="eacf7-wrapper">
                <h3><?php echo esc_html__( 'Submission ID Option', 'essential-addons-for-contact-form-7' ); ?></h3>
                <div class="eacf7-wrapper-settings">
                    <div class="eacf7-field w-50">
                        <div class="eacf7-fieldset">
                            <label for="eacf7_submission_id_start"><?php echo esc_html__( 'Submission ID Starts from', 'essential-addons-for-contact-form-7' ); ?></label>
                            <input type="number" name="eacf7_submission_id_start" class="eacf7-input"
                                   id="eacf7_submission_id_start"
                                   value="<?php echo esc_html( $eacf7_submission_id_start ); ?>">
                            <span><?php echo esc_html__( 'Enter the starting number for the countdown, for example, 101. The default setting is 1.', 'essential-addons-for-contact-form-7' ); ?></span>
                        </div>
                    </div>
                    <div class="eacf7-field w-50">
                        <div class="eacf7-fieldset">
                            <label for="eacf7_submission_id_increment"><?php echo esc_html__( 'ID Step Increment', 'essential-addons-for-contact-form-7' ); ?></label>
                            <input type="number" name="eacf7_submission_id_increment" class="eacf7-input"
                                   id="eacf7_submission_id_increment"
                                   value="<?php echo esc_html( $eacf7_submission_id_increment ); ?>">
                            <span><?php echo esc_html__( 'Set how much the number will increase with each submission. For instance, if you set it to 2 and the ID starts from 101, the number will increment in the following sequence with each submission: 101, 103, 105, and so on. The default setting is 1.', 'essential-addons-for-contact-form-7' ); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="eacf7-wrapper">
                <h3>
                    <?php echo esc_html__( 'Show Submission ID on the Mail Subject Line', 'essential-addons-for-contact-form-7' ); ?>
                </h3>
                <div class="eacf7-fieldset">
                    <input type="checkbox" class="eacf7-checkbox eacf7_show_id_on_the_mail_subject"
                           id="eacf7_show_id_on_the_mail_subject" name="eacf7_show_id_on_the_mail_subject"
                           value="true" <?php checked( 'true', $eacf7_show_id_on_the_mail_subject, true ); ?>>
                    <label for="eacf7_show_id_on_the_mail_subject" class="eacf7-switch"></label>
                </div>

                <h3><?php echo esc_html__( 'Show Submission ID Option', 'essential-addons-for-contact-form-7' ); ?></h3>

                <div class="eacf7-wrapper-settings">
                    <div class="eacf7-field w-50">
                        <div class="eacf7-fieldset">
                            <label for="eacf7-id-placement-style"><?php echo esc_html__( 'ID Placement', 'essential-addons-for-contact-form-7' ); ?></label>

                            <select name="eacf7_submission_id_placement" class="eacf7-select"
                                    id="eacf7-id-placement-style">
                                <option value="start" <?php selected( $eacf7_submission_id_placement, 'start', true ); ?>><?php echo esc_html__( 'Start', 'essential-addons-for-contact-form-7' ); ?></option>
                                <option value="end" <?php selected( $eacf7_submission_id_placement, 'end', true ); ?>><?php echo esc_html__( 'End', 'essential-addons-for-contact-form-7' ); ?></option>
                                <option value="skip" <?php selected( $eacf7_submission_id_placement, 'skip', true ); ?>><?php echo esc_html__( 'Show only Submission ID, skip Subject Text', 'essential-addons-for-contact-form-7' ); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        <?php
    }

    /**
     * Save meta
     * @since 1.0.0
     */
    public function eacf7_save_meta( $contact_form ) {
        $post_id = $contact_form->id();

        if ( empty( $_POST['eacf7_submission_id_data'] ) ) {
            return;
        }

        $submission_id_data = stripslashes( $_POST['eacf7_submission_id_data'] );
        $submission_id_data = json_decode( $submission_id_data, true );

        update_post_meta( $post_id, 'eacf7_submission_id_data', $submission_id_data );
    }

    /**
     * @return Submission_Id|null
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

Submission_Id::instance();
