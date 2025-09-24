<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Conversational {
    /**
     * @var null
     */
    protected static $instance = null;

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );
        add_action( 'wpcf7_admin_init', array( $this, 'add_tag_generator' ), 99 );
        add_action( 'wpcf7_init', array( $this, 'add_form_tag_conversational' ), 99 );
        add_action( 'wpcf7_after_save', array( $this, 'save_conversational_data' ) );
        add_action( 'wp_ajax_eacf7_conversational_fields_validation', array( $this, 'fields_validation' ) );
        add_action( 'wp_ajax_nopriv_eacf7_conversational_fields_validation', array( $this, 'fields_validation' ) );
        add_action( 'eacf7_conversational_progress_bar', array( $this, 'render_progress_bar' ) );
        add_action( 'eacf7_conversational_welcome_screen', array( $this, 'render_welcome_screen' ) );
        add_action( 'eacf7_conversational_thank_you_screen', array( $this, 'render_thank_you_screen' ) );
        add_filter( 'wpcf7_contact_form_properties', array( $this, 'form_properties' ), 10, 2 );

        // add conversational data to localize script
        add_filter( 'eacf7_localize_data', array( $this, 'add_localize_data' ) );

        add_filter( 'wpcf7_form_elements', array( $this, 'print_conversational_js_data' ) );
    }

    /**
     * Localize Data
     * @since 1.0.0
     */
    public function add_localize_data( $data ) {
        if ( eacf7_is_editor_page() ) {
            $data['conversationalData'] = $this->get_conversational_data();
        }

        return $data;
    }

    public function print_conversational_js_data( $form_html ) {

        $form_id = eacf7_get_current_form_id();

        $data = $this->get_conversational_data( $form_id );

        if ( empty( $data ) ) {
            return $form_html;
        }

        // Encode PHP array/object to JSON for safe JS usage
        $json_data = wp_json_encode( $data );

        $js = <<<HTML
<script>
var eacf7ConversationalData_{$form_id} = {$json_data};
</script>
HTML;

        return $form_html . $js;
    }

    /**
     * Conversational Data
     * @since 1.0.0
     */
    public function get_conversational_data( $form_id = null ) {

        if ( ! $form_id ) {
            $form_id = eacf7_get_current_form_id();
        }

        $data = get_post_meta( $form_id, 'eacf7_conversational_data', true );

        return ! empty( $data ) ? $data : [];

    }

    /**
     * Assets
     * @since 1.0.0
     */
    public function frontend_assets() {
        wp_enqueue_style( 'eacf7-frontend' );
        wp_enqueue_script( 'eacf7-frontend' );
    }

    /**
     * Tag Generator
     */
    public function add_tag_generator() {
        $tag_generator = \WPCF7_TagGenerator::get_instance();

        if ( version_compare( WPCF7_VERSION, '6.0', '>=' ) ) {
            $tag_generator->add(
                    'conversational_start',
                    __( 'Conversational Form Start', 'essential-addons-for-contact-form-7' ),
                    [ $this, 'conversational_start_tag_generator_body_v6' ],
                    [ 'version' => 2 ]
            );

            $tag_generator->add(
                    'conversational_end',
                    __( 'Conversational Form End', 'essential-addons-for-contact-form-7' ),
                    [ $this, 'conversational_end_tag_generator_body_v6' ],
                    [ 'version' => 2 ]
            );
        } else {
            $tag_generator->add(
                    'conversational_start',
                    __( 'Conversational Form Start', 'essential-addons-for-contact-form-7' ),
                    [ $this, 'conversational_start_tag_generator_body' ]
            );

            $tag_generator->add(
                    'conversational_end',
                    __( 'Conversational Form End', 'essential-addons-for-contact-form-7' ),
                    [ $this, 'conversational_end_tag_generator_body' ]
            );
        }
    }

    /**
     * Start Tag Generator callback
     * @since 1.0.1
     */
    public function conversational_start_tag_generator_body_v6( $contact_form, $options ) {
        $tgg = new \WPCF7_TagGeneratorGenerator( $options['content'] );
        ?>
        <header class="description-box">
            <div class="eacf7-notice eacf7-notice-info">
                <p>
                    <span class="dashicons dashicons-info-outline"></span>
                    <?php
                    echo sprintf(
                            __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Conversational Form Field</a>', 'essential-addons-for-contact-form-7' ),
                            'https://softlabbd.com/docs/'
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
                            'conversational_start' => esc_html__( 'Conversational Start', 'essential-addons-for-contact-form-7' ),
                    ),
            ) );

            $tgg->print( 'field_name' );
            ?>

            <!-- Title -->
            <fieldset>
                <legend><?php echo esc_html__( 'Title', 'essential-addons-for-contact-form-7' ); ?></legend>
                <p class="oneline address-format">
                    <label>
                        <input type="text" data-tag-part="value" name="values"
                               placeholder="<?php echo esc_html__( 'Ex: Step Title', 'essential-addons-for-contact-form-7' ) ?>"/>
                    </label>
                </p>
            </fieldset>

            <!-- Step Number -->
            <fieldset>
                <legend><?php echo esc_html__( 'Step Number', 'essential-addons-for-contact-form-7' ); ?></legend>
                <p class="oneline address-format">
                    <label>
                        <input type="text" data-tag-part="option" data-tag-option="step:" name="step"
                               placeholder="<?php echo esc_html__( 'Ex: 1', 'essential-addons-for-contact-form-7' ) ?>"/>
                    </label>
                </p>
            </fieldset>
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
     * Start Tag Generator callback
     */
    public function conversational_start_tag_generator_body( $contact_form, $args = '' ) {
        $args             = wp_parse_args( $args, array() );
        $eacf7_field_type = 'conversational_start';
        ?>
        <div class="control-box">
            <fieldset>
                <table class="form-table">
                    <tbody>
                    <!-- Title -->
                    <tr>
                        <th scope="row">
                            <label><?php echo esc_html__( 'Title', 'essential-addons-for-contact-form-7' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="values" class="tg-value oneline"
                                   placeholder="<?php echo esc_attr__( 'Ex: Step Title', 'essential-addons-for-contact-form-7' ); ?>">
                        </td>
                    </tr>
                    <!-- Step -->
                    <tr>
                        <th scope="row">
                            <label for="step-number"><?php echo esc_html__( 'Step Number', 'essential-addons-for-contact-form-7' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="step" class="step-number oneline option"
                                   id="step-number"
                                   placeholder="<?php echo esc_attr__( 'Ex: 1', 'essential-addons-for-contact-form-7' ); ?>">
                        </td>
                    </tr>

                    <!-- Name -->
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr( isset( $args['content'] ) ? $args['content'] : 'conversational-start' ); ?>"><?php echo esc_html__( 'Name', 'essential-addons-for-contact-form-7' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline"
                                   id="<?php echo esc_attr( isset( $args['content'] ) ? $args['content'] : 'conversational-start' ); ?>">
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="eacf7-notice eacf7-notice-info">
                    <p>
                        <?php
                        echo sprintf(
                                __( 'Not sure how to set this? Check our <a href="%1$s" target="_blank">documentation</a>', 'essential-addons-for-contact-form-7' ),
                                'https://softlabbd.com/docs-category/contact-form-7-extended/'
                        );
                        ?>
                    </p>
                </div>
            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="<?php echo esc_attr( $eacf7_field_type ) ?>" class="tag code" readonly
                   onfocus="this.select()">
            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag"
                       value="<?php echo esc_attr( __( 'Insert Tag', 'essential-addons-for-contact-form-7' ) ); ?>">
            </div>
        </div>
        <?php
    }

    /**
     * Start Tag Generator callback
     * @since 1.0.1
     */
    public function conversational_end_tag_generator_body_v6( $contact_form, $options ) {
        $tgg = new \WPCF7_TagGeneratorGenerator( $options['content'] );
        ?>
        <header class="description-box">
            <div class="eacf7-notice eacf7-notice-info">
                <p>
                    <span class="dashicons dashicons-info-outline"></span>
                    <?php
                    echo sprintf(
                            __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Conversational Form Field</a>', 'essential-addons-for-contact-form-7' ),
                            'https://softlabbd.com/docs/'
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
                            'conversational_end' => esc_html__( 'Conversational End', 'essential-addons-for-contact-form-7' ),
                    ),
            ) );

            $tgg->print( 'field_name' );
            ?>
        </div>
        <footer class="insert-box">
            <?php
            $tgg->print( 'insert_box_content' );
            ?>
        </footer>
        <?php
    }


    /**
     * Stop Tag Generator callback
     */
    public function conversational_end_tag_generator_body( $contact_form, $args = '' ) {
        $args             = wp_parse_args( $args, array() );
        $eacf7_field_type = 'conversational_end';
        ?>
        <div class="control-box">
            <fieldset>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr( isset( $args['content'] ) ? $args['content'] : 'conversational-end' ); ?>"><?php echo esc_html__( 'Name', 'essential-addons-for-contact-form-7' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline"
                                   id="<?php echo esc_attr( isset( $args['content'] ) ? $args['content'] : 'conversational-end' ); ?>"
                                   value="end">
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="eacf7-notice eacf7-notice-info">
                    <p>
                        <span class="dashicons dashicons-info-outline"></span>
                        <?php
                        echo sprintf(
                                __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Conversational Form Field</a>', 'essential-addons-for-contact-form-7' ),
                                'https://softlabbd.com/docs-category/contact-form-7-extended/'
                        );
                        ?>
                    </p>
                </div>
            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="<?php echo esc_attr( $eacf7_field_type ) ?>" class="tag code" readonly
                   onfocus="this.select()">
            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag"
                       value="<?php echo esc_attr( __( 'Insert Tag', 'essential-addons-for-contact-form-7' ) ); ?>">
            </div>
        </div>
        <?php
    }

    /**
     * Custom Conversational_Form Tag
     */
    public function add_form_tag_conversational() {
        wpcf7_add_form_tag( 'conversational_start', array( $this, 'conversational_start_form_tag_handler' ) );
        wpcf7_add_form_tag( 'conversational_end', array( $this, 'conversational_end_form_tag_handler' ) );
    }

    /**
     * Conversational_Form start tag callback
     */
    public function conversational_start_form_tag_handler( $tag ) {
        ob_start();
        $current_form                = \WPCF7_ContactForm::get_current();
        $step                        = $tag->get_option( 'step', '', true );
        $title                       = $title = isset( $tag['values'][0] ) ? $tag['values'][0] : '';
        $id                          = $current_form->id();
        $eacf7_conversational_data   = ! empty( get_post_meta( $id, 'eacf7_conversational_data', true ) ) ? get_post_meta( $id, 'eacf7_conversational_data', true ) : [];
        $eacf7_conversational_status = ! empty( $eacf7_conversational_data['multitStep'] ) ? $eacf7_conversational_data['multitStep'] : '1';
        $eacf7_conversational_layout = isset( $eacf7_conversational_data['conversationalStyle'] ) ? $eacf7_conversational_data['conversationalStyle'] : '1';

        if ( ! $eacf7_conversational_status ) {
            return;
        }
        ?>
        <div class="eacf7-conversational style-<?php echo esc_attr( $eacf7_conversational_layout ); ?>" data-step="<?php echo esc_attr( $step ); ?>">
        <span class="title"><?php echo esc_html( $title ); ?></span>
        <div class="form-control-container">
        <?php
        return ob_get_clean();
    }

    /**
     * Conversational_Form end tag callback
     */
    public function conversational_end_form_tag_handler( $tag ) {
        ob_start();
        ?>
        <p class="eacf7-conversational-control">
            <button type="button"
                    class="button eacf7-next"><?php echo esc_html__( 'Next', 'essential-addons-for-contact-form-7' ); ?></button>
        </p>
        </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Save meta
     */
    public function save_conversational_data( $contact_form ) {

        $post_id = $contact_form->id();

        $all_steps = $contact_form->scan_form_tags( array( 'type' => 'conversational_start' ) );

        $step_titles = array_map( function ( $step ) {
            return ( is_array( $step->values ) && ! empty( $step->values ) ) ? $step->values[0] : '';
        }, $all_steps );

        if ( ! empty( $step_titles ) ) {
            update_post_meta( $post_id, 'eacf7_conversational_steps_title', $step_titles );
        }

        if ( empty( $_POST['eacf7_conversational_data'] ) ) {
            return;
        }

        $conversational_data = stripslashes( $_POST['eacf7_conversational_data'] );
        $conversational_data = json_decode( $conversational_data, true );

        update_post_meta( $post_id, 'eacf7_conversational_data', $conversational_data );
    }

    /**
     * Fields Validation
     */
    public function fields_validation() {
        if ( ! isset( $_POST ) || empty( $_POST ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['nonce'], 'eacf7' ) ) {
            exit( esc_html__( "Security error", 'essential-addons-for-contact-form-7' ) );
        }

        $id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : null;

        $tag_name = isset( $_REQUEST['validation_fields'] ) ? $_REQUEST['validation_fields'] : array();

        $form = wpcf7_contact_form( $id );

        $all_form_tags = $form->scan_form_tags();

        require_once WPCF7_PLUGIN_DIR . '/includes/validation.php';

        $result = new \WPCF7_Validation();

        $tags = array_filter(
                $all_form_tags,
                function ( $item, $index ) use ( $tag_name ) {
                    return in_array( $item->name, $tag_name );
                },
                ARRAY_FILTER_USE_BOTH
        );

        $form->validate_schema(
                array(
                        'text'  => true,
                        'file'  => false,
                        'field' => $tag_name,
                ),
                $result
        );

        foreach ( $tags as $tag ) {
            $type = $tag->type;

            if ( 'file' != $type && 'file*' != $type ) {
                $result = apply_filters( "wpcf7_validate_{$type}", $result, $tag );
            } elseif ( 'file*' === $type || 'file' === $type ) {
                $fdir = $_REQUEST[ $tag->name ];
                if ( $fdir ) {
                    $_FILES[ $tag->name ] = array(
                            'name'     => wp_basename( $fdir ),
                            'tmp_name' => $fdir,
                    );
                }
                $file           = $_FILES[ $tag->name ];
                $args           = array(
                        'tag'       => $tag,
                        'name'      => $tag->name,
                        'required'  => $tag->is_required(),
                        'filetypes' => $tag->get_option( 'filetypes' ),
                        'limit'     => $tag->get_limit_option(),
                );
                $args['schema'] = $form->get_schema();
                $new_files      = wpcf7_unship_uploaded_file( $file, $args );
                if ( is_wp_error( $new_files ) ) {
                    $result->invalidate( $tag, $new_files );
                }
                $result = apply_filters( "wpcf7_validate_{$type}", $result, $tag, array( 'uploaded_files' => $new_files, ) );

                if ( isset( $_REQUEST[ $tag->name . '_size' ] ) ) {
                    $file_size = $_REQUEST[ $tag->name . '_size' ];
                    if ( $file_size > $tag->get_limit_option() ) {
                        $file_error = array(
                                'into'    => 'span.wpcf7-form-control-wrap[data-name = ' . esc_attr( $tag->name ) . ']',
                                'message' => 'The uploaded file is too large.',
                                'idref'   => null,
                        );
                    }
                }
            }
        }

        $is_valid = ( $result->is_valid() ) ? true : false;

        $invalid_fields = $result->get_invalid_fields() ? $result->get_invalid_fields() : [];

        $data = array();

        if ( $is_valid ) {
            $data['is_valid'] = $is_valid;
        } else {
            $data['is_valid']       = $is_valid;
            $data['invalid_fields'] = $invalid_fields;
        }

        echo wp_send_json_success( $data );

        wp_die();
    }

    /**
     * Render Progressbar
     *
     * @since 1.0.0
     */
    public function render_progress_bar( $id ) {
        $data                     = ! empty( get_post_meta( $id, 'eacf7_conversational_data', true ) ) ? get_post_meta( $id, 'eacf7_conversational_data', true ) : [];
        $progressbar              = ( isset( $data['progressbar'] ) && $data['progressbar'] ) ? intval( $data['progressbar'] ) : 0;
        $progressbarHeight        = isset( $data['progressbarHeight'] ) ? intval( $data['progressbarHeight'] ) : '';
        $progressbarBgColor       = isset( $data['progressbarBgColor'] ) ? $data['progressbarBgColor'] : '';
        $progressbarActiveBgColor = isset( $data['progressbarActiveBgColor'] ) ? $data['progressbarActiveBgColor'] : '';

        if ( ! $progressbar ) {
            return;
        }

        $style = '';

        if ( ! empty( $progressbarHeight ) ) {
            $style .= 'height:' . esc_html( $progressbarHeight ) . 'px;';
        }

        if ( ! empty( $progressbarBgColor ) ) {
            $style .= 'background-color:' . esc_html( $progressbarBgColor ) . ';';
        }
        ?>
        <div class="eacf7-progressbar" <?php echo $style ? 'style="' . $style . '"' : ''; ?>>
            <span class="eacf7-progress-completed" <?php echo $progressbarActiveBgColor ? 'style="background-color: ' . esc_attr( $progressbarActiveBgColor ) . ';"' : ''; ?>></span>
        </div>
        <?php
    }

    /**
     * Welcom Screen
     * @since 1.0.0
     */
    public function render_welcome_screen( $id ) {
        $data                         = ! empty( get_post_meta( $id, 'eacf7_conversational_data', true ) ) ? get_post_meta( $id, 'eacf7_conversational_data', true ) : [];
        $welcome_screen               = ( isset( $data['welcomeScreen'] ) && $data['welcomeScreen'] ) ? $data['welcomeScreen'] : '';
        $welcome_screen_heading       = isset( $data['welcomeHeading'] ) ? $data['welcomeHeading'] : __( 'Welcome Heading', 'essential-addons-for-contact-form-7' );
        $welcome_screen_desc          = isset( $data['welcomeDescription'] ) ? $data['welcomeDescription'] : __( 'Welcome Description', 'essential-addons-for-contact-form-7' );
        $welcome_screen_media         = isset( $data['welcomeMedia'] ) ? $data['welcomeMedia'] : '';
        $welcome_screen_content_align = isset( $data['welcomeAlignment'] ) ? $data['welcomeAlignment'] : 'center';
        $welcome_screen_layout        = isset( $data['welcomeLayout'] ) ? $data['welcomeLayout'] : 'left';
        $active                       = $welcome_screen ? 'active' : '';
        if ( ! $welcome_screen ) {
            return;
        }
        ?>
        <div class="eacf7-welcome-screen layout-<?php echo esc_attr( $welcome_screen_layout ); ?> <?php echo esc_attr( $active ); ?>">
            <?php
            switch ( $welcome_screen_layout ) {
                case 'left':
                    ?>
                    <div class="img">
                        <img src="<?php echo esc_url( $welcome_screen_media ); ?>" class=""
                             alt="<?php echo esc_attr( $welcome_screen_heading ); ?>"/>
                    </div>
                    <div class="content <?php echo esc_attr( $welcome_screen_content_align ) ?>">
                        <h2><?php echo esc_html( $welcome_screen_heading ); ?></h2>
                        <p><?php echo esc_html( $welcome_screen_desc ); ?></p>
                        <button class="button"
                                type="button"><?php echo esc_html__( 'Start', 'essential-addons-for-contact-form-7' ); ?></button>
                    </div>
                    <?php
                    break;
                case 'right':
                    ?>
                    <div class="content <?php echo esc_attr( $welcome_screen_content_align ) ?>">
                        <h2><?php echo esc_html( $welcome_screen_heading ); ?></h2>
                        <p><?php echo esc_html( $welcome_screen_desc ); ?></p>
                        <button class="button"
                                type="button"><?php echo esc_html__( 'Start', 'essential-addons-for-contact-form-7' ); ?></button>
                    </div>
                    <div class="img">
                        <img src="<?php echo esc_url( $welcome_screen_media ); ?>" class=""
                             alt="<?php echo esc_attr( $welcome_screen_heading ); ?>"/>
                    </div>
                    <?php
                    break;
                default:
                    ?>
                    <div class="content <?php echo esc_attr( $welcome_screen_content_align ) ?>">
                        <h2><?php echo esc_html( $welcome_screen_heading ); ?></h2>
                        <p><?php echo esc_html( $welcome_screen_desc ); ?></p>
                        <button class="button"
                                type="button"><?php echo esc_html__( 'Start', 'essential-addons-for-contact-form-7' ); ?></button>
                    </div>
                    <?php
                    break;
            }
            ?>
        </div>
        <?php
    }

    /**
     * Thank You Screen
     * @since 1.0.0
     */
    public function render_thank_you_screen( $id ) {
        $data                      = ! empty( get_post_meta( $id, 'eacf7_conversational_data', true ) ) ? get_post_meta( $id, 'eacf7_conversational_data', true ) : [];
        $thank_you_screen          = ( isset( $data['thankYouScreen'] ) && $data['thankYouScreen'] ) ? $data['thankYouScreen'] : 0;
        $thank_you_screen_heading  = isset( $data['thankYouHeading'] ) ? $data['thankYouHeading'] : esc_html( 'Thank You', 'essential-addons-for-contact-form-7' );
        $thank_you_screen_desc     = isset( $data['thankYouDescription'] ) ? $data['thankYouDescription'] : esc_html( 'Lorem ipsum doller sit ammet', 'essential-addons-for-contact-form-7' );
        $thank_you_screen_btn_text = isset( $data['thankYouBtnText'] ) ? $data['thankYouBtnText'] : __( 'Learn More', 'essential-addons-for-contact-form-7' );
        $thank_you_screen_btn_link = isset( $data['thankYouBtnLink'] ) ? $data['thankYouBtnLink'] : '#';
        $thank_you_screen_layout   = isset( $data['thankYouLayout'] ) ? $data['thankYouLayout'] : 'full';
        $thank_you_screen_media    = isset( $data['thankYouMedia'] ) ? $data['thankYouMedia'] : '';
        if ( $thank_you_screen ) {
            ?>
            <div class="eacf7-thank-you-screen layout-<?php echo esc_attr( $thank_you_screen_layout ); ?>">
                <div class="content">
                    <h2><?php echo esc_html( $thank_you_screen_heading ); ?></h2>
                    <p><?php echo esc_html( $thank_you_screen_desc ); ?></p>
                    <a href="#" class="button" type="button"><?php echo esc_html( $thank_you_screen_btn_text ); ?></a>
                </div>
                <div class="img">
                    <img src="<?php echo esc_url( $thank_you_screen_media ); ?>" class=""
                         alt="<?php echo esc_attr( $thank_you_screen_heading ); ?>"/>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Form Properties
     */
    public function form_properties( $properties, $form ) {

        if ( ! is_admin() ) {
            $cf7_form_id                             = $form->id();
            $cf7_form                                = $properties['form'];
            $eacf7_conversational_data               = ! empty( get_post_meta( $cf7_form_id, 'eacf7_conversational_data', true ) ) ? get_post_meta( $cf7_form_id, 'eacf7_conversational_data', true ) : [];
            $welcomeScreen                           = isset( $eacf7_conversational_data['welcomeScreen'] ) ? intval( $eacf7_conversational_data['welcomeScreen'] ) : 0;
            $eacf7_conversational_status             = str_contains( $properties['form'], 'conversational_start' ) ? 1 : 0;
            $eacf7_conversational_progressbar_status = isset( $eacf7_conversational_data['progressbar'] ) ? $eacf7_conversational_data['progressbar'] : 1;
            $eacf7_conversational_fullscreen         = ( isset( $eacf7_conversational_data['fullscreen'] ) && $eacf7_conversational_data['fullscreen'] ) ? 'eacf7-conversational-full-screen' : '';
            $thankYouScreen                          = isset( $eacf7_conversational_data['thankYouScreen'] ) ? intval( $eacf7_conversational_data['thankYouScreen'] ) : 0;

            $eacf7_conversational_bg_color       = isset( $eacf7_conversational_data['formBgColor'] ) ? $eacf7_conversational_data['formBgColor'] : '#fff';
            $eacf7_conversational_btn_text_color = isset( $eacf7_conversational_data['formBtnTextColor'] ) ? $eacf7_conversational_data['formBtnTextColor'] : '#fff';
            $eacf7_conversational_btn_bg_color   = isset( $eacf7_conversational_data['formBtnBgColor'] ) ? $eacf7_conversational_data['formBtnBgColor'] : '#4A98FD';
            $eacf7_conversational_btn_bg_img     = isset( $eacf7_conversational_data['formBgImg'] ) ? $eacf7_conversational_data['formBgImg'] : '';

            $style = '<style>';
            $style .= '.eacf7-conversational-form{';
            if ( $eacf7_conversational_bg_color ) {
                $style .= 'background-color: ' . esc_attr( $eacf7_conversational_bg_color ) . ';';
            }

            if ( $eacf7_conversational_btn_bg_img ) {
                $style .= 'background-image: url(' . esc_attr( $eacf7_conversational_btn_bg_img ) . ');';
            }
            $style .= '}';

            $style .= '.eacf7-conversational-form .eacf7-conversational .eacf7-next{';
            if ( $eacf7_conversational_btn_text_color ) {
                $style .= 'color:' . esc_attr( $eacf7_conversational_btn_text_color ) . ' !important;';
            }

            if ( $eacf7_conversational_btn_bg_color ) {
                $style .= 'background-color:' . esc_attr( $eacf7_conversational_btn_bg_color ) . ' !important;';
            }
            $style .= '}';
            $style .= '</style>';

            if ( $eacf7_conversational_status ) {
                ob_start();
                ?>
                <div class="eacf7-conversational-form <?php echo esc_attr( $eacf7_conversational_fullscreen ); ?>">
                    <?php
                    if ( $welcomeScreen ) {
                        do_action( 'eacf7_conversational_welcome_screen', $cf7_form_id );
                    }
                    ?>
                    <?php echo $cf7_form; ?>
                    <?php
                    if ( $thankYouScreen ) {
                        do_action( 'eacf7_conversational_thank_you_screen', $cf7_form_id );
                    }
                    ?>
                    <div class="eacf7-conversational-prev-next-control">
                        <a href="" class="prev">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 20 20">
                                <path fill="currentColor" d="m15 14l-5-5l-5 5l-2-1l7-7l7 7z"/>
                            </svg>
                        </a>
                        <a href="" class="next">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 20 20">
                                <path fill="currentColor" d="m5 6l5 5l5-5l2 1l-7 7l-7-7z"/>
                            </svg>
                        </a>
                    </div>
                    <?php
                    // progressbar
                    if ( $eacf7_conversational_progressbar_status ) {
                        do_action( 'eacf7_conversational_progress_bar', $cf7_form_id );
                    }
                    ?>
                </div>
                <?php
                echo $style;
                $conversational_form = ob_get_clean();
                $properties['form']  = $conversational_form;
            } else {
                $properties['form'] = $cf7_form;
            }
        }

        return $properties;
    }

    /**
     * @return Conversational|null
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

Conversational::instance();
