<?php

namespace EACF7;

if (! defined('ABSPATH')) {
    exit;
}

class Image_Choice {
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
        add_action('wpcf7_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('wpcf7_validate_image_choice', array($this, 'validate'), 10, 2);
        add_filter('wpcf7_validate_image_choice*', array($this, 'validate'), 10, 2);
        // add_filter('wpcf7_contact_form_properties', array($this, 'properties'), 99);
    }

    /**
     * Tag Generator
     * @since 1.0.0
     * @author monzuralam
     */
    public function add_tag_generator() {
        $tag_generator = \WPCF7_TagGenerator::get_instance();

        if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
            $tag_generator->add(
                'image_choice',
                __('Image Choice', 'essential-addons-for-contact-form-7'),
                [$this, 'tag_generator_body_v6'],
                [
                    'version'     => 2,
                ]
            );
        } else {
            $tag_generator->add(
                'image_choice',
                __('Image Choice', 'essential-addons-for-contact-form-7'),
                [$this, 'tag_generator_body']
            );
        }
    }

    /**
     * Tag Generator Box
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
                        __('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Image Choice</a>', 'essential-addons-for-contact-form-7'),
                        'https://softlabbd.com/docs/image-choice-field'
                    );
                    ?>
                </p>
            </div>
        </header>
        <div class="control-box">
            <?php
            $tgg->print('field_type', array(
                'with_required' => true,
                'select_options' => array(
                    'image_choice' => esc_html__('Image Choice', 'essential-addons-for-contact-form-7'),
                ),
            ));

            $tgg->print('field_name');
            ?>

            <fieldset>
                <legend><?php echo esc_html__('Images', 'essential-addons-for-contact-form-7'); ?></legend>
                <p class="oneline">
                    <label>
                        <textarea data-tag-part="value" name="values"></textarea>
                        <p><?php echo esc_html__('One item per line. ex: value - image url', 'essential-addons-for-contact-form-7'); ?></p>
                    </label>
                </p>
            </fieldset>

            <fieldset>
                <legend><?php echo esc_html__('Multiple Selection', 'essential-addons-for-contact-form-7'); ?></legend>
                <p>
                    <label for="multiple_yes">
                        <input type="radio" data-tag-part="option" data-tag-option="multiple:" name="multiple" class="option" id="multiple_yes" value="1"><?php echo esc_html__('Yes', 'essential-addons-for-contact-form-7'); ?>
                    </label>
                    <label for="multiple_no">
                        <input type="radio" data-tag-part="option" data-tag-option="multiple:" name="multiple" class="option" id="multiple_no" value="0" checked><?php echo esc_html__('No', 'essential-addons-for-contact-form-7'); ?>
                    </label>
                </p>
            </fieldset>

            <fieldset>
                <legend><?php echo esc_html__('Layout', 'essential-addons-for-contact-form-7'); ?></legend>
                <p>
                    <label for="layout-inline">
                        <input type="radio" data-tag-part="option" data-tag-option="layout:" name="layout" class="option" id="layout-inline" value="inline" checked><?php echo esc_html__('Inline', 'essential-addons-for-contact-form-7'); ?>
                    </label>
                    <label for="layout-grid">
                        <input type="radio" data-tag-part="option" data-tag-option="layout:" name="layout" class="option" id="layout-grid" value="grid"><?php echo esc_html__('Grid', 'essential-addons-for-contact-form-7'); ?>
                    </label>
                </p>
            </fieldset>

            <fieldset>
                <legend><?php echo esc_html__('Style', 'essential-addons-for-contact-form-7'); ?></legend>
                <p>
                    <label for="style-1">
                        <input type="radio" data-tag-part="option" data-tag-option="style:" name="style" class="option" id="style-1" value="1" checked><?php echo esc_html__('1', 'essential-addons-for-contact-form-7'); ?>
                    </label>
                    <label for="style-2">
                        <input type="radio" data-tag-part="option" data-tag-option="style:" name="style" class="option" id="style-2" value="2"><?php echo esc_html__('2', 'essential-addons-for-contact-form-7'); ?>
                    </label>
                    <label for="style-3">
                        <input type="radio" data-tag-part="option" data-tag-option="style:" name="style" class="option" id="style-3" value="3"><?php echo esc_html__('3', 'essential-addons-for-contact-form-7'); ?>
                    </label>
                    <label for="style-4">
                        <input type="radio" data-tag-part="option" data-tag-option="style:" name="style" class="option" id="style-4" value="4"><?php echo esc_html__('4', 'essential-addons-for-contact-form-7'); ?>
                    </label>
                </p>
            </fieldset>

            <fieldset>
                <legend><?php echo esc_html__('Display Value', 'essential-addons-for-contact-form-7'); ?></legend>
                <p>
                    <label for="display_value_yes">
                        <input type="radio" data-tag-part="option" data-tag-option="display_value:" name="display_value" class="option" id="display_value_yes" value="1"><?php echo esc_html__('Yes', 'essential-addons-for-contact-form-7'); ?>
                    </label>
                    <label for="display_value_no">
                        <input type="radio" data-tag-part="option" data-tag-option="display_value:" name="style" class="option" id="display_value_no" value="0"><?php echo esc_html__('No', 'essential-addons-for-contact-form-7'); ?>
                    </label>
                </p>
            </fieldset>

            <?php $tgg->print('class_attr'); ?>
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
                            __('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Image Choice</a>', 'essential-addons-for-contact-form-7'),
                            'https://softlabbd.com/docs-category/image-choice-field/'
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
                                <input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr($args['content'] . '-name'); ?>" />
                            </td>
                        </tr>

                        <!-- Content -->
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($args['content'] . '-content'); ?>">
                                    <?php echo esc_html__('Images', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                            </th>
                            <td>
                                <textarea type="text" name="content" class="oneline" id="<?php echo esc_attr($args['content'] . '-content'); ?>"></textarea>
                                <p><?php echo esc_html__('One item per line. ex: label - image url', 'essential-addons-for-contact-form-7'); ?></p>
                            </td>
                        </tr>

                        <!-- Multiple Selection -->
                        <tr>
                            <th scope="row">
                                <label for=""><?php echo esc_html__('Multiple Selection', 'essential-addons-for-contact-form-7'); ?></label>
                            </th>
                            <td>
                                <label for="multiple_yes">
                                    <input type="radio" name="multiple" class="option" id="multiple_yes" value="1">
                                    <?php echo esc_html__('Yes', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                                <label for="multiple_no">
                                    <input type="radio" name="multiple" class="option" id="multiple_no" value="0" checked>
                                    <?php echo esc_html__('No', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                            </td>
                        </tr>

                        <!-- Layout -->
                        <tr>
                            <th scope="row">
                                <label for=""><?php echo esc_html__('Layout', 'essential-addons-for-contact-form-7'); ?></label>
                            </th>
                            <td>
                                <label for="layout-inline">
                                    <input type="radio" name="layout" class="option" id="layout-inline" value="inline" checked>
                                    <?php echo esc_html__('Inline', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                                <label for="layout-grid">
                                    <input type="radio" name="layout" class="option" id="layout-grid" value="grid">
                                    <?php echo esc_html__('Grid', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                            </td>
                        </tr>

                        <!-- Style -->
                        <tr>
                            <th scope="row">
                                <label for=""><?php echo esc_html__('Style', 'essential-addons-for-contact-form-7'); ?></label>
                            </th>
                            <td>
                                <label for="style-1">
                                    <input type="radio" name="style" class="option" id="style-1" value="1" checked>
                                    <?php echo esc_html__('1', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                                <label for="style-2">
                                    <input type="radio" name="style" class="option" id="style-2" value="2">
                                    <?php echo esc_html__('2', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                                <label for="style-3">
                                    <input type="radio" name="style" class="option" id="style-3" value="3">
                                    <?php echo esc_html__('3', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                                <label for="style-4">
                                    <input type="radio" name="style" class="option" id="style-4" value="4">
                                    <?php echo esc_html__('4', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                            </td>
                        </tr>

                        <!-- Display Value -->
                        <tr>
                            <th scope="row">
                                <label for=""><?php echo esc_html__('Display Value', 'essential-addons-for-contact-form-7'); ?></label>
                            </th>
                            <td>
                                <label for="display_value_yes">
                                    <input type="radio" name="display_value" class="option" id="display_value_yes" value="1">
                                    <?php echo esc_html__('Yes', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                                <label for="display_value_no">
                                    <input type="radio" name="display_value" class="option" id="display_value_no" value="0" checked>
                                    <?php echo esc_html__('No', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                            </td>
                        </tr>

                        <!-- Class -->
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($args['content'] . '-class'); ?>">
                                    <?php echo esc_html__('Class', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr($args['content'] . '-class'); ?>" />
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
        if (function_exists('wpcf7_add_form_tag')) {
            wpcf7_add_form_tag(['image_choice', 'image_choice*'], [
                $this,
                'render_image_choice'
            ], ['name-attr' => true]);
        }
    }

    public function render_image_choice($tag) {

        $tag = new \WPCF7_FormTag($tag);

        // check and make sure tag name is not empty
        if (empty($tag->name)) {
            return '';
        }

        // Validate our fields
        $validation_error = wpcf7_get_validation_error($tag->name);
        $class = $tag->get_class_option();
        $layout         = ! empty($tag->get_option('layout', '', true)) ? $tag->get_option('layout', '', true) : 'inline';
        $multiple       = ! empty($tag->get_option('multiple', '', true)) ? $tag->get_option('multiple', '', true) : 0;
        $style          = ! empty($tag->get_option('style', '', true)) ? $tag->get_option('style', '', true) : 1;
        $display_value  = ! empty($tag->get_option('display_value', '', true)) ? $tag->get_option('display_value', '', true) : 0;
        $items = is_array($tag->values) && ! empty($tag->values) ? $tag->values : '';

        $atts = [
            'class'         => $validation_error ? 'wpcf7-not-valid' : '' . ' image-choice',
            'name'          => $tag->name,
            'aria-invalid'  => $validation_error ? 'true' : 'false',
            'aria-required' => $tag->is_required() ? 'true' : 'false',
            'value'         => '',
        ];

        $atts = wpcf7_format_atts($atts);

        ob_start();
    ?>

        <span class="wpcf7-form-control-wrap eacf7-image-choice" data-name="<?php echo esc_attr($tag->name); ?>">
            <div class="<?php echo esc_attr($class); ?> image-choice-wrap <?php echo esc_attr($layout); ?> style-<?php echo esc_attr($style); ?>" data-multiple="<?php echo esc_attr($multiple); ?>">
                <?php
                foreach ($items as $item) {
                    $data = explode(' - ', $item);
                    $label = $data[0] ?? $item;
                    $image = $data[1] ?? $item;
                ?>
                    <div class="image-choice-item" data-value="<?php echo esc_attr($label); ?>">
                        <?php if ($style == 4) { ?>
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_3_600)">
                                    <path d="M12.8679 2.88208C13.2097 3.22388 13.2097 3.77895 12.8679 4.12075L5.86794 11.1208C5.52615 11.4625 4.97107 11.4625 4.62927 11.1208L1.12927 7.62075C0.787476 7.27896 0.787476 6.72388 1.12927 6.38208C1.47107 6.04028 2.02615 6.04028 2.36794 6.38208L5.24998 9.26138L11.632 2.88208C11.9738 2.54028 12.5289 2.54028 12.8707 2.88208H12.8679Z" fill="white" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_3_600">
                                        <path d="M0.875 0H13.125V14H0.875V0Z" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        <?php } else { ?>
                            <svg class="check" xmlns="http://www.w3.org/2000/svg" fill="none" height="20px" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        <?php } ?>
                        <img src="<?php echo esc_url($image); ?>" class="img-fluid" alt="<?php echo esc_attr($label); ?>" />
                        <?php if ($display_value && ! empty($label)) { ?>
                            <p><?php echo esc_html($label); ?></p>
                        <?php } ?>
                    </div>
                <?php
                }
                ?>
            </div>
            <input type="hidden" <?php echo $atts; ?> />

            <?php echo $validation_error; ?>
        </span>
<?php
        return ob_get_clean();
    }

    /**
     * Assets
     * @since 1.0.0
     * @author monzuralam
     */
    public function enqueue_scripts() {
        wp_enqueue_style('eacf7-frontend');

        wp_enqueue_script('eacf7-frontend');
    }

    public function validate($result, $tag) {
        $tag   = new \WPCF7_FormTag($tag);
        $name  = $tag->name;
        $value = isset($_POST[$name]) ? $_POST[$name] : '';

        if ($tag->is_required() && empty($value)) {
            $result->invalidate($tag, wpcf7_get_message('invalid_required'));
        }

        return $result;
    }

    /**
     * @return Custom_HTML|null
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

Image_Choice::instance();
