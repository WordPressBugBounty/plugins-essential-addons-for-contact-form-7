<?php

namespace EACF7;

if (! defined('ABSPATH')) {
	exit;
}

class DateTime {
	/**
	 * @var null
	 */
	protected static $instance = null;

	public function __construct() {
		add_action('wpcf7_admin_init', [$this, 'add_tag_generator'], 99);
		add_action('wpcf7_init', [$this, 'add_data_handler']);

		add_action('wpcf7_enqueue_scripts', array($this, 'enqueue_scripts'));

		add_filter('wpcf7_validate_date_time', [$this, 'validate'], 10, 2);
		add_filter('wpcf7_validate_date_time*', [$this, 'validate'], 10, 2);
	}

	public function validate($result, $tag) {
		$tag   = new \WPCF7_FormTag($tag);
		$name  = $tag->name;
		$value = $_POST[$name] ?? '';



		if ($tag->is_required() && empty($value)) {
			$result->invalidate($tag, wpcf7_get_message('invalid_required'));
		}

		$patterns = [
			'/^\d{4}-\d{1,2}-\d{1,2} \d{1,2}:\d{2} [AP]M$/',     // Y-m-d h:i A
			'/^\d{1,2}\/\d{1,2}\/\d{4}$/',               // m/d/Y or d/m/Y
			'/^\d{1,2}\.\d{1,2}\.\d{4}$/',               // d.m.Y
			'/^\d{1,2}\/\d{1,2}\/\d{2}$/',               // n/j/y or m/d/y
			'/^[A-Za-z]{3}\/\d{1,2}\/\d{4}$/',           // M/d/Y
			'/^\d{2}\/\d{1,2}\/\d{1,2}$/',               // y/m/d
			'/^\d{4}-\d{1,2}-\d{1,2}$/',                 // Y-m-d
			'/^\d{1,2}-[A-Za-z]{3}-\d{2}$/',             // d-M-y
			'/^\d{1,2}\/\d{1,2}\/\d{4} \d{1,2}:\d{2} [AP]M$/',     // m/d/Y h:i A
			'/^\d{1,2}\/\d{1,2}\/\d{4} \d{1,2}:\d{2}$/', // m/d/Y H:i
			'/^\d{1,2}\/\d{1,2}\/\d{4} [AP]M$/',         // Only time with AM/PM
			'/^\d{1,2}:\d{2} [AP]M$/',                   // h:i A
			'/^\d{1,2}:\d{2}$/'                          // H:i
		];
	
		if (!empty($value)) {
			$matched = false;
			foreach ($patterns as $pattern) {
				if (preg_match($pattern, $value)) {
					$matched = true;
					break;
				}
			}
	
			if (!$matched) {
				$result->invalidate($tag, esc_html__('Invalid Date Format', 'essential-addons-for-contact-form-7'));
			}
		}

		return $result;
	}

	/**
	 * Add shortcode handler to CF7.
	 */
	public function add_data_handler() {
		if (function_exists('wpcf7_add_form_tag')) {
			wpcf7_add_form_tag(['date_time', 'date_time*'], [
				$this,
				'render_date_time'
			], ['name-attr' => true]);
		}
	}

	public function render_date_time($tag) {

		$tag = new \WPCF7_FormTag($tag);

		// check and make sure tag name is not empty
		if (empty($tag->name)) {
			return '';
		}

		// Validate our fields
		$validation_error = wpcf7_get_validation_error($tag->name);

		$class = $tag->get_class_option('wpcf7-form-control eacf7-datetimepicker');

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

		$values = $tag->values;

		$format = ! empty($values) ? $values[0] : '';

		if ($format) {
			$atts['data-format'] = $format;
		}

		$atts = wpcf7_format_atts($atts);

		ob_start();
?>

		<span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr($tag->name); ?>">

			<input type="text" <?php echo $atts; ?> />

			<?php echo $validation_error; ?>
		</span>
	<?php
		return ob_get_clean();
	}

	public function enqueue_scripts() {
		wp_enqueue_style('eacf7-datetimepicker', EACF7_ASSETS . '/vendor/datetimepicker/jquery.datetimepicker.min.css', array('eacf7-frontend'), '2.5.20');

		wp_enqueue_script('eacf7-datetimepicker', EACF7_ASSETS . '/vendor/datetimepicker/jquery.datetimepicker.full.js', array('eacf7-frontend'), '2.5.20', true);
	}

	public function add_tag_generator() {
		$tag_generator = \WPCF7_TagGenerator::get_instance();

		if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
			$tag_generator->add(
				'date_time',
				__('Date & Time', 'essential-addons-for-contact-form-7'),
				[$this, 'tag_generator_body_v6'],
				[
					'version' 	=> 2,
				]
			);
		} else {
			$tag_generator->add(
				'date_time',
				__('Date & Time', 'essential-addons-for-contact-form-7'),
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
						__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Date & Time</a>', 'essential-addons-for-contact-form-7'),
						'https://softlabbd.com/docs/date-time-field/'
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
					'date_time' => esc_html__('Date & Time', 'essential-addons-for-contact-form-7'),
				),
			));

			$tgg->print('field_name');
			?>
			<!-- Format -->
			<fieldset>
				<legend>
					<?php echo esc_html__('Format', 'essential-addons-for-contact-form-7'); ?>
					<?php echo eacf7_fs()->can_use_premium_code__premium_only() ? '' : esc_html__('(Pro Feature)', 'essential-addons-for-contact-form-7'); ?>
				</legend>
				<p class="oneline date-time-format">
					<?php
					$formats = array(
						'm/d/Y' => esc_html__('m/d/Y - (Ex: 11/12/2024)', 'essential-addons-for-contact-form-7'),
						'd/m/Y' => esc_html__('d/m/Y - (Ex: 28/11/2024)', 'essential-addons-for-contact-form-7'),
						'd.m.Y' => esc_html__('d.m.Y - (Ex: 28.11.2024)', 'essential-addons-for-contact-form-7'),
						'n/j/y' => esc_html__('n/j/y - (Ex: 4/28/24)', 'essential-addons-for-contact-form-7'),
						'm/d/y' => esc_html__('m/d/y - (Ex: 04/28/24)', 'essential-addons-for-contact-form-7'),
						'M/d/Y' => esc_html__('M/d/Y - (Ex: Apr/28/2024)', 'essential-addons-for-contact-form-7'),
						'y/m/d' => esc_html__('y/m/d - (Ex: 24/04/28)', 'essential-addons-for-contact-form-7'),
						'Y-m-d' => esc_html__('Y-m-d - (Ex: 2024-04-28)', 'essential-addons-for-contact-form-7'),
						'd-M-y' => esc_html__('d-M-y - (Ex: 28-Apr-24)', 'essential-addons-for-contact-form-7'),
						'm/d/Y h:i A' => esc_html__('m/d/Y h:i A - (Ex: 04/28/2024 08:55 PM)', 'essential-addons-for-contact-form-7'),
						'm/d/Y H:i' => esc_html__('m/d/Y H:i - (Ex: 04/28/2024 20:55)', 'essential-addons-for-contact-form-7'),
						'd/m/Y h:i A' => esc_html__('d/m/Y h:i A - (Ex: 28/04/2024 08:55 PM)', 'essential-addons-for-contact-form-7'),
						'd/m/Y H:i' => esc_html__('d/m/Y H:i - (Ex: 28/04/2024 20:55)', 'essential-addons-for-contact-form-7'),
						'd.m.Y h:i A' => esc_html__('d.m.Y h:i A - (Ex: 28.04.2024 08:55 PM)', 'essential-addons-for-contact-form-7'),
						'd.m.Y H:i' => esc_html__('d.m.Y H:i - (Ex: 28.04.2024 20:55)', 'essential-addons-for-contact-form-7'),
						'h:i A' => esc_html__('h:i A (Only Time Ex: 08:55 PM)', 'essential-addons-for-contact-form-7'),
						'H:i' => esc_html__('H:i (Only Time Ex: 20:55)', 'essential-addons-for-contact-form-7'),
					);
					?>

					<label>
						<select <?php echo eacf7_fs()->can_use_premium_code__premium_only() ? 'data-tag-part="value"' : ''; ?> name="values">
							<?php
							foreach ($formats as $key => $value) { ?>
								<option value="<?php echo eacf7_fs()->can_use_premium_code__premium_only() ? esc_attr($key) : ''; ?>"><?php echo $value; ?></option>
							<?php } ?>
						</select>
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
		$type = 'date_time';

		$description = esc_html__('Generate a form-tag for a field for entering date/time picker.', 'essential-addons-for-contact-form-7');
	?>
		<div class="control-box">
			<fieldset>
				<div class="eacf7-notice eacf7-notice-info">
					<p>
						<span class="dashicons dashicons-info-outline"></span>
						<?php
						echo sprintf(
							__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Date & Time</a>', 'essential-addons-for-contact-form-7'),
							'https://softlabbd.com/docs/date-time-field/'
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

						<!-- Format -->
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr($args['content'] . '-format'); ?>">
									<?php echo esc_html__('Format', 'essential-addons-for-contact-form-7'); ?>
									<?php echo eacf7_fs()->can_use_premium_code__premium_only() ? '' : esc_html__('(Pro Feature)', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</th>
							<td>
								<?php
								$formats = array(
									'm/d/Y' => esc_html__('m/d/Y - (Ex: 11/12/2024)', 'essential-addons-for-contact-form-7'),
									'd/m/Y' => esc_html__('d/m/Y - (Ex: 28/11/2024)', 'essential-addons-for-contact-form-7'),
									'd.m.Y' => esc_html__('d.m.Y - (Ex: 28.11.2024)', 'essential-addons-for-contact-form-7'),
									'n/j/y' => esc_html__('n/j/y - (Ex: 4/28/24)', 'essential-addons-for-contact-form-7'),
									'm/d/y' => esc_html__('m/d/y - (Ex: 04/28/24)', 'essential-addons-for-contact-form-7'),
									'M/d/Y' => esc_html__('M/d/Y - (Ex: Apr/28/2024)', 'essential-addons-for-contact-form-7'),
									'y/m/d' => esc_html__('y/m/d - (Ex: 24/04/28)', 'essential-addons-for-contact-form-7'),
									'Y-m-d' => esc_html__('Y-m-d - (Ex: 2024-04-28)', 'essential-addons-for-contact-form-7'),
									'd-M-y' => esc_html__('d-M-y - (Ex: 28-Apr-24)', 'essential-addons-for-contact-form-7'),
									'm/d/Y h:i A' => esc_html__('m/d/Y h:i A - (Ex: 04/28/2024 08:55 PM)', 'essential-addons-for-contact-form-7'),
									'm/d/Y H:i' => esc_html__('m/d/Y H:i - (Ex: 04/28/2024 20:55)', 'essential-addons-for-contact-form-7'),
									'd/m/Y h:i A' => esc_html__('d/m/Y h:i A - (Ex: 28/04/2024 08:55 PM)', 'essential-addons-for-contact-form-7'),
									'd/m/Y H:i' => esc_html__('d/m/Y H:i - (Ex: 28/04/2024 20:55)', 'essential-addons-for-contact-form-7'),
									'd.m.Y h:i A' => esc_html__('d.m.Y h:i A - (Ex: 28.04.2024 08:55 PM)', 'essential-addons-for-contact-form-7'),
									'd.m.Y H:i' => esc_html__('d.m.Y H:i - (Ex: 28.04.2024 20:55)', 'essential-addons-for-contact-form-7'),
									'h:i A' => esc_html__('h:i A (Only Time Ex: 08:55 PM)', 'essential-addons-for-contact-form-7'),
									'H:i' => esc_html__('H:i (Only Time Ex: 20:55)', 'essential-addons-for-contact-form-7'),
								);
								?>
								<select <?php echo eacf7_fs()->can_use_premium_code__premium_only() ? 'name="values" class="tg-name oneline" id="' . esc_attr($args['content'] . '-format') . '"' : ''; ?>>
									<?php
									foreach ($formats as $key => $value) { ?>
										<option value="<?php echo eacf7_fs()->can_use_premium_code__premium_only() ? esc_attr($key) : ''; ?>"><?php echo $value; ?></option>
									<?php } ?>
								</select>
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
					<?php printf('To display the date and time in your email, insert the mail-tag (%s) in the Mail tab.', '<strong><span class="mail-tag"></span></strong>'); ?>
					<input type="text" class="mail-tag code eacf7-hidden" readonly="readonly"
						id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" />
				</label>
			</p>
		</div>
<?php
	}

	/**
	 * @return DateTime|null
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

DateTime::instance();
