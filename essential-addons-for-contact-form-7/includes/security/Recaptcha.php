<?php

namespace EACF7;

if (! defined('ABSPATH')) {
	exit;
}

class ReCaptcha {
	/**
	 * @var null
	 */
	protected static $instance = null;

	protected $site_keys = [];

	public function __construct() {

		$this->site_keys = \WPCF7::get_option('recaptcha');

		add_action('wpcf7_admin_init', [$this, 'add_tag_generator'], 99);
		add_action('wpcf7_init', [$this, 'add_data_handler']);

		//add_action( 'wpcf7_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

		add_filter('wpcf7_validate_recaptcha', [$this, 'validate'], 10, 2);
		add_filter('wpcf7_validate_recaptcha*', [$this, 'validate'], 10, 2);

		add_action('setup_theme', function () {
			// reCaptcha Verification
			remove_filter('wpcf7_spam', 'wpcf7_recaptcha_verify_response', 9);
			add_filter('wpcf7_spam', [$this, 'recaptcha_check'], 9);

			// reCaptcha Enqueues
			remove_action('wp_enqueue_scripts', 'wpcf7_recaptcha_enqueue_scripts', 20);

			// reCaptcha Footer Javascript
			remove_action('wp_footer', 'wpcf7_recaptcha_onload_script', 40);
		});
	}

	public function is_active() {
		$site_key = eacf7_get_settings('recaptchaSiteKey');
		$secret   = eacf7_get_settings('recaptchaSecretKey');

		return $site_key && $secret;
	}

	public function verify($response_token) {

		$is_human = false;

		if (empty($response_token)) {
			return $is_human;
		}


		$endpoint = 'https://www.google.com/recaptcha/api/siteverify';
		$secret   = eacf7_get_settings('recaptchaSecretKey');

		$request = array(
			'body' => array(
				'secret'   => $secret,
				'response' => $response_token,
				'remoteip' => $_SERVER['REMOTE_ADDR'],
			),
		);

		$response = wp_safe_remote_post(esc_url_raw($endpoint), $request);

		if (is_wp_error($response)) {
			return $is_human;
		}

		if (200 != wp_remote_retrieve_response_code($response)) {

			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('reCaptcha: ' . wp_remote_retrieve_response_message($response));
			}

			return $is_human;
		}

		$response = wp_remote_retrieve_body($response);
		$response = json_decode($response, true);
		$is_human = isset($response['success']) && true == $response['success'];

		return apply_filters('wpcf7_recaptcha_verify_response', $is_human, $response);
	}

	function recaptcha_check($spam) {

		if ($spam) {
			return $spam;
		}

		$contact_form = wpcf7_get_current_contact_form();

		if (! $contact_form) {
			return $spam;
		}

		$tags = $contact_form->scan_form_tags(array('type' => 'recaptcha'));

		if (empty($tags)) {
			return $spam;
		}

		if (! $this->is_active()) {
			return $spam;
		}

		$response_token = ! empty($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

		$spam = ! $this->verify($response_token);

		return $spam;
	}

	function validate($result, $tag) {

		if (empty($tag->name)) {
			$tag->name = 'recaptcha';
		}

		if (empty($_POST['g-recaptcha-response'])) {
			$result->invalidate($tag, wpcf7_get_message('invalid_captcha'));
		}

		return $result;
	}

	/**
	 * Add shortcode handler to CF7.
	 */
	public function add_data_handler() {
		wpcf7_remove_form_tag('recaptcha');

		wpcf7_add_form_tag(['recaptcha', 'recaptcha*'], [$this, 'render_recaptcha'], [
			'name-attr'     => false,
			'not-for-mail'  => true,
			'display-block' => true,
		]);
	}

	public function render_recaptcha($tag) {

		$tag = new \WPCF7_FormTag($tag);

		// check and make sure tag name is not empty
		if (empty($tag->name)) {
			$tag->name = 'recaptcha';
		}

		// Validate our fields
		$validation_error = wpcf7_get_validation_error($tag->name);

		$class = $tag->get_class_option('wpcf7-form-control eacf7-recaptcha');

		if ($validation_error) {
			$class .= ' wpcf7-not-valid';
		}

		$atts = [
			'data-sitekey' => eacf7_get_settings('recaptchaSiteKey'),
			'class'        => $class,
			'name'         => $tag->name,
			'aria-invalid' => $validation_error ? 'true' : 'false',
			'id'           => $tag->get_id_option(),
			'data-theme'   => $tag->get_option('theme', '', true),
			'data-size'    => $tag->get_option('size', '', true),
			'data-align'   => $tag->get_option('align', '', true),
		];

		$atts = wpcf7_format_atts($atts);

		ob_start();
?>

		<span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr($tag->name); ?>">

			<span <?php echo $atts; ?>></span>

			<span class="wpcf7-form-control-feedback" role="alert"><?php echo $validation_error; ?></span></span>
		</span>
	<?php
		return ob_get_clean();
	}

	public function enqueue_scripts() {
		wp_enqueue_style('eacf7-frontend');

		wp_enqueue_script('eacf7-frontend');

		$src = add_query_arg(array(
			'hl'     => get_locale(),
			'render' => 'explicit',
			'onload' => 'recaptchaCallback',
		), 'https://www.google.com/recaptcha/api.js');
		wp_enqueue_script('google-recaptcha', $src, array(), '2.0', true);
	}

	public function add_tag_generator() {
		if (class_exists('WPCF7_TagGenerator')) {
			$tag_generator = \WPCF7_TagGenerator::get_instance();

			if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
				$tag_generator->add(
					'recaptcha',
					__('reCaptcha', 'essential-addons-for-contact-form-7'),
					[$this, 'tag_generator_body_v6'],
					['version'     => 2]
				);
			} else {
				$tag_generator->add(
					'recaptcha',
					__('reCaptcha', 'essential-addons-for-contact-form-7'),
					[$this, 'tag_generator_body']
				);
			}
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
					'recaptcha' => esc_html__('reCaptcha', 'essential-addons-for-contact-form-7'),
				),
			));

			?>
			<!-- Theme -->
			<fieldset>
				<legend><?php echo esc_html__('Theme', 'essential-addons-for-contact-form-7'); ?></legend>
				<p>
					<label for="light">
						<input type="radio" data-tag-part="option" data-tag-option="theme:" name="theme" class="option" id="light" value="light" selected><?php echo esc_html__('Light', 'essential-addons-for-contact-form-7'); ?>
					</label>
					<label for="dark">
						<input type="radio" data-tag-part="option" data-tag-option="theme:" name="theme" class="option" id="dark" value="dark"><?php echo esc_html__('Dark', 'essential-addons-for-contact-form-7'); ?>
					</label>
				</p>
			</fieldset>

			<!-- Size -->
			<fieldset>
				<legend><?php echo esc_html__('Size', 'essential-addons-for-contact-form-7'); ?></legend>
				<p>
					<label for="normal">
						<input type="radio" data-tag-part="option" data-tag-option="size:" name="size" class="option" id="normal" value="normal" selected><?php echo esc_html__('Normal', 'essential-addons-for-contact-form-7'); ?>
					</label>
					<label for="compact">
						<input type="radio" data-tag-part="option" data-tag-option="size:" name="size" class="option" id="compact" value="compact"><?php echo esc_html__('Compact', 'essential-addons-for-contact-form-7'); ?>
					</label>
				</p>
			</fieldset>
			
			<!-- Alignment -->
			<fieldset>
				<legend><?php echo esc_html__('Alignment', 'essential-addons-for-contact-form-7'); ?></legend>
				<p>
					<label for="left">
						<input type="radio" data-tag-part="option" data-tag-option="align:" name="align" class="option" id="left" value="left" selected><?php echo esc_html__('Left', 'essential-addons-for-contact-form-7'); ?>
					</label>
					<label for="center">
						<input type="radio" data-tag-part="option" data-tag-option="align:" name="align" class="option" id="center" value="center"><?php echo esc_html__('Center', 'essential-addons-for-contact-form-7'); ?>
					</label>
					<label for="right">
						<input type="radio" data-tag-part="option" data-tag-option="align:" name="align" class="option" id="right" value="right"><?php echo esc_html__('Right', 'essential-addons-for-contact-form-7'); ?>
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

	public function tag_generator_body($contact_form, $args = '') {
		$args = wp_parse_args($args, []);
		$type = 'recaptcha';

		$description = esc_html__('Generate a form-tag for the reCaptcha field.', 'essential-addons-for-contact-form-7');
	?>
		<div class="control-box">
			<fieldset>
				<legend><?php echo esc_html($description); ?></legend>
				<table class="form-table">
					<tbody>

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

						<!-- Theme -->
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr($args['content'] . '-theme'); ?>">
									<?php echo esc_html__('Theme', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</th>
							<td>
								<label>
									<input type="radio" name="theme" class="option" value="light" checked="checked" />
									<?php echo esc_html__('Light', 'essential-addons-for-contact-form-7'); ?>
								</label>
								<label>
									<input type="radio" name="theme" class="option" value="dark" />
									<?php echo esc_html__('Dark', 'essential-addons-for-contact-form-7'); ?>
								</label>

								<p class="description"><?php echo esc_html__('Select the color theme of the captcha widget.', 'essential-addons-for-contact-form-7'); ?></p>
							</td>
						</tr>

						<!-- Size -->
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr($args['content'] . '-size'); ?>">
									<?php echo esc_html__('Size', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</th>
							<td>
								<label>
									<input type="radio" name="size" class="option" value="normal" checked="checked" />
									<?php echo esc_html__('Normal', 'essential-addons-for-contact-form-7'); ?>
								</label>
								<label>
									<input type="radio" name="size" class="option" value="compact" />
									<?php echo esc_html__('Compact', 'essential-addons-for-contact-form-7'); ?>
								</label>

								<p class="description"><?php echo esc_html__('Select the size of the captcha widget.', 'essential-addons-for-contact-form-7'); ?></p>
							</td>
						</tr>

						<!-- Alignment -->
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr($args['content'] . '-align'); ?>">
									<?php echo esc_html__('Alignment', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</th>
							<td>
								<label>
									<input type="radio" name="align" class="option" value="left" checked="checked" />
									<?php echo esc_html__('Left', 'essential-addons-for-contact-form-7'); ?>
								</label>
								<label>
									<input type="radio" name="align" class="option" value="center" />
									<?php echo esc_html__('Center', 'essential-addons-for-contact-form-7'); ?>
								</label>
								<label>
									<input type="radio" name="align" class="option" value="right" />
									<?php echo esc_html__('Right', 'essential-addons-for-contact-form-7'); ?>
								</label>

								<p class="description"><?php echo esc_html__('Select the alignment of the captcha widget.', 'essential-addons-for-contact-form-7'); ?></p>
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
		</div>
<?php
	}


	/**
	 * @return ReCaptcha|null
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

ReCaptcha::instance();
