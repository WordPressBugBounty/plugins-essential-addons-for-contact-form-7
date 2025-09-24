<?php

namespace EACF7;

if (! defined('ABSPATH')) {
	exit;
}

class Shortcode {
	/**
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action('wpcf7_admin_init', [$this, 'add_tag_generator'], 99);

		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

		// remove tag
		add_filter('wpcf7_collect_mail_tags', array($this, 'remove_mail_tag'), 10, 3);

		//
		add_filter('wpcf7_contact_form_properties', array($this, 'properties'), 99);

		// allow shortcode
		add_filter('wpcf7_form_elements',  array($this, 'do_shortcode'));

		// Registering the shortcode
		add_shortcode('test', array($this, 'test'));
	}

	function test() {
		return "<p>Hello, this is my custom shortcode!</p>";
	}

	/**
	 * Tag Generator
	 * @since 1.0.0
	 * @author monzuralam
	 */
	public function add_tag_generator() {
		if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
			wpcf7_add_tag_generator(
				'shortcode',
				__('Shortcode', 'essential-addons-for-contact-form-7'),
				'shortcode',
				[$this, 'tag_generator_body_v6'],
				[
					'version' 	=> 2,
					'name'		=> 'shortcode',
				]
			);
		} else {
			wpcf7_add_tag_generator(
				'shortcode',
				__('Shortcode', 'essential-addons-for-contact-form-7'),
				'shortcode',
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
						__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Shortcode</a>', 'essential-addons-for-contact-form-7'),
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
					'shortcode' => esc_html__('Shortcode', 'essential-addons-for-contact-form-7'),
				),
			));

			$tgg->print('field_name'); ?>

			<fieldset>
				<legend><?php echo esc_html__('Shortcode', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline">
					<label>
						<input type="text" data-tag-part="content" name="content" />
					</label>
				<p><?php echo esc_html__('Write here your shortcode, which render on the form.', 'essential-addons-for-contact-form-7'); ?></p>
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
							__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Shortcode</a>', 'essential-addons-for-contact-form-7'),
							'https://softlabbd.com/docs-category/essential-addons-for-contact-form-7/'
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
									<?php echo esc_html__('Shortcode', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</th>
							<td>
								<input type="text" name="content" class="oneline" id="<?php echo esc_attr($args['content'] . '-content'); ?>" />
								<p><?php echo esc_html__('Write here your shortcode, which render on the form.', 'essential-addons-for-contact-form-7') ?></p>
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
	 * Assets
	 * @since 1.0.0
	 * @author monzuralam
	 */
	public function enqueue_scripts() {
		wp_enqueue_style('eacf7-frontend');

		wp_enqueue_script('eacf7-frontend');
	}

	/**
	 * Remove mail tags on the mail tab
	 * @since 1.0.0
	 * @author monzuralam
	 */
	public function remove_mail_tag($tags, $contact_form, $args) {
		// Iterate over the tags array
		foreach ($tags as $key => $tag) {
			// Check if the tag matches the pattern 'shortcode' followed by an optional dash and number
			if (preg_match('/^shortcode(-\d+)?$/', $tag)) {
				// Remove the matching tag from the array
				unset($tags[$key]);
			}
		}

		// Return the modified tags array
		return $tags;
	}

	/**
	 * Fix Shortcode render issue
	 * @since 1.0.0
	 * @author monzuralam
	 */
	public function properties($properties) {
		// Check if not in admin
		if (! is_admin()) {
			$form = $properties['form'];
			$form_parts = preg_split('/(\[\/?shortcode(?:\]|\s.*?\]))/', $form, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			ob_start();

			foreach ($form_parts as $form_part) {
				if (preg_match('/^\[shortcode/', $form_part)) {
					echo '';
				} elseif ($form_part == '[/shortcode]') {
					echo '';
				} else {
					echo $form_part;
				}
			}

			$properties['form'] = ob_get_clean();
		}

		return $properties;
	}

	/**
	 * Shortcode support
	 * @since 1.0.0
	 */
	public function do_shortcode($form) {
		return do_shortcode($form);
	}

	/**
	 * @return Shortcode|null
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Shortcode::instance();
