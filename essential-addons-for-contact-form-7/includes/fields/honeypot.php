<?php

namespace EACF7;

if (! defined('ABSPATH')) {
	exit;
}

class Honeypot {
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
		add_filter('wpcf7_spam', array($this, 'spam_check'), 10, 2);
	}

	/**
	 * Add Contact Form Tag
	 */
	public function add_data_handler() {
		if (function_exists('wpcf7_add_form_tag')) {
			wpcf7_add_form_tag(
				array('honeypot'),
				array($this, 'render_honeypot'),
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
	public function render_honeypot($tag) {

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
			'placeholder'  => $tag->get_option('placeholder', '', true),
			'aria-invalid' => $validation_error ? 'true' : 'false',
		];

		if ($tag->is_required()) {
			$atts['aria-required'] = 'true';
		}

		$atts = wpcf7_format_atts($atts);

		$disable_accessibility = ! empty($tag->get_option('disableaccessibility', '', true)) ? $tag->get_option('disableaccessibility', '', true) : '';

		ob_start();
		$html = '';
		if (! $disable_accessibility) {
			$html .= sprintf('<label>%1$s</label>', 'Please leave this field empty.');
		}
		$html .= sprintf('<input %1$s />%2$s', $atts, $validation_error);
?>
		<span class="eacf7-honeypot wpcf7-form-control-wrap"
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
				'honeypot',
				__('Honeypot', 'essential-addons-for-contact-form-7'),
				[$this, 'tag_generator_body_v6'],
				['version' 	=> 2]
			);
		} else {
			$tag_generator->add(
				'honeypot',
				__('Honeypot', 'essential-addons-for-contact-form-7'),
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
						__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Honeypot</a>', 'essential-addons-for-contact-form-7'),
						'https://softlabbd.com/docs/honeypot/'
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
					'honeypot' => esc_html__('Honeypot', 'essential-addons-for-contact-form-7'),
				),
			));

			$tgg->print('field_name'); ?>

			<fieldset>
				<legend><?php echo esc_html__('Disable Accessibility Label', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline address-format">
					<label>
						<input type="checkbox" data-tag-part="option" data-tag-option="disableaccessibility:" name="disableaccessibility" value="true" />
					</label>
				</p>
			</fieldset>

			<fieldset>
				<legend><?php echo esc_html__('Placeholder', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline address-format">
					<label>
						<input type="text" data-tag-part="option" data-tag-option="placeholder:" name="placeholder" />
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
		$type = 'honeypot';
	?>
		<div class="control-box">
			<fieldset>
				<div class="eacf7-notice eacf7-notice-info">
					<p>
						<span class="dashicons dashicons-info-outline"></span>
						<?php
						echo sprintf(
							__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Honeypot</a>', 'essential-addons-for-contact-form-7'),
							'https://softlabbd.com/docs/honeypot/'
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
							<td colspan="2">
								<em><?php echo esc_html__('For better security, change "honeypot" to something more appealing to a bot, such as text including "email" or "website".', 'essential-addons-for-contact-form-7'); ?></em>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo esc_html__('Disable Accessibility Label', 'essential-addons-for-contact-form-7'); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><?php echo esc_html__('Disable Accessibility Label', 'essential-addons-for-contact-form-7'); ?></legend>
									<label>
										<input type="checkbox" name="disableaccessibility:true" class="option" value="on">
									</label>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="placeholder"><?php echo esc_html__('Placeholder', 'essential-addons-for-contact-form-7'); ?></label>
							</th>
							<td>
								<input type="text" name="placeholder" class="placeholder oneline option" id="placeholder" />
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

	/**
	 * Spam check
	 */
	public function spam_check($spam, $submission = null) {
		if ($spam) {
			return $spam;
		}

		$cf7form   = \WPCF7_ContactForm::get_current();
		$form_tags = $cf7form->scan_form_tags();

		foreach ($form_tags as $tag) {
			if ($tag->type == 'honeypot') {
				$honeypot_fields[] = $tag->name;
			}
		}

		// Check if form has Honeypot fields, if not, exit
		if (empty($honeypot_fields)) {
			return $spam;
		}

		foreach ($honeypot_fields as $honeypot_field) {
			$value = isset($_POST[$honeypot_field]) ? $_POST[$honeypot_field] : '';

			// SPAM CHECK #1: Now we check the honeypot!
			if ($value != '') {
				// Chatty Bots!
				$spam = true;

				if ($submission) {
					$submission->add_spam_log(array(
						'agent'  => 'honeypot',
						'reason' => sprintf(
							/* translators: %s: honeypot field ID */
							__('Something is stuck in the honey. Field ID = %s', 'essential-addons-for-contact-form-7'),
							$honeypot_field
						),
					));
				}

				return $spam; // There's no need to go on, we've got flies in the honey.
			}
		}

		return $spam;
	}

	/**
	 * @return Honeypot|null
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Honeypot::instance();
