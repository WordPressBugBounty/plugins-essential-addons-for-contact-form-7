<?php

namespace EACF7;

if (! defined('ABSPATH')) exit;

class Math_Captcha {
    /**
     * @var null
     */
    protected static $instance = null;

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wpcf7_init', array($this, 'add_data_handler'));
        add_action('wpcf7_admin_init', array($this, 'add_tag_generator'), 99);
        add_filter('wpcf7_validate_math_captcha', array($this, 'validate'), 10, 2);
        add_filter('wpcf7_validate_math_captcha*', array($this, 'validate'), 10, 2);
        add_action('wp_ajax_eacf7_refresh_math_captcha', array($this, 'refresh_math_captcha'));
        add_action('wp_ajax_nopriv_eacf7_refresh_math_captcha', array($this, 'refresh_math_captcha'));
    }

    /**
     * Add Contact Form Tag
     */
    public function add_data_handler() {
        if (function_exists('wpcf7_add_form_tag')) {
            wpcf7_add_form_tag(
                array('math_captcha', 'math_captcha*'),
                array($this, 'render_math_captcha'),
                array('name-attr' => true)
            );
        }
    }

    /**
     * Captcha content
     */
    public function cf7_math_captcha_content($tag, $prefix_label = '') {
        echo $prefix_label;
        $tag                = ! empty($_REQUEST['tagname']) ? sanitize_text_field($_REQUEST['tagname']) : $tag->name;
        $operator           = array('+', '-', '*');
        $random_operator    = array_rand($operator);
        $current_operator   = $operator[$random_operator];
        $math_first_value   = rand(1, 9);
        $math_second_value  = rand(1, 9);
        $loading            = EACF7_ASSETS . '/images/icons/loading.svg';
        $reload             = EACF7_ASSETS . '/images/icons/reload.svg';
        $html = sprintf(
            '<div class="cf7-math-captcha-wrap"><label>%s</label>',
            sprintf(
                __('What is %1$s %2$s %3$s?', 'essential-addons-for-contact-form-7'),
                $math_first_value,
                $current_operator,
                $math_second_value
            )
        );        
        $html .= sprintf('<a href="javascript: void(0)" class="cf7-math-captcha-refresh"><img src="%1$s" class="math-captcha-loading" /> <img src="%2$s" class="math-captcha-reload" /> </a>', $loading, $reload);
        $html .= sprintf('<input type="hidden" name="%1$s" value="%2$s" />', $tag . '-first', $math_first_value);
        $html .= sprintf('<input type="hidden" name="%1$s" value="%2$s" />', $tag . '-operator', $current_operator);
        $html .= sprintf('<input type="hidden" name="%1$s" value="%2$s" /></div>', $tag . '-second', $math_second_value);

        return $html;
    }

    /**
     * Handler callback
     *
     * @return void
     */
    public function render_math_captcha($tag) {

        // check and make sure tag name is not empty
        if (empty($tag->name)) {
            return '';
        }

        $validation_error = wpcf7_get_validation_error($tag->name);

        $class = wpcf7_form_controls_class($tag->type);

        if ($validation_error) {
            $class .= ' wpcf7-not-valid';
        }

        $display        = ! empty($tag->get_option('display', '', true)) ? $tag->get_option('display', '', true) : '';
        $prefix_label   = ! empty($tag->get_option('prefix_label', '', true)) ? $tag->get_option('prefix_label', '', true) : '';

        $atts = [
            'class'        => $tag->get_class_option($class),
            'id'           => $tag->get_id_option(),
            'name'         => $tag->name,
            'aria-invalid' => $validation_error ? 'true' : 'false',
            'placeholder'  => esc_html__('Type your answer', 'essential-addons-for-contact-form-7')
        ];

        if ($tag->is_required()) {
            $atts['aria-required'] = 'true';
        }

        $atts               = wpcf7_format_atts($atts);

        ob_start();
        $html = $this->cf7_math_captcha_content($tag, $prefix_label);
        $html .= sprintf('<input %1$s />%2$s', $atts, $validation_error);

?>
        <span class="cf7-math-captcha wpcf7-form-control-wrap <?php echo ('inline' == $display) ? sanitize_html_class('cf7-math-captcha-inline') : sanitize_html_class('cf7-math-captcha-block'); ?> <?php echo sanitize_html_class($tag->name); ?>" data-name="<?php echo sanitize_html_class($tag->name); ?>">
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

        if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
            $tag_generator->add(
                'math_captcha',
                __('Math Captcha', 'essential-addons-for-contact-form-7'),
                [$this, 'tag_generator_body_v6'],
                [
                    'version'     => 2,
                ]
            );
        } else {
            $tag_generator->add(
                'math_captcha',
                __('Math Captcha', 'essential-addons-for-contact-form-7'),
                [$this, 'tag_generator_body']
            );
        }
    }

    public function tag_generator_body_v6($contact_form, $options) {
        $tgg = new \WPCF7_TagGeneratorGenerator($options['content']);

    ?>
        <header class="description-box">
            <div class="eacf7-notice eacf7-notice-info">
                <p>
                    <span class="dashicons dashicons-info-outline"></span>
                    <?php
                    echo sprintf(
                        __('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Math Captcha</a>', 'essential-addons-for-contact-form-7'),
                        'https://softlabbd.com/docs/'
                    );
                    ?>
                </p>
            </div>
        </header>
        <div class="control-box">
            <?php
            $tgg->print('field_type', array(
                'with_required' => false,
                'select_options' => array(
                    'math_captcha' => esc_html__('Math Captcha', 'essential-addons-for-contact-form-7'),
                ),
            ));

            $tgg->print('field_name');

            $tgg->print('class_attr');
            ?>

            <fieldset>
                <legend><?php echo esc_html__('Display', 'essential-addons-for-contact-form-7'); ?></legend>
                <p class="oneline address-format">
                    <label>
                        <input type="radio" data-tag-part="option" data-tag-option="display:" name="display" value="inline" checked />
                        <?php echo esc_html__('Inline', 'essential-addons-for-contact-form-7'); ?>
                    </label>

                    <label>
                        <input type="radio" data-tag-part="option" data-tag-option="display:" name="display" value="block" />
                        <?php echo esc_html__('Block', 'essential-addons-for-contact-form-7'); ?>
                    </label>
                </p>
            </fieldset>

        </div>
        <footer class="insert-box">
            <?php
            $tgg->print('insert_box_content');

            $tgg->print('mail_tag_tip');
            ?>
        </footer>
    <?php
    }

    /**
     * Tag Generator callback method
     *
     * @return void
     */
    public function tag_generator_body($contact_form, $args = '') {
        $args = wp_parse_args($args, array());
        $eacf7_field_type = 'math_captcha';
    ?>
        <div class="control-box">
            <fieldset>
                <div class="eacf7-notice eacf7-notice-info">
                    <p>
                        <span class="dashicons dashicons-info-outline"></span>
                        <?php
                        echo sprintf(
                            __('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Math Captcha</a>', 'essential-addons-for-contact-form-7'),
                            'https://softlabbd.com/docs-category/contact-form-7-extended/'
                        );
                        ?>
                    </p>
                </div>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($args['content'] . '-name'); ?>"><?php echo esc_html__('Name', 'essential-addons-for-contact-form-7'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr($args['content'] . '-name'); ?>">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for=""><?php echo esc_html__('Display', 'essential-addons-for-contact-form-7'); ?></label>
                            </th>
                            <td>
                                <label for="inline">
                                    <input type="radio" name="display" class="option" id="inline" value="inline"><?php echo esc_html__('Inline', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                                <label for="block">
                                    <input type="radio" name="display" class="option" id="block" value="block" checked><?php echo esc_html__('Block', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="class-attributes"><?php echo esc_html__('Class', 'essential-addons-for-contact-form-7'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="class" class="class-attributes oneline option" id="class-attributes" placeholder="">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="insert-box">
            <input type="text" name="<?php echo esc_attr($eacf7_field_type) ?>" class="tag code" readonly onfocus="this.select()">
            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr(__('Insert Tag', 'essential-addons-for-contact-form-7')); ?>">
            </div>

            <br class="clear" />

            <p class="description mail-tag">
                <label for="<?php echo esc_attr($args['content'] . '-mailtag'); ?>">
                    <?php printf('To display the math captcha value in the email, insert the mail-tag (%s) in the Mail tab.', '<strong><span class="mail-tag"></span></strong>'); ?>
                    <input type="text" class="mail-tag code eacf7-hidden" readonly="readonly" id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" />
                </label>
            </p>
        </div>
<?php
    }

    /**
     * Validation filter
     * @param [type] $result
     * @param [type] $tag
     * @return void
     * @author monzuralam
     * @since 1.0.0
     */
    public function validate($result, $tag) {
        $final_result       = '';
        $math_first         = sanitize_text_field($_POST[$tag->name . '-first']);
        $math_operator      = sanitize_text_field($_POST[$tag->name . '-operator']);
        $math_second        = sanitize_text_field($_POST[$tag->name . '-second']);

        if ('*' == $math_operator) {
            $final_result = ($math_first * $math_second);
        } elseif ('+' == $math_operator) {
            $final_result = ($math_first + $math_second);
        } else {
            $final_result = ($math_first - $math_second);
        }

        $captcha_value = isset($_POST[$tag->name]) ? trim(wp_unslash(strtr((string) $_POST[$tag->name], "\n", " "))) : '';

        if ('' == $captcha_value) {
            $result->invalidate($tag, apply_filters('math_captcha_required', esc_html__('Please enter Captcha.', 'essential-addons-for-contact-form-7')));
        }

        if ($captcha_value != '' && $captcha_value != $final_result) {
            $result->invalidate($tag, apply_filters('math_captcha_invalidate', esc_html__('Incorrect Captcha!', 'essential-addons-for-contact-form-7')));
        }

        return $result;
    }

    public function refresh_math_captcha($tag) {
        $tag = $_POST['tagname'];
        echo $this->cf7_math_captcha_content($tag);

        wp_die();
    }

    /**
     * @return Math_Captcha|null
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

Math_Captcha::instance();
