<?php

namespace EACF7;

if (! defined('ABSPATH')) {
	exit;
}

class Country_List {
	/**
	 * @var null
	 */
	protected static $instance = null;

	public function __construct() {
		add_action('wpcf7_admin_init', [$this, 'add_tag_generator'], 99);
		add_action('wpcf7_init', [$this, 'add_data_handler']);

		add_action('wpcf7_enqueue_scripts', array($this, 'enqueue_scripts'));

		add_filter('wpcf7_validate_country_list', [$this, 'validate'], 10, 2);
		add_filter('wpcf7_validate_country_list*', [$this, 'validate'], 10, 2);

		add_filter('wpcf7_posted_data', [$this, 'render_country_name']);
	}

	public function render_country_name($posted_data) {
		foreach ($posted_data as $key => $value) {
			if (strpos($key, 'country_list') !== false) {
				$countries = eacf7_get_countries();

				if (! empty($value) && ! empty($countries[$value])) {
					$posted_data[$key] = $countries[$value] . ' (' . $value . ')';
				}
			}
		}

		return $posted_data;
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
			wpcf7_add_form_tag(['country_list', 'country_list*'], [
				$this,
				'render_country_list'
			], ['name-attr' => true]);
		}
	}

	public function render_country_list($tag) {

		$tag = new \WPCF7_FormTag($tag);

		// check and make sure tag name is not empty
		if (empty($tag->name)) {
			return '';
		}

		// Validate our fields
		$validation_error = wpcf7_get_validation_error($tag->name);

		$class = $tag->get_class_option('wpcf7-form-control eacf7-country-list');

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


		$atts = wpcf7_format_atts($atts);

		ob_start();
?>

		<span class="wpcf7-form-control-wrap eacf7-country-list-wrap" data-name="<?php echo esc_attr($tag->name); ?>">

			<select <?php echo $atts; ?>>
				<option value=""><?php echo esc_html__('Select Country', 'essential-addons-for-contact-form-7'); ?></option>
				<?php foreach (eacf7_get_countries() as $key => $value) : ?>
					<option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
				<?php endforeach; ?>
			</select>

			<?php echo $validation_error; ?>
		</span>
	<?php
		return ob_get_clean();
	}

	public function enqueue_scripts() {
		wp_enqueue_style('eacf7-select2');
		wp_enqueue_style('eacf7-frontend');

		wp_enqueue_script('eacf7-select2');
		wp_enqueue_script('eacf7-frontend');
	}

	public function add_tag_generator() {
		$tag_generator = \WPCF7_TagGenerator::get_instance();

		if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
			$tag_generator->add(
				'country_list',
				__('Country List', 'essential-addons-for-contact-form-7'),
				[$this, 'tag_generator_body_v6'],
				[
					'version' 	=> 2,
				]
			);
		} else {
			$tag_generator->add(
				'country_list',
				__('Country List', 'essential-addons-for-contact-form-7'),
				[$this, 'tag_generator_body']
			);
		}
	}

	/**
	 * Tag Generator v6
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
						__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Country List</a>', 'essential-addons-for-contact-form-7'),
						'https://softlabbd.com/docs/country-list/'
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
					'country_list' => esc_html__('Country List', 'essential-addons-for-contact-form-7'),
				),
			));

			$tgg->print('field_name');

			$tgg->print('class_attr');
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
	 * @return void
	 * @since 1.0.0
	 */
	public function tag_generator_body($contact_form, $args = '') {
		$args = wp_parse_args($args, []);
		$type = 'country_list';

		$description = esc_html__('Generate a form-tag for this country list dropdown.', 'essential-addons-for-contact-form-7');
	?>
		<div class="control-box">
			<fieldset>
				<div class="eacf7-notice eacf7-notice-info">
					<p>
						<span class="dashicons dashicons-info-outline"></span>
						<?php
						echo sprintf(
							__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Country List</a>', 'essential-addons-for-contact-form-7'),
							'https://softlabbd.com/docs/country-list/'
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
					<?php printf('To display the country name in the email, insert the mail-tag (%s) in the Mail tab.', '<strong><span class="mail-tag"></span></strong>'); ?>
					<input type="text" class="mail-tag code eacf7-hidden" readonly="readonly"
						id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" />
				</label>
			</p>
		</div>
<?php
	}

	/**
	 * @return Country_List|null
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Country_List::instance();
