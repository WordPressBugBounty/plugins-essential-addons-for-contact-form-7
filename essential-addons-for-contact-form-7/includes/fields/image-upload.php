<?php

namespace EACF7;

if (! defined('ABSPATH')) {
    exit;
}

class Image_Upload {
    /**
     * @var null
     */
    protected static $instance = null;

    public function __construct() {
        if (! class_exists('EACF7/Uploader')) {
            include_once EACF7_INCLUDES . '/Uploader.php';
        }

        add_action('wpcf7_admin_init', [$this, 'add_tag_generator'], 99);
        add_action('wpcf7_init', [$this, 'add_data_handler']);

        add_action('wpcf7_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Add shortcode handler to CF7.
     */
    public function add_data_handler() {
        if (function_exists('wpcf7_add_form_tag')) {
            wpcf7_add_form_tag(['image_upload', 'image_upload*'], [
                $this,
                'render_uploader'
            ], ['name-attr' => true]);
        }
    }

    public function render_uploader($tag) {
        $tag = new \WPCF7_FormTag($tag);

        // check and make sure tag name is not empty
        if (empty($tag->name)) {
            return '';
        }

        // Get current form Object
        $form = \WPCF7_ContactForm::get_current();


        // Validate our fields
        $validation_error = wpcf7_get_validation_error($tag->name);

        $class = wpcf7_form_controls_class($tag->type, 'upload-file-list eacf7-hidden');

        if ($validation_error) {
            $class .= ' wpcf7-not-valid';
        }

        $max_files = $tag->get_option('max_files', 'int', true);
        $max_files = ! empty($max_files) ? $max_files : 1;

        $max_size = $tag->get_option('max_size', 'int', true);

        $atts = [
            'name'               => $tag->name,
            'class'              => $class,
            'tabindex'           => $tag->get_option('tabindex', 'signed_int', true),
            'aria-invalid'       => $validation_error ? 'true' : 'false',
            'aria-required'      => $tag->is_required() ? 'true' : 'false',
            'data-max_files'     => $max_files,
            'data-max_size'      => $max_size ? size_format($max_size * 1024 * 1024) : '',
            'data-max_post_size' => eacf7_get_max_upload_size(),
            'data-extensions'    => $tag->get_option('extensions', '.*', true),
            'data-media_library' => in_array('media_library', $tag->options) ? 1 : 0,
            'data-form_id'       => $form->id(),
            'data-field_id'      => $tag->name,
        ];

        $atts = wpcf7_format_atts($atts);

        ob_start();
?>
        <span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr($tag->name); ?>">

            <span class="eacf7-uploader image-upload">
                <span class="eacf7-uploader-body">
                    <span class="uploader-text"><?php echo esc_html__("Drag and drop image here or", 'essential-addons-for-contact-form-7') ?></span>

                    <span class="uploader-buttons">
                        <button type="button" class="eacf7-uploader-browse">
                            <span><?php echo esc_html__('Browse Image', 'essential-addons-for-contact-form-7') ?></span>
                        </button>
                    </span>

                </span>

                <span class="file-list"></span>

                <span class="uploader-hint">
                    <span class="max-files-label <?php echo $max_files < 2 ? 'eacf7-hidden' : ''; ?>"><?php printf(__("Upload upto %s Images.", 'essential-addons-for-contact-form-7'), '<span class="number">' . esc_html($max_files) . '</span>'); ?></span>

                    <span class="max-size-label <?php echo empty($max_size) ? 'eacf7-hidden' : ''; ?>"><?php echo esc_html__("Max File Size: ", 'essential-addons-for-contact-form-7') . '<span class="size">' . esc_html($max_size) . ' MB </span>'; ?></span>

                </span>
            </span>

            <input style="position:absolute!important;clip:rect(0,0,0,0)!important;height:1px!important;width:1px!important;border:0!important;overflow:hidden!important;padding:0!important;margin:0!important;" <?php echo $atts; ?> />

            <?php echo $validation_error; ?>
        </span>
    <?php
        return ob_get_clean();
    }

    public function enqueue_scripts() {
        wp_enqueue_style('dashicons');
        wp_enqueue_style('eacf7-frontend');

        wp_enqueue_script('wp-plupload');
        wp_enqueue_script('eacf7-frontend');
    }

    public function add_tag_generator() {
        $tag_generator = \WPCF7_TagGenerator::get_instance();

        if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
            $tag_generator->add(
                'image_upload',
                __('Image Upload', 'essential-addons-for-contact-form-7'),
                [$this, 'tag_generator_body_v6'],
                [
                    'version'     => 2,
                ]
            );
        } else {
            $tag_generator->add(
                'image_upload',
                __('Image Upload', 'essential-addons-for-contact-form-7'),
                [$this, 'tag_generator_body']
            );
        }
    }

    /**
     * Tag Generator v6
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
                        __('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Image Upload</a>', 'essential-addons-for-contact-form-7'),
                        'https://softlabbd.com/docs/image-upload/'
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
                    'image_upload' => esc_html__('Image Upload', 'essential-addons-for-contact-form-7'),
                ),
            ));

            $tgg->print('field_name');

            ?>

            <fieldset>
                <legend><?php echo esc_html__('Allowed Image Extensions', 'essential-addons-for-contact-form-7'); ?></legend>
                <p class="oneline">
                    <label>
                        <input type="text" data-tag-part="option" data-tag-option="extensions:" name="extensions" placeholder="<?php echo esc_attr__('jpg|png|gif') ?>" />
                        <p><?php echo esc_html__('Enter pipe (|) separated list of allowed image extensions. Leave blank to allow all extensions.', 'essential-addons-for-contact-form-7'); ?></p>
                    </label>
                </p>
            </fieldset>

            <fieldset>
                <legend><?php echo esc_html__('Max Image Size (MB)', 'essential-addons-for-contact-form-7'); ?></legend>
                <p class="oneline">
                    <label>
                        <input type="text" data-tag-part="option" data-tag-option="max_size:" name="max_size" />
                        <p><?php echo esc_html__('Enter the max size of each file, in megabytes. If left blank, the value defaults to the maximum size the server allows which is 256 MB.', 'essential-addons-for-contact-form-7'); ?></p>
                    </label>
                </p>
            </fieldset>

            <fieldset>
                <legend><?php echo esc_html__('Max Images Uploads', 'essential-addons-for-contact-form-7'); ?></legend>
                <p class="oneline">
                    <label>
                        <input type="number" data-tag-part="option" data-tag-option="max_files:" name="max_files" value="1" />
                        <p><?php echo esc_html__('Enter the max number of images to allow. If left blank, the value defaults to 1.', 'essential-addons-for-contact-form-7'); ?></p>
                    </label>
                </p>
            </fieldset>

            <!-- Save to Media Library -->
            <fieldset>
                <legend><?php echo esc_html__('Save to Media', 'essential-addons-for-contact-form-7'); ?></legend>
                <p class="oneline">
                    <label>
                        <input type="checkbox" data-tag-part="option" data-tag-option="media_library" name="media_library" />
                        <p><?php echo esc_html__('Check to store uploaded images to Media Library', 'essential-addons-for-contact-form-7'); ?></p>
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
     * Tag Generator
     * @since 1.0.0
     * @author monzuralam
     */
    public function tag_generator_body($contact_form, $args = '') {
        $args = wp_parse_args($args, []);
        $type = 'image_upload';

    ?>
        <div class="control-box">
            <fieldset>
                <div class="eacf7-notice eacf7-notice-info">
                    <p>
                        <span class="dashicons dashicons-info-outline"></span>

                        <?php
                        echo sprintf(
                            __('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Image Upload</a>', 'essential-addons-for-contact-form-7'),
                            'https://softlabbd.com/docs/image-upload/'
                        );
                        ?>

                    </p>
                </div>

                <table class="form-table">
                    <tbody>

                        <!-- Field Type -->
                        <tr>
                            <th scope="row"><?php echo esc_html__('Field type', 'essential-addons-for-contact-form-7'); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php echo esc_html__('Field type', 'essential-addons-for-contact-form-7'); ?></legend>
                                    <label>
                                        <input type="checkbox"
                                            name="required" /> <?php echo esc_html__('Required field', 'essential-addons-for-contact-form-7'); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>

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

                        <!-- Allowed Files Extensions -->
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($args['content'] . '-extensions'); ?>">
                                    <?php echo esc_html__('Allowed Image Extensions', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="extensions" class="oneline option"
                                    id="<?php echo esc_attr($args['content'] . '-extensions'); ?>"
                                    placeholder="jpg|png|gif" />

                                <p class="description"><?php echo esc_html__('Enter pipe (|) separated list of allowed image extensions. Leave blank to allow all extensions.', 'essential-addons-for-contact-form-7'); ?></p>
                            </td>
                        </tr>

                        <!-- Max File Size -->
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($args['content'] . '-max_size'); ?>">
                                    <?php echo esc_html__('Max Image Size (MB)', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="max_size" class="option oneline"
                                    id="<?php echo esc_attr($args['content'] . '-max_size'); ?>" />
                                <p class="description"><?php echo esc_html__(sprintf(__('Enter the max size of each file, in megabytes. If left blank, the value defaults to the maximum size the server allows which is %s.', 'essential-addons-for-contact-form-7'), eacf7_get_max_upload_size())); ?></p>
                            </td>
                        </tr>

                        <!-- Max Files -->
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($args['content'] . '-max_files'); ?>">
                                    <?php echo esc_html__('Max Images Uploads', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="max_files" class="option oneline"
                                    id="<?php echo esc_attr($args['content'] . '-max_files'); ?>"
                                    value="1" />
                                <p class="description"><?php echo esc_html__('Enter the max number of images to allow. If left blank, the value defaults to 1.', 'essential-addons-for-contact-form-7'); ?></p>
                            </td>
                        </tr>

                        <!-- Save to Media Library -->
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($args['content'] . '-media_library'); ?>">
                                    <?php echo esc_html__('Save to Media', 'essential-addons-for-contact-form-7'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="checkbox" name="media_library" class="option"
                                    id="<?php echo esc_attr($args['content'] . '-media_library'); ?>" />

                                <p class="description">
                                    <?php echo esc_html__('Check to store uploaded images to Media Library', 'essential-addons-for-contact-form-7'); ?>
                                </p>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="<?php echo esc_attr($type); ?>" class="tag code" readonly="readonly"
                onfocus="this.select()" />

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag"
                    value="<?php echo esc_attr__('Insert Tag', 'essential-addons-for-contact-form-7'); ?>" />
            </div>

            <br class="clear" />

            <p class="description mail-tag">
                <label for="<?php echo esc_attr($args['content'] . '-mailtag'); ?>">
                    <?php printf('To list the uploads in your email, insert the mail-tag (%s) in the Mail tab.', '<strong><span class="mail-tag"></span></strong>'); ?>
                    <input type="text" class="mail-tag code eacf7-hidden" readonly="readonly"
                        id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" />
                </label>
            </p>
        </div>
<?php
    }

    /**
     * @return Image_Upload|null
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

Image_Upload::instance();
