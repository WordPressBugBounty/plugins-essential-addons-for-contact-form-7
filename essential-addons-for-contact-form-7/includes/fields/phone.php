<?php

namespace EACF7;

if (! defined('ABSPATH')) {
	exit;
}

class Phone {
	/**
	 * @var null
	 */
	protected static $instance = null;

	public function __construct() {
		add_action('wpcf7_admin_init', [$this, 'add_tag_generator'], 99);
		add_action('wpcf7_init', [$this, 'add_data_handler']);

		add_action('wpcf7_enqueue_scripts', array($this, 'enqueue_scripts'));

		add_filter('wpcf7_validate_phone', [$this, 'validate'], 10, 2);
		add_filter('wpcf7_validate_phone*', [$this, 'validate'], 10, 2);
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
	 * Add shortcode handler to CF7.
	 */
	public function add_data_handler() {
		if (function_exists('wpcf7_add_form_tag')) {
			wpcf7_add_form_tag(['phone', 'phone*'], [
				$this,
				'render_phone'
			], ['name-attr' => true]);
		}
	}

	public function render_phone($tag) {

		$tag = new \WPCF7_FormTag($tag);

		// check and make sure tag name is not empty
		if (empty($tag->name)) {
			return '';
		}

		// Validate our fields
		$validation_error = wpcf7_get_validation_error($tag->name);

		$class = $tag->get_class_option('wpcf7-form-control eacf7-phone');

		if ($validation_error) {
			$class .= ' wpcf7-not-valid';
		}

		$atts = [
			'class'         => $class,
			'id'            => $tag->get_id_option(),
			'name'          => $tag->name,
			'aria-invalid'  => $validation_error ? 'true' : 'false',
			'aria-required' => $tag->is_required() ? 'true' : 'false',
		];

		$validation = $tag->get_option('validation', '', true);

		if( $validation ){
			$atts['data-validation'] = intval($validation);
		}

		$atts = wpcf7_format_atts($atts);

		ob_start();
?>

		<span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr($tag->name); ?>">

			<input type="tel" <?php echo $atts; ?> />

			<?php echo $validation_error; ?>
		</span>
	<?php
		return ob_get_clean();
	}

	public function enqueue_scripts() {
		wp_enqueue_style('eacf7-int-tel-input', EACF7_ASSETS . '/vendor/int-tel-input/css/intlTelInput.css', array('eacf7-frontend'), '23.7.4');

		wp_enqueue_script('eacf7-int-tel-input', EACF7_ASSETS . '/vendor/int-tel-input/js/intlTelInput.min.js', array('eacf7-frontend'), '23.7.4', true);
	}

	public function add_tag_generator() {
		$tag_generator = \WPCF7_TagGenerator::get_instance();

		if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
			$tag_generator->add(
				'phone',
				__('Phone/ Mobile', 'essential-addons-for-contact-form-7'),
				[$this, 'tag_generator_body_v6'],
				[
					'version' 	=> 2,
				]
			);
		} else {
			$tag_generator->add(
				'phone',
				__('Phone/ Mobile', 'essential-addons-for-contact-form-7'),
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
						__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Phone/ Mobile</a>', 'essential-addons-for-contact-form-7'),
						'https://softlabbd.com/docs/phone-field/'
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
					'phone' => esc_html__('Phone/ Mobile', 'essential-addons-for-contact-form-7'),
				),
			));

			$tgg->print('field_name');

			?>
			<!-- Validation -->
			<fieldset>
				<legend><?php echo esc_html__('Validation', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline phone-validation">
					<label>
						<input type="checkbox" data-tag-part="option" data-tag-option="validation:" name="validation" value="1" />
						<?php echo esc_html__('Check valid/invalid phone/mobile number.', 'essential-addons-for-contact-form-7'); ?>
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
		$type = 'phone';

	?>
		<div class="control-box">
			<fieldset>
				<div class="eacf7-notice eacf7-notice-info">
					<p>
						<span class="dashicons dashicons-info-outline"></span>
						<?php
						echo sprintf(
							__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Phone/ Mobile</a>', 'essential-addons-for-contact-form-7'),
							'https://softlabbd.com/docs/phone-field/'
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

						<!-- Validation -->
						<tr>
							<th scope="row"><?php echo esc_html__('Validation', 'essential-addons-for-contact-form-7'); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><?php echo esc_html__('Validation', 'essential-addons-for-contact-form-7'); ?></legend>

									<p class="oneline phone-validation">
										<label>
											<input type="checkbox" <?php echo eacf7_fs()->can_use_premium_code__premium_only() ? 'name="validation" class="option" value="1"' : ''; ?> />
											<?php echo esc_html__('Check valid/invalid phone/mobile number.', 'essential-addons-for-contact-form-7'); ?>
										</label>
									</p>
								</fieldset>
							</td>
						</tr>

						<!-- ID -->
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr($args['content'] . '-id'); ?>">
									<?php echo esc_html__('ID', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</th>
							<td>
								<input type="text" name="id" class="idvalue oneline option"
									id="<?php echo esc_attr($args['content'] . '-id'); ?>" />
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
								<input type="text" name="class" class="classvalue oneline option"
									id="<?php echo esc_attr($args['content'] . '-class'); ?>" />
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
					<?php printf('To display the phone number in the email, insert the mail-tag (%s) in the Mail tab.', '<strong><span class="mail-tag"></span></strong>'); ?>
					<input type="text" class="mail-tag code eacf7-hidden" readonly="readonly"
						id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" />
				</label>
			</p>
		</div>
<?php
	}

	/**
	 * @return Phone|null
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Phone::instance();
