<?php

namespace EACF7;

if (! defined('ABSPATH')) {
    exit;
}

class Google_Maps {
    /**
     * @var null
     */
    protected static $instance = null;

    public function __construct() {
        add_action('wpcf7_admin_init', [$this, 'add_tag_generator'], 99);
        add_action('wpcf7_init', [$this, 'add_data_handler']);

        add_action('wpcf7_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('script_loader_tag', array($this, 'script_loader_tag'), 10, 2);
    }

    /**
     * Add shortcode handler to CF7.
     */
    public function add_data_handler() {
        if (function_exists('wpcf7_add_form_tag')) {
            wpcf7_add_form_tag(['google_maps', 'google_maps*'], [
                $this,
                'render_google_maps'
            ], ['name-attr' => true]);
        }
    }

    public function render_google_maps($tag) {
        $tag = new \WPCF7_FormTag($tag);

        // check and make sure tag name is not empty
        if (empty($tag->name)) {
            return '';
        }

        // Validate our fields
        $validation_error = wpcf7_get_validation_error($tag->name);

        $class = wpcf7_form_controls_class($tag->type, ' eacf7-hidden');

        if ($validation_error) {
            $class .= ' wpcf7-not-valid';
        }

        $lat = ! empty($tag->get_option('lat', '', true)) ? $tag->get_option('lat', '', true) : 0;
        $long = ! empty($tag->get_option('long', '', true)) ? $tag->get_option('long', '', true) : 0;
        $zoom = $tag->get_option('zoom', 'int', true);

        $atts = [
            'name'               => $tag->name,
            'class'              => $class,
            'aria-invalid'       => $validation_error ? 'true' : 'false',
            'aria-required'      => $tag->is_required() ? 'true' : 'false',
            'data-lat'           => $lat,
            'data-long'          => $long,
            'data-zoom'          => $zoom,
        ];

        $atts = wpcf7_format_atts($atts);

        ob_start();
?>
        <span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr($tag->name); ?>">

            <div id="<?php echo esc_attr($tag->name); ?>" class="eacf7-google-maps <?php echo esc_attr($tag->name); ?>"></div>
            <input type="hidden" <?php echo $atts; ?> />

            <?php echo $validation_error; ?>
        </span>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                function initGoogleMap() {
                    const initialLat = <?php echo esc_html($lat); ?>;
                    const initialLng = <?php echo esc_html($long); ?>;

                    const element = document.getElementById('google_maps-739');
                    const inputElement = element.nextElementSibling;
                    console.log(inputElement.getAttribute('data-lat'));

                    // Check if Google Maps is available
                    if (typeof google === 'undefined') {
                        document.getElementById('eacf7-google-maps').innerHTML =
                            '<div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f8f9fa; color: #6c757d; text-align: center; padding: 20px;">' +
                            '<div><strong>Google Maps API Key Required</strong><br>Please add your API key to load Google Maps</div>' +
                            '</div>';
                        return;
                    }

                    const googleMap = new google.maps.Map(element, {
                        zoom: 13,
                        center: {
                            lat: initialLat,
                            lng: initialLng
                        },
                        keyboardShortcuts: false,
                    });

                    // Create draggable marker
                    const googleMarker = new google.maps.Marker({
                        position: {
                            lat: initialLat,
                            lng: initialLng
                        },
                        map: googleMap,
                        draggable: true,
                        title: "Drag me to update coordinates!"
                    });

                    // Info window
                    const infoWindow = new google.maps.InfoWindow({
                        content: "Drag me to update coordinates!"
                    });

                    // infoWindow.open(googleMap, googleMarker);

                    // Function to update Google Maps coordinates
                    function updateGoogleCoordinates(lat, lng) {
                        document.querySelector(`input[name="<?php echo esc_attr($tag->name); ?>"]`).value = `${lat.toFixed(6)},${lng.toFixed(6)}`;
                    }

                    // Add dragend event listener
                    googleMarker.addListener("dragend", function(event) {
                        const lat = event.latLng.lat();
                        const lng = event.latLng.lng();
                        updateGoogleCoordinates(lat, lng);
                    });

                    // Also update on map click
                    googleMap.addListener("click", function(event) {
                        googleMarker.setPosition(event.latLng);
                        const lat = event.latLng.lat();
                        const lng = event.latLng.lng();
                        updateGoogleCoordinates(lat, lng);
                    });

                    // Initialize coordinates display
                    updateGoogleCoordinates(initialLat, initialLng);
                }

                // Initialize Google Maps when API loads
                window.initGoogleMap = initGoogleMap;

                // Initialize Google Maps when the page loads
                window.addEventListener('load', initGoogleMap);
            });
        </script>
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDucSpoWkWGH6n05GpjFLorktAzT1CuEc&callback=initGoogleMap">
        </script>
    <?php
        return ob_get_clean();
    }

    public function enqueue_scripts() {
        wp_enqueue_style('eacf7-frontend');

        // wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=&callback=initGoogleMap', array(), null, true);
        wp_enqueue_script('eacf7-frontend');
    }

    public function script_loader_tag($tag, $handle) {
        if ($handle === 'google-maps') {
            return str_replace(' src', ' async defer src', $tag);
        }
        return $tag;
    }

    public function add_tag_generator() {
        $tag_generator = \WPCF7_TagGenerator::get_instance();

        if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
            $tag_generator->add(
                'google_maps',
                __('Google Maps', 'essential-addons-for-contact-form-7'),
                [$this, 'tag_generator_body_v6'],
                [
                    'version'     => 2,
                ]
            );
        } else {
            $tag_generator->add(
                'google_maps',
                __('Google Maps', 'essential-addons-for-contact-form-7'),
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
                        __('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Google Maps</a>', 'essential-addons-for-contact-form-7'),
                        'https://softlabbd.com/docs/google-maps/'
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
                    'google_maps' => esc_html__('Google Maps', 'essential-addons-for-contact-form-7'),
                ),
            ));

            $tgg->print('field_name');

            ?>

            <fieldset>
                <legend><?php echo esc_html__('Default Latitude', 'essential-addons-for-contact-form-7'); ?></legend>
                <p class="oneline">
                    <label>
                        <input type="text" data-tag-part="option" data-tag-option="lat:" name="lat" placeholder="<?php echo esc_attr__('Enter Default Latitude', 'essential-addons-for-contact-form-7') ?>" />
                        <p><?php echo esc_html__('Enter the default latitude for the map. ex: 23.8103', 'essential-addons-for-contact-form-7'); ?></p>
                    </label>
                </p>
            </fieldset>

            <fieldset>
                <legend><?php echo esc_html__('Default Longitude', 'essential-addons-for-contact-form-7'); ?></legend>
                <p class="oneline">
                    <label>
                        <input type="text" data-tag-part="option" data-tag-option="long:" name="long" placeholder="<?php echo esc_attr__('Enter Default Longitude', 'essential-addons-for-contact-form-7') ?>" />
                        <p><?php echo esc_html__('Enter the default longitude for the map. ex: 90.4125', 'essential-addons-for-contact-form-7'); ?></p>
                    </label>
                </p>
            </fieldset>

            <fieldset>
                <legend><?php echo esc_html__('Zoom Level', 'essential-addons-for-contact-form-7'); ?></legend>
                <p class="oneline">
                    <label>
                        <input type="number" data-tag-part="option" data-tag-option="zoom:" name="zoom" value="10" />
                        <p><?php echo esc_html__('Enter the zoom level for the map. default: 10', 'essential-addons-for-contact-form-7'); ?></p>
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
        $type = 'google_maps';

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
     * @return Google_Maps|null
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

Google_Maps::instance();
