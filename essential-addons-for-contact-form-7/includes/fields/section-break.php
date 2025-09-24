<?php

namespace EACF7;

if (! defined('ABSPATH')) {
	exit;
}

class Section_Break {
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

		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
	}

	/**
	 * Tag Generator
	 * @since 1.0.0
	 * @author monzuralam
	 */
	public function add_tag_generator() {
		if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
			wpcf7_add_tag_generator(
				'section_break',
				__('Section Break', 'essential-addons-for-contact-form-7'),
				'section_break',
				[$this, 'tag_generator_body_v6'],
				[
					'version' 	=> 2,
					'name'		=> 'section_break',
				]
			);
		} else {
			wpcf7_add_tag_generator(
				'section_break',
				__('Section Break', 'essential-addons-for-contact-form-7'),
				'section_break',
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
						__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Section Break</a>', 'essential-addons-for-contact-form-7'),
						'https://softlabbd.com/docs/section-break/'
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
					'section_break' => esc_html__('Section Break', 'essential-addons-for-contact-form-7'),
				),
			));

			$tgg->print('field_name'); ?>

			<!-- Title -->
			<fieldset>
				<legend><?php echo esc_html__('Title', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline address-format">
					<label>
						<input type="text" data-tag-part="value" name="values" />
					</label>
				</p>
			</fieldset>

			<!-- Content -->
			<fieldset>
				<legend><?php echo esc_html__('Content', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline address-format">
					<label>
						<textarea data-tag-part="content" name="content"></textarea>
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
							__('Not sure hot to set this? Check our documentation on <a href="%1$s" target="_blank">Section Break</a>', 'essential-addons-for-contact-form-7'),
							'https://softlabbd.com/docs/section-break/'
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
								<input type="text" name="name" class="tg-name oneline"
									id="<?php echo esc_attr($args['content'] . '-name'); ?>" />
							</td>
						</tr>

						<!-- Title -->
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr($args['content'] . '-title'); ?>">
									<?php echo esc_html__('Title', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</th>
							<td>
								<input type="text" name="values" class="option oneline"
									id="<?php echo esc_attr($args['content'] . '-title'); ?>" />
							</td>
						</tr>

						<!-- Content -->
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr($args['content'] . '-content'); ?>">
									<?php echo esc_html__('Content', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</th>
							<td>
								<textarea type="text" name="content" class="oneline"
									id="<?php echo esc_attr($args['content'] . '-content'); ?>"></textarea>
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
		</div>
	<?php
	}

	/**
	 * Add shortcode handler to CF7.
	 */
	public function add_data_handler() {
		wpcf7_add_form_tag(['section_break'], [
			$this,
			'render_section_break'
		], ['name-attr' => true]);
	}

	/**
	 * Render Rich Text
	 * @since 1.0.0
	 * @author monzuralam
	 */
	public function render_section_break($tag) {

		$tag = new \WPCF7_FormTag($tag);

		// check and make sure tag name is not empty
		if (empty($tag->name)) {
			return '';
		}

		$class = $tag->get_class_option('wpcf7-form-control eacf7-section-break');

		$atts = [
			'class' => $class,
			'id'    => $tag->get_id_option(),
			'name'  => $tag->name,
		];

		$atts['class'] .= ' ' . $tag->name;

		$atts = wpcf7_format_atts($atts);

		$section_title = ! empty($tag['values'][0]) ? esc_html($tag['values'][0]) : '';
		$section_desc  = ! empty($tag['content']) ? esc_html($tag['content']) : '';

		ob_start();
	?>

		<span class="wpcf7-form-control-wrap eacf7-section-break-wrap"
			data-name="<?php echo esc_attr($tag->name); ?>">

			<?php if (! empty($section_title)) { ?>
				<h3 class="eacf7-section-break-title"><?php echo $section_title; ?></h3>
			<?php } ?>

			<?php if (! empty($section_desc)) { ?>
				<p class="eacf7-section-break-content"><?php echo $section_desc; ?></p>
			<?php } ?>

			<hr <?php echo $atts; ?> />

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


	/**
	 * @return Section_Break|null
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Section_Break::instance();
