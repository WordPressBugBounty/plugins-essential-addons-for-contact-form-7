<?php

namespace EACF7;

if (! defined('ABSPATH')) {
    exit;
}

class Hook {
    /**
     * @var null
     */
    protected static $instance = null;

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wpcf7_admin_init', [$this, 'add_tag_generator'], 99);
        add_action('wpcf7_init', [$this, 'add_data_handler']);

        add_action('wpcf7_enqueue_scripts', array($this, 'enqueue_scripts'), 1);
    }

    /**
     * Tag Generator
     * @since 1.0.0
     * @author monzuralam
     */
    public function add_tag_generator() {
        if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
            wpcf7_add_tag_generator(
                'hook',
                __('Action Hook', 'essential-addons-for-contact-form-7'),
                'hook',
                [$this, 'tag_generator_body_v6'],
                [
                    'version'     => 2,
                ]
            );
        } else {
            wpcf7_add_tag_generator(
                'hook',
                __('Action Hook', 'essential-addons-for-contact-form-7'),
                'hook',
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
                        __('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Action Hook</a>', 'essential-addons-for-contact-form-7'),
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
                    'hook' => esc_html__('Hook', 'essential-addons-for-contact-form-7'),
                ),
            ));

            $tgg->print('field_name'); ?>

            <fieldset>
                <legend><?php echo esc_html__('Hook', 'essential-addons-for-contact-form-7'); ?></legend>
                <p class="oneline address-format">
                    <label>
                        <input type="text" data-tag-part="content" name="content" id="tag-generator-panel-hook-content" />
                    </label>
                <p><?php echo esc_html__('An option for developers to add dynamic elements they want. It provides the chance to add whatever input type you want to add in this form. You\'ll be given 1 parameter to play with: $form.', 'essential-addons-for-contact-form-7') ?>
                <pre class="hook-code">
add_action('custom_hook','custom_hook_callback' );
function custom_hook_callback( $form) {
    // do what ever you want
}
</pre>
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
     * Tag Generator Box
     * @since 1.0.0
     * @author monzuralam
     */
    public function tag_generator_body($contact_form, $args = '') {
        $args = wp_parse_args($args, []);
        $type = isset($args['id']) ? $args['id'] : '';
    ?>
        <div class="control-box">

            <fieldset>
                <div class="eacf7-notice eacf7-notice-info">
                    <p>
                        <span class="dashicons dashicons-info-outline"></span>
                        <?php
                        echo sprintf(
                            __('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Action Hook</a>', 'essential-addons-for-contact-form-7'),
                            'https://softlabbd.com/docs/'
                        );
                        ?>
                    </p>
                </div>

                <table class="form-table">
                    <tbody>
                        <!-- Name -->
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($args['content'] . '-name'); ?>">
                                    <?php echo esc_html__('Name', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="name" class="tg-name oneline"
                                    id="<?php echo esc_attr($args['content'] . '-name'); ?>" />
                            </td>
                        </tr>

                        <!-- Content -->
                        <tr class="hook">
                            <th scope="row">
                                <label for="<?php echo esc_attr($args['content'] . '-content'); ?>">
                                    <?php echo esc_html__('Hook', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="content" class="oneline" id="<?php echo esc_attr($args['content'] . '-content'); ?>" />
                                <p><?php echo esc_html__('An option for developers to add dynamic elements they want. It provides the chance to add whatever input type you want to add in this form. You\'ll be given 1 parameter to play with: $form.', 'essential-addons-for-contact-form-7') ?>

                                </p>
                                <pre class="hook-code">
add_action('custom_hook','custom_hook_callback' );

function custom_hook_callback( $form) {
    // do what ever you want
}
</pre>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="<?php echo esc_attr($type); ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr__('Insert Tag', 'essential-addons-for-contact-form-7'); ?>" />
            </div>
        </div>
<?php
    }

    /**
     * Add shortcode handler to CF7.
     */
    public function add_data_handler() {
        wpcf7_add_form_tag(['hook'], [
            $this,
            'render_hook'
        ], ['name-attr' => true]);
    }

    /**
     * Render Hook
     * @since 1.0.0
     * @author monzuralam
     */
    public function render_hook($tag) {

        // check and make sure tag name is not empty
        if (empty($tag->name)) {
            return '';
        }

        $hook = $tag->content;

        ob_start();

        do_action('before_' . $hook);
        do_action($hook);
        do_action('after_' . $hook);

        return ob_get_clean();
    }

    /**
     * Assets
     * @since 1.0.0
     * @author monzuralam
     */
    public function enqueue_scripts() {
        do_action('eacf7_hook_enqueue_scripts');
    }


    /**
     * @return Hook|null
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

Hook::instance();
