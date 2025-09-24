<?php

namespace EACF7;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
class Range_Slider {
    /**
     * @var null
     */
    protected static $instance = null;

    public function __construct() {
        // add range slider data to localize script
        add_filter( 'eacf7_localize_data', array($this, 'add_localize_data') );
        add_action( 'wpcf7_admin_init', [$this, 'add_tag_generator'], 99 );
        add_action( 'wpcf7_init', [$this, 'add_data_handler'] );
        add_action( 'wpcf7_enqueue_scripts', array($this, 'enqueue_scripts') );
        add_filter(
            'wpcf7_validate_range_slider',
            [$this, 'validate'],
            10,
            2
        );
        add_filter(
            'wpcf7_validate_range_slider*',
            [$this, 'validate'],
            10,
            2
        );
        add_filter(
            'wpcf7_contact_form_properties',
            [$this, 'form_properties'],
            10,
            2
        );
        add_action( 'eacf7_range_slider_style', [$this, 'eacf7_range_slider_style'] );
        add_action( 'wpcf7_after_save', array($this, 'save_data') );
    }

    public function add_localize_data( $data ) {
        if ( eacf7_is_editor_page() ) {
            $data['rangeSliderData'] = $this->get_range_slider_data();
        }
        return $data;
    }

    public function get_range_slider_data( $form_id = null ) {
        if ( !$form_id ) {
            $form_id = eacf7_get_current_form_id();
        }
        $data = get_post_meta( $form_id, 'eacf7_range_slider_data', true );
        return ( !empty( $data ) ? $data : [] );
    }

    public function validate( $result, $tag ) {
        $tag = new \WPCF7_FormTag($tag);
        $name = $tag->name;
        $value = ( isset( $_POST[$name] ) ? $_POST[$name] : '' );
        if ( $tag->is_required() && empty( $value ) ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
        }
        return $result;
    }

    /**
     * Add shortcode handler to CF7.
     */
    public function add_data_handler() {
        if ( function_exists( 'wpcf7_add_form_tag' ) ) {
            wpcf7_add_form_tag( ['range_slider', 'range_slider*'], [$this, 'render_range_slider'], [
                'name-attr' => true,
            ] );
        }
    }

    public function render_range_slider( $tag ) {
        $tag = new \WPCF7_FormTag($tag);
        // check and make sure tag name is not empty
        if ( empty( $tag->name ) ) {
            return '';
        }
        // Validate our fields
        $validation_error = wpcf7_get_validation_error( $tag->name );
        $class = $tag->get_class_option( 'wpcf7-form-control eacf7-range-slider' );
        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }
        $default = intval( $tag->get_option( 'default', '', true ) );
        $style = ( is_array( $tag->values ) && !empty( $tag->values ) ? intval( $tag->values[0] ) : 0 );
        $atts = [
            'class'         => $class,
            'id'            => $tag->get_id_option(),
            'name'          => $tag->name,
            'aria-invalid'  => ( $validation_error ? 'true' : 'false' ),
            'aria-required' => ( $tag->is_required() ? 'true' : 'false' ),
            'min'           => $tag->get_option( 'min', 'int', true ),
            'max'           => $tag->get_option( 'max', 'int', true ),
            'step'          => $tag->get_option( 'step', 'int', true ),
            'value'         => $default,
            'data-style'    => $style,
        ];
        $atts = wpcf7_format_atts( $atts );
        ob_start();
        ?>

        <span class="wpcf7-form-control-wrap" data-name="<?php 
        echo esc_attr( $tag->name );
        ?>">

            <?php 
        switch ( $style ) {
            case '1':
                break;
            case '2':
                break;
            case '3':
                break;
            default:
                ?>
                    <input type="range" <?php 
                echo $atts;
                ?> />

                    <span class="eacf7-range-slider-value"><?php 
                echo esc_html( $default );
                ?></span>
                    <?php 
                break;
        }
        ?>

            <?php 
        echo $validation_error;
        ?>
        </span>
        <?php 
        return ob_get_clean();
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'eacf7-range-slider',
            EACF7_ASSETS . '/vendor/rangeslider/rangeslider.css',
            [],
            '2.3.2'
        );
        wp_enqueue_style( 'eacf7-frontend' );
        wp_enqueue_script(
            'eacf7-range-slider',
            EACF7_ASSETS . '/vendor/rangeslider/rangeslider.js',
            ['jquery'],
            '2.3.2',
            true
        );
        wp_enqueue_script( 'eacf7-frontend' );
    }

    public function add_tag_generator() {
        $tag_generator = \WPCF7_TagGenerator::get_instance();
        if ( version_compare( WPCF7_VERSION, '6.0', '>=' ) ) {
            $tag_generator->add(
                'range_slider',
                __( 'Range Slider', 'essential-addons-for-contact-form-7' ),
                [$this, 'tag_generator_body_v6'],
                [
                    'version' => 2,
                ]
            );
        } else {
            $tag_generator->add( 'range_slider', __( 'Range Slider', 'essential-addons-for-contact-form-7' ), [$this, 'tag_generator_body'] );
        }
    }

    /**
     * Tag Generator v6
     * @since 1.0.1
     * @author monzuralam
     */
    public function tag_generator_body_v6( $contact_form, $options ) {
        $tgg = new \WPCF7_TagGeneratorGenerator($options['content']);
        ?>
        <header class="description-box">
            <div class="eacf7-notice eacf7-notice-info">
                <p>
                    <span class="dashicons dashicons-info-outline"></span>
                    <?php 
        echo sprintf( __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Range Slider</a>', 'essential-addons-for-contact-form-7' ), 'https://softlabbd.com/docs/range-slider/' );
        ?>
                </p>
            </div>
        </header>
        <div class="control-box">
            <?php 
        $tgg->print( 'field_type', array(
            'with_required'  => true,
            'select_options' => array(
                'range_slider' => esc_html__( 'Range Slider', 'essential-addons-for-contact-form-7' ),
            ),
        ) );
        $tgg->print( 'field_name' );
        ?>

            <fieldset>
                <legend>
                    <?php 
        echo esc_html__( 'Style', 'essential-addons-for-contact-form-7' );
        echo ( !eacf7_fs()->can_use_premium_code__premium_only() ? esc_html__( ' (Pro)', 'essential-addons-for-contact-form-7' ) : '' );
        ?>
                </legend>
                <p class="oneline">
                    <label>
                        <select data-tag-part="value" <?php 
        echo ( eacf7_fs()->can_use_premium_code__premium_only() ? 'name="values"' : '' );
        ?>>
                            <option <?php 
        echo ( eacf7_fs()->can_use_premium_code__premium_only() ? 'value="0"' : '' );
        ?>><?php 
        echo esc_html__( 'Default', 'essential-addons-for-contact-form-7' );
        ?></option>
                            <option <?php 
        echo ( eacf7_fs()->can_use_premium_code__premium_only() ? 'value="1"' : '' );
        ?>><?php 
        echo esc_html__( 'One', 'essential-addons-for-contact-form-7' );
        ?></option>
                            <option <?php 
        echo ( eacf7_fs()->can_use_premium_code__premium_only() ? 'value="2"' : '' );
        ?>><?php 
        echo esc_html__( 'Two', 'essential-addons-for-contact-form-7' );
        ?></option>
                            <option <?php 
        echo ( eacf7_fs()->can_use_premium_code__premium_only() ? 'value="3"' : '' );
        ?>><?php 
        echo esc_html__( 'Three', 'essential-addons-for-contact-form-7' );
        ?></option>
                        </select>
                    </label>
                </p>
            </fieldset>

            <fieldset>
                <legend><?php 
        echo esc_html__( 'Min', 'essential-addons-for-contact-form-7' );
        ?></legend>
                <p class="oneline">
                    <label>
                        <input type="number" data-tag-part="option" data-tag-option="min:" name="min" value="0"/>
                    </label>
                </p>
            </fieldset>

            <fieldset>
                <legend><?php 
        echo esc_html__( 'Max', 'essential-addons-for-contact-form-7' );
        ?></legend>
                <p class="oneline">
                    <label>
                        <input type="number" data-tag-part="option" data-tag-option="max:" name="max" value="100"/>
                    </label>
                </p>
            </fieldset>

            <fieldset>
                <legend><?php 
        echo esc_html__( 'Step', 'essential-addons-for-contact-form-7' );
        ?></legend>
                <p class="oneline">
                    <label>
                        <input type="number" data-tag-part="option" data-tag-option="step:" name="step" value="1"/>
                    </label>
                </p>
            </fieldset>

            <fieldset>
                <legend><?php 
        echo esc_html__( 'Default', 'essential-addons-for-contact-form-7' );
        ?></legend>
                <p class="oneline">
                    <label>
                        <input type="number" data-tag-part="option" data-tag-option="default:" name="default"
                               value="0"/>
                    </label>
                </p>
            </fieldset>

            <?php 
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
     * Tag Generator
     * @since 1.0.0
     * @author monzuralam
     */
    public function tag_generator_body( $contact_form, $args = '' ) {
        $args = wp_parse_args( $args, [] );
        $type = 'range_slider';
        $description = esc_html__( 'Generate a form-tag for a field for entering range_slider number.', 'essential-addons-for-contact-form-7' );
        ?>
        <div class="control-box">
            <fieldset>
                <div class="eacf7-notice eacf7-notice-info">
                    <p>
                        <span class="dashicons dashicons-info-outline"></span>
                        <?php 
        echo sprintf( __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Range Slider</a>', 'essential-addons-for-contact-form-7' ), 'https://softlabbd.com/docs/range-slider/' );
        ?>
                    </p>
                </div>

                <table class="form-table">
                    <tbody>

                    <!-- Field Type -->
                    <tr>
                        <th scope="row"><?php 
        echo esc_html__( 'Field type', 'essential-addons-for-contact-form-7' );
        ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php 
        echo esc_html__( 'Field type', 'essential-addons-for-contact-form-7' );
        ?></legend>
                                <label>
                                    <input type="checkbox"
                                           name="required"/> <?php 
        echo esc_html__( 'Required field', 'essential-addons-for-contact-form-7' );
        ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <!-- Name -->
                    <tr>
                        <th scope="row">
                            <label for="<?php 
        echo esc_attr( $args['content'] . '-name' );
        ?>">
                                <?php 
        echo esc_html__( 'Name', 'essential-addons-for-contact-form-7' );
        ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline"
                                   id="<?php 
        echo esc_attr( $args['content'] . '-name' );
        ?>"/>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <label>
                                <?php 
        echo esc_html__( 'Style', 'essential-addons-for-contact-form-7' );
        echo ( !eacf7_fs()->can_use_premium_code__premium_only() ? esc_html__( ' (Pro)', 'essential-addons-for-contact-form-7' ) : '' );
        ?>
                            </label>
                        </th>
                        <td>
                            <select <?php 
        echo ( eacf7_fs()->can_use_premium_code__premium_only() ? ' name="values"' : '' );
        ?>>
                                <option <?php 
        echo ( eacf7_fs()->can_use_premium_code__premium_only() ? 'value="0"' : '' );
        ?>><?php 
        echo esc_html__( 'Default', 'essential-addons-for-contact-form-7' );
        ?></option>
                                <option <?php 
        echo ( eacf7_fs()->can_use_premium_code__premium_only() ? 'value="1"' : '' );
        ?>><?php 
        echo esc_html__( 'One', 'essential-addons-for-contact-form-7' );
        ?></option>
                                <option <?php 
        echo ( eacf7_fs()->can_use_premium_code__premium_only() ? 'value="2"' : '' );
        ?>><?php 
        echo esc_html__( 'Two', 'essential-addons-for-contact-form-7' );
        ?></option>
                                <option <?php 
        echo ( eacf7_fs()->can_use_premium_code__premium_only() ? 'value="3"' : '' );
        ?>><?php 
        echo esc_html__( 'Three', 'essential-addons-for-contact-form-7' );
        ?></option>
                            </select>
                        </td>
                    </tr>

                    <!-- ID -->
                    <tr>
                        <th scope="row">
                            <label for="<?php 
        echo esc_attr( $args['content'] . '-id' );
        ?>">
                                <?php 
        echo esc_html__( 'ID', 'essential-addons-for-contact-form-7' );
        ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" name="id" class="idvalue oneline option"
                                   id="<?php 
        echo esc_attr( $args['content'] . '-id' );
        ?>"/>
                        </td>
                    </tr>

                    <!-- Class -->
                    <tr>
                        <th scope="row">
                            <label for="<?php 
        echo esc_attr( $args['content'] . '-class' );
        ?>">
                                <?php 
        echo esc_html__( 'Class', 'essential-addons-for-contact-form-7' );
        ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" name="class" class="classvalue oneline option"
                                   id="<?php 
        echo esc_attr( $args['content'] . '-class' );
        ?>"/>
                        </td>
                    </tr>

                    <!-- Min -->
                    <tr>
                        <th scope="row">
                            <label for="<?php 
        echo esc_attr( $args['content'] . '-min' );
        ?>">
                                <?php 
        echo esc_html__( 'Min', 'essential-addons-for-contact-form-7' );
        ?>
                            </label>
                        </th>
                        <td>
                            <input type="number" name="min" class="minvalue oneline option"
                                   id="<?php 
        echo esc_attr( $args['content'] . '-min' );
        ?>" value="0"/>
                        </td>
                    </tr>

                    <!-- Max -->
                    <tr>
                        <th scope="row">
                            <label for="<?php 
        echo esc_attr( $args['content'] . '-max' );
        ?>">
                                <?php 
        echo esc_html__( 'Max', 'essential-addons-for-contact-form-7' );
        ?>
                            </label>
                        </th>
                        <td>
                            <input type="number" name="max" class="maxvalue oneline option"
                                   id="<?php 
        echo esc_attr( $args['content'] . '-max' );
        ?>" value="100"/>
                        </td>
                    </tr>

                    <!-- Step -->
                    <tr>
                        <th scope="row">
                            <label for="<?php 
        echo esc_attr( $args['content'] . '-step' );
        ?>">
                                <?php 
        echo esc_html__( 'Step', 'essential-addons-for-contact-form-7' );
        ?>
                            </label>
                        </th>
                        <td>
                            <input type="number" name="step" class="stepvalue oneline option"
                                   id="<?php 
        echo esc_attr( $args['content'] . '-step' );
        ?>" value="1"/>
                        </td>
                    </tr>

                    <!-- Default Value -->
                    <tr>
                        <th scope="row">
                            <label for="<?php 
        echo esc_attr( $args['content'] . '-default' );
        ?>">
                                <?php 
        echo esc_html__( 'Default Value', 'essential-addons-for-contact-form-7' );
        ?>
                            </label>
                        </th>
                        <td>
                            <input type="number" name="default" class="defaultvalue oneline option"
                                   id="<?php 
        echo esc_attr( $args['content'] . '-default' );
        ?>" value="0"/>
                        </td>
                    </tr>


                    </tbody>
                </table>
            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="<?php 
        echo esc_attr( $type );
        ?>" class="tag code" readonly="readonly"
                   onfocus="this.select()"/>

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag"
                       value="<?php 
        echo esc_attr__( 'Insert Tag', 'essential-addons-for-contact-form-7' );
        ?>"/>
            </div>

            <br class="clear"/>

            <p class="description mail-tag">
                <label for="<?php 
        echo esc_attr( $args['content'] . '-mailtag' );
        ?>">
                    <?php 
        printf( 'To display the range-slider value in the email, insert the mail-tag (%s) in the Mail tab.', '<strong><span class="mail-tag"></span></strong>' );
        ?>
                    <input type="text" class="mail-tag code eacf7-hidden" readonly="readonly"
                           id="<?php 
        echo esc_attr( $args['content'] . '-mailtag' );
        ?>"/>
                </label>
            </p>
        </div>
        <?php 
    }

    /**
     * Form Properties
     */
    public function form_properties( $properties, $form ) {
        if ( !is_admin() ) {
            $cf7_form = $properties['form'];
            $eacf7_range_slider_status = ( str_contains( $properties['form'], 'range_slider' ) ? 1 : 0 );
            ob_start();
            if ( $eacf7_range_slider_status ) {
                do_action( 'eacf7_range_slider_style', $form->id() );
            }
            echo $cf7_form;
            $cf7_form = ob_get_clean();
            $properties['form'] = $cf7_form;
        }
        return $properties;
    }

    public function eacf7_range_slider_style( $form_id ) {
        $eacf7_range_slider_data = ( !empty( get_post_meta( $form_id, 'eacf7_range_slider_data', true ) ) ? get_post_meta( $form_id, 'eacf7_range_slider_data', true ) : [] );
        $eacf7_selection_color = ( !empty( $eacf7_range_slider_data['selectionColor'] ) ? sanitize_hex_color( $eacf7_range_slider_data['selectionColor'] ) : '' );
        $eacf7_handle_color = ( !empty( $eacf7_range_slider_data['handleColor'] ) ? sanitize_hex_color( $eacf7_range_slider_data['handleColor'] ) : '' );
        $css = '<style>';
        $css .= ".eacf7-form-{$form_id} .rangeslider__fill,";
        $css .= ".eacf7-form-{$form_id} .rangeslider__labels__label:first-child:before{";
        $css .= "background: {$eacf7_selection_color} !important;";
        $css .= "}";
        $css .= ".eacf7-form-{$form_id} .rangeslider .rangeslider__handle,";
        $css .= ".eacf7-form-{$form_id} .rangeslider .rangeslider__value-bubble,";
        $css .= ".eacf7-form-{$form_id} .rangeslider .rangeslider__handle .rangeslider__handle__value{";
        $css .= "background: {$eacf7_handle_color} !important;";
        $css .= "border-color: {$eacf7_handle_color} !important;";
        $css .= "}";
        $css .= ".eacf7-form-{$form_id} .rangeslider .rangeslider__value-bubble::before,";
        $css .= ".eacf7-form-{$form_id} .rangeslider .rangeslider__handle__value::before{";
        $css .= "border-top-color: {$eacf7_handle_color} !important;";
        $css .= "}";
        $css .= '</style>';
        echo $css;
    }

    /**
     * Save meta
     */
    public function save_data( $contact_form ) {
        $post_id = $contact_form->id();
        if ( empty( $_POST['eacf7_range_slider_data'] ) ) {
            return;
        }
        $range_slider_data = stripslashes( $_POST['eacf7_range_slider_data'] );
        $range_slider_data = json_decode( $range_slider_data, true );
        update_post_meta( $post_id, 'eacf7_range_slider_data', $range_slider_data );
    }

    /**
     * @return Range_Slider|null
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}

Range_Slider::instance();