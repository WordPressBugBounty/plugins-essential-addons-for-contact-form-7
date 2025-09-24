<?php

namespace EACF7;

use WPCF7_Validation, WPCF7_Submission;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Conditional {
    /**
     * @var null
     */
    protected static $instance = null;

    private $hidden_fields = [];

    public function __construct() {
        add_action( 'admin_init', array( $this, 'add_tag_generator' ) );
        add_action( 'wpcf7_init', array( $this, 'add_shortcodes' ) );

        add_filter( 'wpcf7_contact_form_properties', array( $this, 'update_form_properties' ), 10, 2 );

        add_action( 'wpcf7_form_hidden_fields', array( $this, 'set_form_hidden_fields' ) );
        add_filter( 'wpcf7_posted_data', array( $this, 'remove_hidden_post_data' ) );
        add_filter( 'wpcf7_validate', array( $this, 'skip_validation_for_hidden_fields' ), 2, 2 );
        add_action( 'wpcf7_before_send_mail', array( $this, 'mail_properties' ) );

        add_action( 'wpcf7_after_save', array( $this, 'save_form_settings' ) );

        // add conditional data to localize script
        add_filter( 'eacf7_localize_data', array( $this, 'add_conditional_data' ) );


        // Add integration data to the form footer
        add_filter( 'wpcf7_form_elements', array( $this, 'print_conditional_js_data' ) );

    }

    public function add_conditional_data( $data ) {
        if ( eacf7_is_editor_page() ) {
            $data['conditionalData'] = $this->get_conditional_data();
        }

        return $data;
    }

    public function print_conditional_js_data( $form_html ) {

        $form_id = eacf7_get_current_form_id();

        $data = $this->get_conditional_data( $form_id );

        if ( empty( $data ) ) {
            return $form_html;
        }

        // Encode PHP array/object to JSON for safe JS usage
        $json_data = wp_json_encode( $data );

        $js = <<<HTML
<script>
var eacf7ConditionalData_{$form_id} = {$json_data};
</script>
HTML;

        return $form_html . $js;
    }

    public function get_conditional_data( $form_id = null ) {

        if ( ! $form_id ) {
            $form_id = eacf7_get_current_form_id();
        }

        $data = get_post_meta( $form_id, 'eacf7_conditional_rules', true );

        return ! empty( $data ) ? $data : [];

    }

    /**
     * Save Form Settings
     */
    public function save_form_settings( $form ) {

        if ( empty( $_POST['eacf7_conditional_rules'] ) ) {
            return;
        }

        $conditional_rules = stripslashes( $_POST['eacf7_conditional_rules'] );
        $conditional_rules = json_decode( $conditional_rules, true );

        $id = $form->id();

        update_post_meta( $id, 'eacf7_conditional_rules', $conditional_rules );
    }


    /*
    * Form tag
    */
    public static function add_shortcodes() {
        if ( function_exists( 'wpcf7_add_form_tag' ) ) {
            wpcf7_add_form_tag( 'conditional', array( __CLASS__, 'conditional_form_tag_handler' ), true );
        }
    }

    /**
     * Form Tag callback
     */
    public static function conditional_form_tag_handler( $tag ) {
        $tag = new \WPCF7_FormTag( $tag );

        return '<div>' . $tag->content . '</div>';
    }

    /**
     * Tag Generator
     */
    public function add_tag_generator() {
        if ( version_compare( WPCF7_VERSION, '6.0', '>=' ) ) {
            wpcf7_add_tag_generator(
                    'conditional',
                    __( 'Conditional Wrapper', 'essential-addons-for-contact-form-7' ),
                    'conditional',
                    [ $this, 'tag_generator_body_v6' ],
                    [
                            'version' => 2,
                            'name'    => 'conditional',
                    ]
            );
        } else {
            wpcf7_add_tag_generator(
                    'conditional',
                    __( 'Conditional Wrapper', 'essential-addons-for-contact-form-7' ),
                    'conditional',
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
                            __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Conditional Wrapper</a>', 'essential-addons-for-contact-form-7' ),
                            'https://softlabbd.com/docs/conditional-logic/'
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
                            'conditional' => esc_html__( 'Conditional Wrapper', 'essential-addons-for-contact-form-7' )
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
            ?>
        </footer>
        <?php
    }

    /**
     * Tag generator body callback
     */
    public function tag_generator_body( $args ) {
        $args = wp_parse_args( $args, array() );
        ?>
        <div class="control-box">

            <fieldset>

                <div class="eacf7-notice eacf7-notice-info">
                    <p>
                        <span class="dashicons dashicons-info-outline"></span>
                        <?php
                        echo sprintf(
                                __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Conditional Field</a>', 'essential-addons-for-contact-form-7' ),
                                'https://softlabbd.com/docs/conditional-logic/'
                        );
                        ?>
                    </p>
                </div>

                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr( isset( $args['content'] ) ? $args['content'] : 'conditional' . '-name' ); ?>">
                                <?php echo esc_html( __( 'Name', 'essential-addons-for-contact-form-7' ) ); ?>
                            </label>
                        </th>

                        <td>
                            <input type="text" name="name" class="tg-name oneline"
                                   id="<?php echo esc_attr( isset( $args['content'] ) ? $args['content'] : 'conditional' . '-name' ); ?>"/>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="eacf7-doc-notice eacf7-guide">
                    <?php echo esc_html__( "Additional settings can be found on the 'Conditional Fields' tab in the Essential Addons for Contact Form 7 Settings panel within the form editor. Ensure these settings are configured correctly; otherwise, the conditions may not function as expected.", 'essential-addons-for-contact-form-7' ); ?>
                </div>

            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="conditional" class="tag code" readonly="readonly" onfocus="this.select()"/>

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag"
                       value="<?php echo esc_attr( __( 'Insert Tag', 'essential-addons-for-contact-form-7' ) ); ?>"/>
            </div>
        </div>
        <?php
    }

    /**
     * Render Tag on Frontend
     */
    public function update_form_properties( $properties, $cf7 ) {
        if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            $form = $properties['form'];

            $form_parts = preg_split( '/(\[\/?conditional(?:\]|\s.*?\]))/', $form, - 1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );

            ob_start();
            $stack = [];
            foreach ( $form_parts as $form_part ) {
                if ( strpos( $form_part, '[conditional ' ) === 0 ) {
                    $tag_id  = substr( $form_part, 13, - 1 );
                    $stack[] = 'div';
                    echo '<div class="conditional ' . esc_attr( $tag_id ) . '">';
                } elseif ( $form_part === '[/conditional]' ) {
                    echo '</' . array_pop( $stack ) . '>';
                } else {
                    echo $form_part;
                }
            }

            $properties['form'] = ob_get_clean();
        }

        return $properties;
    }

    /**
     * set form hidden fields
     */
    public function set_form_hidden_fields( $hidden_fields ) {
        return array_merge( $hidden_fields, array(
                '_eacf7_hidden_conditional_fields' => '',
        ) );
    }

    public function remove_hidden_post_data( $posted_data ) {

        $this->set_hidden_fields_arrays( $posted_data );

        foreach ( $this->hidden_fields as $name => $value ) {
            unset( $posted_data[ $name ] );
        }

        return $posted_data;
    }

    /***
     * set hidden fields
     */
    public function set_hidden_fields_arrays( $posted_data = false ) {

        if ( ! $posted_data ) {
            $posted_data = WPCF7_Submission::get_instance()->get_posted_data();
        }

        if ( isset( $posted_data['_eacf7_hidden_conditional_fields'] ) ) {
            $hidden_fields = json_decode( stripslashes( $posted_data['_eacf7_hidden_conditional_fields'] ) );
        } else {
            $hidden_fields = [];
        }

        if ( is_array( $hidden_fields ) && count( $hidden_fields ) > 0 ) {
            foreach ( $hidden_fields as $field ) {

                $this->hidden_fields[] = $field;
            }
        }
    }

    /* Skip validation for hidden field */
    function skip_validation_for_hidden_fields( $result, $tags ) {

        if ( isset( $_POST ) ) {
            $this->set_hidden_fields_arrays( $_POST );
        }

        $invalid_fields = $result->get_invalid_fields();

        $return_result = new WPCF7_Validation();

        if ( count( $this->hidden_fields ) == 0 || ! is_array( $invalid_fields ) || count( $invalid_fields ) == 0 ) {
            $return_result = $result;
        } else {
            foreach ( $invalid_fields as $invalid_field_key => $invalid_field_data ) {
                if ( ! in_array( $invalid_field_key, $this->hidden_fields ) ) {
                    $return_result->invalidate( $invalid_field_key, $invalid_field_data['reason'] );
                }
            }
        }

        return $return_result;
    }

    /**
     * Handle mail properties for conditional logic
     *
     * This function will be called on the wpcf7_before_send_mail hook.
     * It will check the conditions for each group and apply the hide/show logic accordingly.
     * The conditions are checked based on the posted data and the values in the conditions.
     * The mail body and mail_2 body will be updated accordingly.
     *
     * @author Monzur Alam
     * @since 1.0.3
     */
    public function mail_properties() {
        $wpcf7      = \WPCF7_ContactForm::get_current();
        $submission = \WPCF7_Submission::get_instance();
        $id         = $wpcf7->id();

        $conditional_data = get_post_meta( $id, 'eacf7_conditional_rules', true );

        $posted_data = $submission->get_posted_data();

        $properties = $submission->get_contact_form()->get_properties();

        // Get the email body
        $mail_body   = $properties['mail']['body'];
        $mail_body_2 = $properties['mail_2']['body'];

        // check & early return if no conditional data
        if ( ! $submission || empty( $submission ) || empty( $conditional_data ) || ! is_array( $conditional_data ) || empty( $posted_data ) || ! is_array( $posted_data ) ) {
            return;
        }

        foreach ( $conditional_data as $key => $condition ) {
            $condition        = (object) $condition;
            $group_field      = $condition->field;
            $group_visibility = $condition->visibility ?? 'show';
            $group_match      = $condition->match ?? 'any';
            $group_conditions = $condition->conditions ?? [];

            // check if the condition is valid
            $condition_status = [];

            foreach ( $group_conditions as $conditions ) {
                $conditions          = $conditions;
                $conditions_field    = $conditions['field'];
                $conditions_operator = $conditions['operator'];
                $conditions_value    = $conditions['value'];

                $posted_value = is_array( $posted_data[ $conditions_field ] ) && in_array( $conditions_value, $posted_data[ $conditions_field ] ) ? $conditions_value : $posted_data[ $conditions_field ];

                // Apply the condition check
                if ( $this->check_condition( $conditions_operator, $posted_value, $conditions_value ) ) {
                    $condition_status[] = 'true';
                } else {
                    $condition_status[] = 'false';
                }
            }

            // Check if the conditions for all or any match
            if ( in_array( $group_match, [ 'all', 'any' ] ) ) {
                list( $mail_body, $mail_body_2 ) = $this->modify_mail_body( $group_field, $group_visibility, $condition_status, $mail_body, $mail_body_2 );
            }
        }

        // Mail
        $properties['mail']['body'] = $mail_body;

        // Mail 2
        $properties['mail_2']['body'] = $mail_body_2;

        $submission->get_contact_form()->set_properties( $properties );
    }

    /**
     * Check condition
     *
     * @param string $operator Operator
     * @param mixed $posted_value Value from the form
     * @param mixed $conditions_value Value from the condition
     *
     * @return bool True if the condition matches, false otherwise
     */
    public function check_condition( $operator, $posted_value, $conditions_value ) {
        switch ( $operator ) {
            case 'equal':
                return $posted_value == $conditions_value;
            case 'not_equal':
                return $posted_value != $conditions_value;
            case 'greater_than':
                return $posted_value > $conditions_value;
            case 'less_than':
                return $posted_value < $conditions_value;
            case 'greater_than_or_equal_to':
                return $posted_value >= $conditions_value;
            case 'less_than_or_equal_to':
                return $posted_value <= $conditions_value;
            default:
                return false;
        }
    }

    /**
     * Modify the mail body based on the condition status
     *
     * If the condition status is true, remove the group field tags from the mail body.
     * If the condition status is false, remove the group field content entirely from the mail body.
     *
     * @param string $group_field Group field name
     * @param string $group_visibility Group visibility
     * @param array $condition_status Condition status
     * @param string $mail_body Original mail body
     * @param string $mail_body_2 Original mail body 2
     *
     * @return array Modified mail body and mail body 2
     */
    public function modify_mail_body( $group_field, $group_visibility, $condition_status, $mail_body, $mail_body_2 ) {
        if ( ! in_array( 'false', $condition_status ) ) {
            if ( 'show' === $group_visibility ) {
                // Remove the group field tags
                $mail_body = preg_replace( '/\[' . $group_field . '\]/s', '', $mail_body );
                $mail_body = preg_replace( '/\[\/' . $group_field . '\]/s', '', $mail_body );

                // Modify Mail 2
                $mail_body_2 = preg_replace( '/\[' . $group_field . '\]/s', '', $mail_body_2 );
                $mail_body_2 = preg_replace( '/\[\/' . $group_field . '\]/s', '', $mail_body_2 );
            }
        } else {
            // Remove group field content entirely
            $mail_body = preg_replace( '/\[' . $group_field . '\].*?\[\/' . $group_field . '\]/s', '', $mail_body );

            // Modify Mail 2
            $mail_body_2 = preg_replace( '/\[' . $group_field . '\].*?\[\/' . $group_field . '\]/s', '', $mail_body_2 );
        }

        return [ $mail_body, $mail_body_2 ];
    }

    /**
     * @return Conditional|null
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

Conditional::instance();
