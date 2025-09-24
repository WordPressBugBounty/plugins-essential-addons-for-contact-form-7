<?php

namespace EACF7;

if (! defined('ABSPATH')) exit;

class Dynamic_Text {
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
		add_filter('wpcf7_validate_dynamic_text', array($this, 'validate'), 10, 2);
		add_filter('wpcf7_validate_dynamic_text*', array($this, 'validate'), 10, 2);
	}

	/**
	 * Add Contact Form Tag
	 */
	public function add_data_handler() {
		wpcf7_add_form_tag(
			array('dynamic_text', 'dynamic_text*'),
			array($this, 'render_dynamic_text'),
			array('name-attr' => true)
		);
	}

	/**
	 * Handler callback
	 *
	 * @return void|mixed
	 */
	public function render_dynamic_text($tag) {

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
			'class'        => $tag->get_class_option($class),
			'id'           => $tag->get_id_option(),
			'name'         => $tag->name,
			'aria-invalid' => $validation_error ? 'true' : 'false',
		];

		if ($tag->is_required()) {
			$atts['aria-required'] = 'true';
		}

		// Visibility
		$visibility = $tag->get_option('visibility', '', true);
		if ($visibility == 'show') {
			$atts['type'] = 'text';
		} elseif ($visibility == 'disabled') {
			$atts['type']     = 'text';
			$atts['disabled'] = 'disabled';
		} elseif ($visibility == 'hidden') {
			$atts['type'] = 'hidden';
		}

		$values = $tag->values;

		$key = $tag->get_option('key', '', true);

		// dynamic value
		$atts['value'] = $this->get_dynamic_value($values[0], $key);

		$atts = wpcf7_format_atts($atts);

		ob_start();
		$html = sprintf('<input %1$s> %2$s', $atts, $validation_error);
?>

		<span class="dynamic-text wpcf7-form-control-wrap <?php echo sanitize_html_class($tag->name); ?>"
			data-name="<?php echo sanitize_html_class($tag->name); ?>">
			<?php echo $html; ?>
		</span>

	<?php
		return ob_get_clean();
	}

	public function get_dynamic_value($value, $key = false) {
		$dynamic_value = '';

		switch ($value) {
			case 'url_param':
				$dynamic_value = isset($_GET[$key]) ? sanitize_text_field($_GET[$key]) : '';
				break;
			case 'admin_email':
				$dynamic_value = get_option('admin_email');
				break;
			case 'site_url':
				$dynamic_value = site_url();
				break;
			case 'ip_address':
				$dynamic_value = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : 'IP Address Not Found';
				break;
			case 'date':
				$dynamic_value = date('Y-m-d');
				break;
			case 'post_id':
				$dynamic_value = get_the_ID();
				break;
			case 'post_title':
				$dynamic_value = get_the_title();
				break;
			case 'post_url':
				$dynamic_value = get_permalink();
				break;
			case 'post_meta':
				$dynamic_value = get_post_meta(get_the_ID(), $key, true);
				break;
			case 'referrer_url':
				$dynamic_value = isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field($_SERVER['HTTP_REFERER']) : 'Not Found Referrer';
				break;
			case 'user_id':
				$dynamic_value = get_current_user_id();
				break;
			case 'user_email':
				$dynamic_value = get_the_author_meta('user_email');
				break;
			case 'user_display_name':
				$dynamic_value = get_the_author_meta('display_name');
				break;
			case 'user_first_name':
				$dynamic_value = get_the_author_meta('first_name');
				break;
			case 'user_last_name':
				$dynamic_value = get_the_author_meta('last_name');
				break;
			case 'user_login':
				$dynamic_value = get_the_author_meta('user_login');
				break;
			case 'user_meta':
				$dynamic_value = get_the_author_meta($key);
				break;
		}

		return $dynamic_value;
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
				'dynamic_text',
				__('Dynamic Text', 'essential-addons-for-contact-form-7'),
				[$this, 'tag_generator_body_v6'],
				[
					'version' 	=> 2,
				]
			);
		} else {
			$tag_generator->add(
				'dynamic_text',
				__('Dynamic Text', 'essential-addons-for-contact-form-7'),
				[$this, 'tag_generator_body']
			);
		}
	}

	/**
	 * Tag Generator callback method for v6
	 * @return void
	 * @since 1.0.1
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
						__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Dynamic Text</a>', 'essential-addons-for-contact-form-7'),
						'https://softlabbd.com/docs/dynamic-text/'
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
					'dynamic_text' => esc_html__('Dynamic Text', 'essential-addons-for-contact-form-7'),
				),
			));

			$tgg->print('field_name');
			?>

			<fieldset>
				<legend><?php echo esc_html__('Field Visibility', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline">
					<label>
						<input type="radio" data-tag-part="option" data-tag-option="visibility:" name="visibility" value="show" checked />
						<?php echo esc_html__('Show', 'essential-addons-for-contact-form-7'); ?>
					</label>

					<label>
						<input type="radio" data-tag-part="option" data-tag-option="visibility:" name="visibility" value="disabled" />
						<?php echo esc_html__('Disabled', 'essential-addons-for-contact-form-7'); ?>
					</label>

					<label>
						<input type="radio" data-tag-part="option" data-tag-option="visibility:" name="visibility" value="hidden" />
						<?php echo esc_html__('Hidden', 'essential-addons-for-contact-form-7'); ?>
					</label>
				</p>
			</fieldset>

			<fieldset>
				<legend><?php echo esc_html__('Dynamic Value', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline">
					<select data-tag-part="value" name="values" id="eacf7-dynamic-text-value">
						<option value=""><?php echo esc_html__('Select', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="url_param"><?php echo esc_html__('URL GET Parameter', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="admin_email"><?php echo esc_html__('Admin Email', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="site_url"><?php echo esc_html__('Site URL', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="ip_address"><?php echo esc_html__('IP Address', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="date"><?php echo esc_html__('Date', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="post_id"><?php echo esc_html__('Embedded Post/Page ID', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="post_title"><?php echo esc_html__('Embedded Post/Page Title', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="post_url"><?php echo esc_html__('Embedded URL', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="post_meta"><?php echo esc_html__('Post Meta', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="referrer_url"><?php echo esc_html__('HTTP Referrer URL', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="user_id"><?php echo esc_html__('User ID', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="user_email"><?php echo esc_html__('User Email', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="user_display_name"><?php echo esc_html__('User Display Name', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="user_first_name"><?php echo esc_html__('User First Name', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="user_last_name"><?php echo esc_html__('User Last Name', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="user_login"><?php echo esc_html__('User Login', 'essential-addons-for-contact-form-7'); ?></option>
						<option value="user_meta"><?php echo esc_html__('User Meta', 'essential-addons-for-contact-form-7'); ?></option>
					</select>
				</p>
			</fieldset>

			<!-- Dynamic Key -->
			<fieldset class="dynamic-key" style="display: none;">
				<legend><?php echo esc_html__('Dynamic Key', 'essential-addons-for-contact-form-7'); ?></legend>
				<input type="text" data-tag-part="option" data-tag-option="key:" name="key" class="oneline option" id="eacf7-dynamic-text-key" placeholder="<?php echo esc_html__('Dynamic Key', 'essential-addons-for-contact-form-7'); ?>">
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
	 * Tag Generator callback method
	 *
	 * @return void
	 */
	public function tag_generator_body($contact_form, $args = '') {
		$args            = wp_parse_args($args, array());
		$field_type 	 = 'dynamic_text';

	?>
		<div class="control-box dynamic-text-control">
			<fieldset>
				<div class="eacf7-notice eacf7-notice-info">
					<p>
						<span class="dashicons dashicons-info-outline"></span>
						<?php
						echo sprintf(
							__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Dynamic Text</a>', 'essential-addons-for-contact-form-7'),
							'https://softlabbd.com/docs/dynamic-text/'
						);
						?>
					</p>
				</div>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php echo esc_html__('Field Type', 'essential-addons-for-contact-form-7'); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><?php echo esc_html__('Field Type', 'essential-addons-for-contact-form-7'); ?></legend>
									<label>
										<input type="checkbox" name="required" value="on">
										<?php echo esc_html__('Required Field', 'essential-addons-for-contact-form-7'); ?>
									</label>
								</fieldset>
							</td>
						</tr>

						<!-- Name -->
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr($args['content'] . '-name'); ?>"><?php echo esc_html__('Name', 'essential-addons-for-contact-form-7'); ?></label>
							</th>
							<td>
								<input type="text" name="name" class="tg-name oneline"
									id="<?php echo esc_attr($args['content'] . '-name'); ?>">
							</td>
						</tr>

						<!-- Field Visibility -->
						<tr>
							<th scope="row">
								<label for=""><?php echo esc_html__('Field Visibility', 'essential-addons-for-contact-form-7'); ?></label>
							</th>
							<td>
								<label for="show">
									<input type="radio" name="visibility" class="option" id="show" value="show"
										checked><?php echo esc_html__('Show', 'essential-addons-for-contact-form-7'); ?>
								</label>
								<label for="disabled">
									<input type="radio" name="visibility" class="option" id="disabled"
										value="disabled"><?php echo esc_html__('Disabled', 'essential-addons-for-contact-form-7'); ?>
								</label>
								<label for="hidden">
									<input type="radio" name="visibility" class="option" id="hidden"
										value="hidden"><?php echo esc_html__('Hidden', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</td>
						</tr>

						<!-- Dynamic Value -->
						<tr>
							<th scope="row">
								<label for="eacf7-dynamic-text-value"><?php echo esc_html__('Dynamic Value', 'essential-addons-for-contact-form-7'); ?></label>
							</th>
							<td>
								<select name="values" id="eacf7-dynamic-text-value">
									<option value=""><?php echo esc_html__('Select', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="url_param"><?php echo esc_html__('URL GET Parameter', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="admin_email"><?php echo esc_html__('Admin Email', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="site_url"><?php echo esc_html__('Site URL', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="ip_address"><?php echo esc_html__('IP Address', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="date"><?php echo esc_html__('Date', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="post_id"><?php echo esc_html__('Embedded Post/Page ID', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="post_title"><?php echo esc_html__('Embedded Post/Page Title', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="post_url"><?php echo esc_html__('Embedded URL', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="post_meta"><?php echo esc_html__('Post Meta', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="referrer_url"><?php echo esc_html__('HTTP Referrer URL', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="user_id"><?php echo esc_html__('User ID', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="user_email"><?php echo esc_html__('User Email', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="user_display_name"><?php echo esc_html__('User Display Name', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="user_first_name"><?php echo esc_html__('User First Name', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="user_last_name"><?php echo esc_html__('User Last Name', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="user_login"><?php echo esc_html__('User Login', 'essential-addons-for-contact-form-7'); ?></option>
									<option value="user_meta"><?php echo esc_html__('User Meta', 'essential-addons-for-contact-form-7'); ?></option>
								</select>
							</td>
						</tr>

						<!-- Dynamic Key -->
						<tr class="dynamic-key" style="display: none;">
							<th scope="row">
								<label for="eacf7-dynamic-text-key"><?php echo esc_html__('Dynamic Key', 'essential-addons-for-contact-form-7'); ?></label>
							</th>
							<td>
								<input type="text" name="key" class="oneline option" id="eacf7-dynamic-text-key"
									placeholder="<?php echo esc_html__('Dynamic Key', 'essential-addons-for-contact-form-7'); ?>">
							</td>
						</tr>

						<!-- Class Attributes -->
						<tr>
							<th scope="row">
								<label for="class-attributes"><?php echo esc_html__('Class Attributes', 'essential-addons-for-contact-form-7'); ?></label>
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
			<input type="text" name="<?php echo esc_attr($field_type) ?>" class="tag code" readonly onfocus="this.select()">

			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr(__('Insert Tag', 'essential-addons-for-contact-form-7')); ?>">
			</div>

			<br class="clear" />

			<p class="description mail-tag">
				<label for="<?php echo esc_attr($args['content'] . '-mailtag'); ?>">
					<?php printf('To display the dynamic text in the email, insert the mail-tag (%s) in the Mail tab.', '<strong><span class="mail-tag"></span></strong>'); ?>
					<input type="text" class="mail-tag code eacf7-hidden" readonly="readonly" id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" />
				</label>
			</p>
		</div>

<?php
	}

	/**
	 * Validation filter
	 *
	 * @param [type] $result
	 * @param [type] $tag
	 *
	 * @return void
	 */
	public function validate($result, $tag) {

		$name = $tag->name;

		// Check if the field is empty or contains only empty values
		$empty = ! isset($_POST[$name]) || empty($_POST[$name]) && '0' !== $_POST[$name];

		if ($tag->is_required() && $empty) {
			// Field is required but empty, invalidate the result
			$result->invalidate($tag, wpcf7_get_message('invalid_required'));
		}

		return $result;
	}

	/**
	 * @return Dynamic_Text|null
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Dynamic_Text::instance();
