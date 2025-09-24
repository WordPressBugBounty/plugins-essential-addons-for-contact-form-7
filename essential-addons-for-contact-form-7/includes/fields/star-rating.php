<?php

namespace EACF7;

if (! defined('ABSPATH')) exit;

class Rating {
	/**
	 * @var null
	 */
	protected static $instance = null;

	public function __construct() {
		add_action('wpcf7_admin_init', [$this, 'add_tag_generator'], 99);
		add_action('wpcf7_init', [$this, 'add_data_handler']);

		add_action('wpcf7_enqueue_scripts', array($this, 'enqueue_scripts'));

		add_filter('wpcf7_validate_rating', [$this, 'validate'], 10, 2);
		add_filter('wpcf7_validate_rating*', [$this, 'validate'], 10, 2);

		add_filter('wpcf7_mail_tag_replaced_rating', [$this, 'mail_tag_replaced'], 10, 4);
	}

	public function mail_tag_replaced($replaced, $submitted, $html, $mail_tag) {
		if (!$html) {
			return $submitted;
		}

		$value = intval($submitted);
		$html  = '';

		for ($i = 1; $i <= $value; $i++) {
			$html .= '<img width="20" height="20" draggable="false" role="img" class="emoji" alt="⭐" src="' . EACF7_ASSETS . '/images/fields/star.svg"/>';
		}

		$html .= '(' . $value . '/5)';

		return $html;
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
			wpcf7_add_form_tag(['rating', 'rating*'], [
				$this,
				'render_rating'
			], ['name-attr' => true]);
		}
	}

	public function render_rating($tag) {

		$tag = new \WPCF7_FormTag($tag);

		// check and make sure tag name is not empty
		if (empty($tag->name)) {
			return '';
		}

		// Validate our fields
		$validation_error = wpcf7_get_validation_error($tag->name);

		$class = $tag->get_class_option('wpcf7-form-control eacf7-rating');

		if ($validation_error) {
			$class .= ' wpcf7-not-valid';
		}

		$atts = [
			'class'			=> 'eacf7-star-rating',
			'name'          => $tag->name,
			'aria-invalid'  => $validation_error ? 'true' : 'false',
			'aria-required' => $tag->is_required() ? 'true' : 'false',
		];

		$icon 		= !empty($tag->get_option('icon', '', true)) ? $tag->get_option('icon', '', true) : 'star';
		$icon_class = (is_array($tag->values) && ! empty($tag->values)) ? $tag->values[0] : '';
		$default 	= $tag->get_option('default', '', true) ?? '5';
		$style		= !empty($tag->get_option('style', '', true)) ? $tag->get_option('style', '', true) : '0';

		if (isset($style)) {
			$class .= " style-$style";
		}

		$atts = wpcf7_format_atts($atts);

		ob_start();
?>

		<span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr($tag->name); ?>">

			<div class="<?php echo esc_attr($class); ?>" id="<?php echo esc_attr($tag->get_id_option()); ?>" data-style="<?php echo esc_attr($style); ?>" data-default="<?php echo esc_attr($default); ?>">
				<?php for ($i = 1; $i <= 5; $i++) { ?>
					<span class="eacf7-rating-star" data-value="<?php echo esc_attr($i); ?>">
						<?php
						switch ($icon) {
							case 'star2':
						?>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
									<rect x="0" fill="none" width="20" height="20" />
									<g>
										<path d="M10 1L7 7l-6 .75 4.13 4.62L4 19l6-3 6 3-1.12-6.63L19 7.75 13 7zm0 2.24l2.34 4.69 4.65.58-3.18 3.56.87 5.15L10 14.88l-4.68 2.34.87-5.15-3.18-3.56 4.65-.58z" />
									</g>
								</svg>
							<?php
								break;

							case 'heart':
							?>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
									<rect x="0" fill="none" width="20" height="20" />
									<g>
										<path d="M10 17.12c3.33-1.4 5.74-3.79 7.04-6.21 1.28-2.41 1.46-4.81.32-6.25-1.03-1.29-2.37-1.78-3.73-1.74s-2.68.63-3.63 1.46c-.95-.83-2.27-1.42-3.63-1.46s-2.7.45-3.73 1.74c-1.14 1.44-.96 3.84.34 6.25 1.28 2.42 3.69 4.81 7.02 6.21z" />
									</g>
								</svg>
							<?php
								break;

							case 'thumbs-up':
							?>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
									<rect x="0" fill="none" width="20" height="20" />
									<g>
										<path d="M12.72 2c.15-.02.26.02.41.07.56.19.83.79.66 1.35-.17.55-1 3.04-1 3.58 0 .53.75 1 1.35 1h3c.6 0 1 .4 1 1s-2 7-2 7c-.17.39-.55 1-1 1H6V8h2.14c.41-.41 3.3-4.71 3.58-5.27.21-.41.6-.68 1-.73zM2 8h2v9H2V8z" />
									</g>
								</svg>
							<?php
								break;

							case 'smile':
							?>
								<svg width="20px" height="20px" viewBox="0 0 1024 1024" fill="#000000" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg">
									<g id="SVGRepo_bgCarrier" stroke-width="0" />
									<g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" />
									<g id="SVGRepo_iconCarrier">
										<path d="M324.8 440c34.4 0 62.4-28 62.4-62.4s-28-62.4-62.4-62.4-62.4 28-62.4 62.4 28 62.4 62.4 62.4z m374.4 0c34.4 0 62.4-28 62.4-62.4s-28-62.4-62.4-62.4-62.4 28-62.4 62.4 28 62.4 62.4 62.4zM340 709.6C384 744 440.8 764.8 512 764.8s128-20.8 172-55.2c26.4-21.6 42.4-42.4 50.4-58.4 6.4-12 0.8-27.2-11.2-33.6s-27.2-0.8-33.6 11.2c-0.8 1.6-3.2 6.4-8 12-7.2 10.4-17.6 20-28.8 29.6-34.4 28-80.8 44.8-140.8 44.8s-105.6-16.8-140.8-44.8c-12-9.6-21.6-20-28.8-29.6-4-5.6-7.2-9.6-8-12-6.4-12-20.8-17.6-33.6-11.2s-17.6 20.8-11.2 33.6c8 16 24 36.8 50.4 58.4z" fill="" />
										<path d="M512 1010.4c-276.8 0-502.4-225.6-502.4-502.4S235.2 5.6 512 5.6s502.4 225.6 502.4 502.4-225.6 502.4-502.4 502.4zM512 53.6C261.6 53.6 57.6 257.6 57.6 508s204 454.4 454.4 454.4 454.4-204 454.4-454.4S762.4 53.6 512 53.6z" fill="" />
									</g>

								</svg>
							<?php
								break;

							case 'ok':
							?>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
									<rect x="0" fill="none" width="20" height="20" />
									<g>
										<path d="M10 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm-.615 12.66h-1.34l-3.24-4.54 1.34-1.25 2.57 2.4 5.14-5.93 1.34.94-5.81 8.38z" />
									</g>
								</svg>
							<?php
								break;

							case 'custom':
							?>
								<i class="<?php echo esc_attr($icon_class); ?>"></i>
							<?php
								break;

							default:
							?>
								<svg width="22" height="22" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 53.867 53.867" style="enable-background:new 0 0 53.867 53.867;">
									<polygon points="26.934,1.318 35.256,18.182 53.867,20.887 40.4,34.013 43.579,52.549 26.934,43.798 10.288,52.549 13.467,34.013 0,20.887 18.611,18.182 "></polygon>
								</svg>
						<?php
								break;
						}
						?>
					</span>
				<?php } ?>

				<input type="hidden" <?php echo $atts; ?> />
			</div>

			<?php if ($style == '3') { ?>
				<div class="rating-message"></div>
			<?php } ?>

			<span class="wpcf7-form-control-feedback" role="alert"><?php echo $validation_error; ?></span>
		</span>
	<?php
		return ob_get_clean();
	}

	public function enqueue_scripts() {
		wp_enqueue_style('eacf7-font-awesome', EACF7_ASSETS . '/vendor/fontawesome/css/all.min.css', array('eacf7-frontend'), '6.0', 'all');
		wp_enqueue_style('eacf7-frontend');

		wp_enqueue_script('eacf7-frontend');
	}

	public function add_tag_generator() {
		$tag_generator = \WPCF7_TagGenerator::get_instance();

		if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
			$tag_generator->add(
				'rating',
				__('Star Rating', 'essential-addons-for-contact-form-7'),
				[$this, 'tag_generator_body_v6'],
				[
					'version' 	=> 2,
				]
			);
		} else {
			$tag_generator->add(
				'rating',
				__('Star Rating', 'essential-addons-for-contact-form-7'),
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
						__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Star Rating</a>', 'essential-addons-for-contact-form-7'),
						'https://softlabbd.com/docs/star-rating/'
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
					'rating' => esc_html__('Star Rating', 'essential-addons-for-contact-form-7'),
				),
			));

			$tgg->print('field_name');

			?>
			<fieldset>
				<legend><?php echo esc_html__('Rating Icon', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline rating-icon">
					<label>
						<input type="radio" data-tag-part="option" data-tag-option="icon:" name="icon" value="star1" checked />
						<?php echo esc_html__('Star 1', 'essential-addons-for-contact-form-7'); ?>
					</label>

					<label>
						<input type="radio" data-tag-part="option" data-tag-option="icon:" name="icon" value="star2" />
						<?php echo esc_html__('Star 2', 'essential-addons-for-contact-form-7'); ?>
					</label>

					<label>
						<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'data-tag-part="option" data-tag-option="icon:" name="icon" value="heart"' : ''; ?> />
						<?php
						echo esc_html__('Heart', 'essential-addons-for-contact-form-7');
						echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
						?>
					</label>

					<label>
						<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'data-tag-part="option" data-tag-option="icon:" name="icon" value="thumbs-up"' : ''; ?> />
						<?php
						echo esc_html__('Thumbs Up', 'essential-addons-for-contact-form-7');
						echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
						?>
					</label>

					<label>
						<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'data-tag-part="option" data-tag-option="icon:" name="icon" value="smile"' : ''; ?> />
						<?php
						echo esc_html__('Smile', 'essential-addons-for-contact-form-7');
						echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
						?>
					</label>

					<label>
						<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'data-tag-part="option" data-tag-option="icon:" name="icon" value="ok"' : ''; ?> />
						<?php
						echo esc_html__('Ok', 'essential-addons-for-contact-form-7');
						echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
						?>
					</label>

					<label>
						<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'data-tag-part="option" data-tag-option="icon:" name="icon" value="custom"' : ''; ?> />
						<?php
						echo esc_html__('Custom', 'essential-addons-for-contact-form-7');
						echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
						?>
					</label>
				</p>
			</fieldset>

			<fieldset class="custom-icon-class">
				<legend>
					<?php
					echo esc_html__('Icon Class', 'essential-addons-for-contact-form-7');
					echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
					?>
				</legend>
				<p class="oneline custom-class">
					<input type="text" data-tag-part="value" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'name="values"' : ''; ?>>
				</p>
			</fieldset>

			<fieldset>
				<legend><?php echo esc_html__('Style', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline rating-icon">
					<label>
						<input type="radio" data-tag-part="option" data-tag-option="style:" name="style" value="0" checked />
						<?php echo esc_html__('Default', 'essential-addons-for-contact-form-7'); ?>
					</label>

					<label>
						<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'data-tag-part="option" data-tag-option="style:" name="style" value="1"' : ''; ?> />
						<?php
						echo esc_html__('One', 'essential-addons-for-contact-form-7');
						echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
						?>
					</label>

					<label>
						<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'data-tag-part="option" data-tag-option="style:" name="style" value="2"' : ''; ?> />
						<?php
						echo esc_html__('Two', 'essential-addons-for-contact-form-7');
						echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
						?>
					</label>
				</p>
			</fieldset>

			<fieldset>
				<legend>
					<?php echo esc_html__('Default Rating', 'essential-addons-for-contact-form-7'); ?>
				</legend>
				<p class="oneline custom-class">
					<input type="number" data-tag-part="option" data-tag-option="default:" name="default" value="5">
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
		$type = 'rating';

	?>
		<div class="control-box">
			<fieldset>
				<div class="eacf7-notice eacf7-notice-info">
					<p>
						<span class="dashicons dashicons-info-outline"></span>
						<?php
						echo sprintf(
							__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Star Rating</a>', 'essential-addons-for-contact-form-7'),
							'https://softlabbd.com/docs/star-rating/'
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
										<input type="checkbox" name="required" /> <?php echo esc_html__('Required field', 'essential-addons-for-contact-form-7'); ?>
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
								<input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr($args['content'] . '-name'); ?>" />
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label>
									<?php echo esc_html__('Rating Icon', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</th>
							<td class="oneline rating-icon">
								<label>
									<input type="radio" class="option" name="icon" value="star1" checked />
									<?php echo esc_html__('Star 1', 'essential-addons-for-contact-form-7'); ?>
								</label>

								<label>
									<input type="radio" class="option" name="icon" value="star2" />
									<?php echo esc_html__('Star 2', 'essential-addons-for-contact-form-7'); ?>
								</label>

								<label>
									<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'class="option" name="icon" value="heart"' : ''; ?> />
									<?php
									echo esc_html__('Heart', 'essential-addons-for-contact-form-7');
									echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
									?>
								</label>

								<label>
									<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'class="option" name="icon" value="thumbs-up"' : ''; ?> />
									<?php
									echo esc_html__('Thumbs Up', 'essential-addons-for-contact-form-7');
									echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
									?>
								</label>

								<label>
									<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'class="option" name="icon" value="smile"' : ''; ?> />
									<?php
									echo esc_html__('Smile', 'essential-addons-for-contact-form-7');
									echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
									?>
								</label>

								<label>
									<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'class="option" name="icon" value="ok"' : ''; ?> />
									<?php
									echo esc_html__('Ok', 'essential-addons-for-contact-form-7');
									echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
									?>
								</label>

								<label>
									<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'data-tag-part="option" data-tag-option="icon:" name="icon" value="custom"' : ''; ?> />
									<?php
									echo esc_html__('Custom', 'essential-addons-for-contact-form-7');
									echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
									?>
								</label>
							</td>
						</tr>

						<tr class="custom-icon-class">
							<th>
								<label>
									<?php
									echo esc_html__('Icon Class', 'essential-addons-for-contact-form-7');
									echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
									?>
								</label>
							</th>
							<td class="oneline custom-class">
								<input type="text" data-tag-part="value" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'name="values"' : ''; ?>>
							</td>
						</tr>

						<tr>
							<th>
								<label><?php echo esc_html__('Style', 'essential-addons-for-contact-form-7'); ?></label>
							</th>
							<td class="oneline rating-icon">
								<label>
									<input type="radio" class="option" name="style" value="0" checked />
									<?php echo esc_html__('Default', 'essential-addons-for-contact-form-7'); ?>
								</label>

								<label>
									<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'class="option" name="style" value="1"' : ''; ?> />
									<?php
									echo esc_html__('One', 'essential-addons-for-contact-form-7');
									echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
									?>
								</label>

								<label>
									<input type="radio" <?php echo (eacf7_fs()->can_use_premium_code__premium_only()) ? 'class="option" name="style" value="2"' : ''; ?> />
									<?php
									echo esc_html__('Two', 'essential-addons-for-contact-form-7');
									echo (!eacf7_fs()->can_use_premium_code__premium_only()) ? esc_html__(' (Pro)', 'essential-addons-for-contact-form-7') : '';
									?>
								</label>
							</td>
						</tr>

						<tr>
							<th>
								<label>
									<?php echo esc_html__('Default Rating', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</th>
							<td class="oneline custom-class">
								<input type="number" class="option" name="default" value="5">
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
								<input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr($args['content'] . '-id'); ?>" />
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

			<br class="clear" />

			<p class="description mail-tag">
				<label for="<?php echo esc_attr($args['content'] . '-mailtag'); ?>">
					<?php printf('To display the rating in the email, insert the mail-tag (%s) in the Mail tab.', '<strong><span class="mail-tag"></span></strong>'); ?>
					<input type="text" class="mail-tag code eacf7-hidden" readonly="readonly" id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" />
				</label>
			</p>
		</div>
<?php
	}

	/**
	 * @return Rating|null
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Rating::instance();
