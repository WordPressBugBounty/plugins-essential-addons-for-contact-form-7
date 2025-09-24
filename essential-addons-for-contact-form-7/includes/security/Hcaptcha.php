<?php

namespace EACF7\Security;

if (! defined('ABSPATH')) {
    exit;
}

class Hcaptcha {
    /**
     * @var null
     */
    protected static $instance = null;

    public function __construct() {
        add_action('wpcf7_enqueue_scripts', array($this, 'assets'));
        add_action('wpcf7_init', array($this, 'add_data_handler'));
        add_action('wpcf7_admin_init', array($this, 'add_tag_generator'), 99);
        add_filter('wpcf7_validate_hcaptcha', array($this, 'validate'), 10, 2);
    }

    public function assets() {
        wp_enqueue_script('hcaptcha', 'https://www.hcaptcha.com/1/api.js', [], null, true);
    }

    /**
     * Add Contact Form Tag
     */
    public function add_data_handler() {
        if (function_exists('wpcf7_add_form_tag')) {
            wpcf7_add_form_tag(
                array('hcaptcha'),
                array($this, 'render_hcaptcha'),
                array(
                    'name-attr'    => true,
                    'do-not-store' => true,
                    'not-for-mail' => true,
                )
            );
        }
    }

    /**
     * Handler callback
     *
     * @return void
     */
    public function render_hcaptcha($tag) {

        // check and make sure tag name is not empty
        if (empty($tag->name)) {
            return '';
        }

        $validation_error = wpcf7_get_validation_error($tag->name);

        $class = wpcf7_form_controls_class($tag->type);

        if ($validation_error) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = [
            'type'         => 'hidden',
            'class'        => $tag->get_class_option(),
            'id'           => $tag->get_id_option(),
            'name'         => $tag->name,
            'aria-invalid' => $validation_error ? 'true' : 'false',
            'area-required' => 'true',
        ];

        $atts = wpcf7_format_atts($atts);

        $theme = ! empty( $tag->get_option('theme', '', true) ) ? $tag->get_option('theme', '', true) : 'light';

        ob_start();
        $html = '';
        $html .= sprintf('<input %1$s>%2$s', $atts, $validation_error);
?>
        <div class='h-captcha' data-sitekey="<?php echo eacf7_get_settings('hcaptchaSiteKey'); ?>" data-theme="<?php echo esc_attr($theme); ?>"></div>
        <span class="eacf7-hcaptcha wpcf7-form-control-wrap"
            data-name="<?php echo sanitize_html_class($tag->name); ?>">
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
                'hcaptcha',
                __('hCaptcha', 'essential-addons-for-contact-form-7'),
                [$this, 'tag_generator_body_v6'],
                ['version'     => 2]
            );
        } else {
            $tag_generator->add(
                'hcaptcha',
                __('hCaptcha', 'essential-addons-for-contact-form-7'),
                [$this, 'tag_generator_body']
            );
        }
    }

    /**
     * Tag Generator
     * @since 1.0.1
     * @author monzuralam
     */
    public function tag_generator_body_v6($contact_form, $options) {
        $tgg = new \WPCF7_TagGeneratorGenerator($options['content']);

    ?>
        <header class="description-box">
            <div class="eacf7-notice eacf7-notice-info">
                <p>
                    <span class="dashicons dashicons-info-outline"></span>
                    <?php
                    echo sprintf(
                        __('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">hCaptcha</a>', 'essential-addons-for-contact-form-7'),
                        'https://softlabbd.com/docs/hcaptcha/'
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
                    'hcaptcha' => esc_html__('hCaptcha', 'essential-addons-for-contact-form-7'),
                ),
            ));

            $tgg->print('field_name');

            ?>
            <!-- Theme -->
            <fieldset>
                <legend><?php echo esc_html__('Theme', 'essential-addons-for-contact-form-7'); ?></legend>
                <p class="<?php echo ! eacf7_fs()->can_use_premium_code__premium_only() ? esc_attr('pro-feature') : ''; ?>">
                    <label for="light">
                        <input type="radio" data-tag-part="option" data-tag-option="theme:" name="theme" class="option" id="light" value="light" selected><?php echo esc_html__('Light', 'essential-addons-for-contact-form-7'); ?>
                    </label>
                    <label for="dark">
                        <input type="radio" data-tag-part="option" data-tag-option="theme:" name="theme" class="option" id="dark" value="dark"><?php echo esc_html__('Dark', 'essential-addons-for-contact-form-7'); ?>
                    </label>
                </p>
            </fieldset>
            <?php

            $tgg->print('class_attr');

            $tgg->print('id_attr');
            ?>
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
        $type = 'hcaptcha';
    ?>
        <div class="control-box">
            <fieldset>
                <div class="eacf7-notice eacf7-notice-info">
                    <p>
                        <span class="dashicons dashicons-info-outline"></span>
                        <?php
                        echo sprintf(
                            __('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Honeypot</a>', 'essential-addons-for-contact-form-7'),
                            'https://softlabbd.com/docs/hcaptcha/'
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
                                <input type="text" name="name" class="tg-name oneline"
                                    id="<?php echo esc_attr($args['content'] . '-name'); ?>">
                            </td>
                        </tr>
                        <tr>
							<th scope="row">
								<label for=""><?php echo esc_html__('Theme', 'essential-addons-for-contact-form-7'); ?></label>
							</th>
							<td class="<?php echo ! eacf7_fs()->can_use_premium_code__premium_only() ? esc_attr('pro-feature') : ''; ?>">
								<label for="light">
									<input type="radio" name="theme" class="option" id="light" value="light" selected>
                                    <?php echo esc_html__('Light', 'essential-addons-for-contact-form-7'); ?>
								</label>
								<label for="dark">
									<input type="radio" name="theme" class="option" id="dark" value="dark">
                                    <?php echo esc_html__('Dark', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</td>
						</tr>
                        <tr>
                            <th scope="row">
                                <label for="class-attributes"><?php echo esc_html__('Class Attributes', 'essential-addons-for-contact-form-7'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="class" class="class-attributes oneline option"
                                    id="class-attributes" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="id-attributes"><?php echo esc_html__('ID', 'essential-addons-for-contact-form-7'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="id" class="class-attributes oneline option" id="id-attributes" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="<?php echo esc_attr($type) ?>" class="tag code" readonly onfocus="this.select()">
            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag"
                    value="<?php echo esc_attr(__('Insert Tag', 'essential-addons-for-contact-form-7')); ?>">
            </div>

            <br class="clear" />

            <p class="description mail-tag">
                <label for="<?php echo esc_attr($args['content'] . '-mailtag'); ?>">
                    <?php printf('To display the honeypot in the email, insert the mail-tag (%s) in the Mail tab.', '<strong><span class="mail-tag"></span></strong>'); ?>
                    <input type="text" class="mail-tag code eacf7-hidden" readonly="readonly"
                        id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" />
                </label>
            </p>
        </div>

<?php
    }

    public function validate($result, $tag) {
        $tag   = new \WPCF7_FormTag($tag);

        $hcaptcha_response = isset($_POST['h-captcha-response']) ? sanitize_text_field($_POST['h-captcha-response']) : '';

        if (empty($hcaptcha_response)) {
            $result->invalidate($tag, esc_html__('Please complete the hCaptcha verification.', 'essential-addons-for-contact-form-7'));
            return $result;
        }

        $secret_key = eacf7_get_settings('hcaptchaSecretKey');

        if (empty($secret_key)) {
            $result->invalidate($tag, esc_html__('hCaptcha secrect key error.', 'essential-addons-for-contact-form-7'));
            return $result;
        }

        $response = wp_remote_post('https://hcaptcha.com/siteverify', [
            'body' => [
                'secret' => $secret_key,
                'response' => $hcaptcha_response,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
            ],
        ]);

        $response_body = wp_remote_retrieve_body($response);
        $result_data = json_decode($response_body, true);

        if (!$result_data['success'] && empty($hcaptcha_response)) {
            $result->invalidate($tag, esc_html__('hCaptcha verification failed. Please try again.', 'essential-addons-for-contact-form-7'));
        }

        return $result;
    }

    /**
     * @return Hcaptcha|null
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

Hcaptcha::instance();
