<?php

namespace EACF7;

if (! defined('ABSPATH')) {
	exit;
}

class Google_Drive_Upload {
	/**
	 * @var null
	 */
	protected static $instance = null;

	public function __construct() {

		add_action('wpcf7_admin_init', [$this, 'add_tag_generator'], 99);
		add_action('wpcf7_init', [$this, 'add_data_handler']);

		// Form scripts
		add_action('wpcf7_enqueue_scripts', array($this, 'enqueue_scripts'));

		// Add uploaded file to mail
		add_filter('wpcf7_posted_data', [$this, 'upload_complete'], 10, 3);

		// Add mail tag
		add_filter('wpcf7_mail_tag_replaced_google_drive_upload', [$this, 'set_mail_tag'], 10, 3);
		add_filter('wpcf7_mail_tag_replaced_google_drive_upload*', [$this, 'set_mail_tag'], 10, 3);

		// validation filter
		add_filter('wpcf7_validate_google_drive_upload', [$this, 'validate'], 10, 2);
		add_filter('wpcf7_validate_google_drive_upload*', [$this, 'validate'], 10, 2);
	}


	/**
	 * Add shortcode handler to CF7.
	 */
	public function add_data_handler() {
		if (function_exists('wpcf7_add_form_tag')) {
			wpcf7_add_form_tag(
				['google_drive_upload', 'google_drive_upload*'],
				[$this, 'render_uploader'],
				['name-attr' => true]
			);
		}
	}

	public function render_uploader($tag) {

		$tag = new \WPCF7_FormTag($tag);

		// check and make sure tag name is not empty
		if (empty($tag->name)) {
			return '';
		}

		// Get current form Object
		$form = \WPCF7_ContactForm::get_current();

		// Validate our fields
		$validation_error = wpcf7_get_validation_error($tag->name);

		$class = wpcf7_form_controls_class($tag->type, 'upload-file-list eacf7-hidden');

		if ($validation_error) {
			$class .= ' wpcf7-not-valid';
		}

		$max_files     = $tag->get_option('max_files', 'int', true);
		$max_files     = ! empty($max_files) ? $max_files : 1;
		$max_size      = $tag->get_option('max_size', 'int', true);
		$upload_folder = $tag->get_option('upload_folder', '.*', true);
		$upload_folder = ! empty($upload_folder) ? sanitize_text_field($upload_folder) : 'root';

		$atts = [
			'name'               => $tag->name,
			'class'              => $class,
			'tabindex'           => $tag->get_option('tabindex', 'signed_int', true),
			'aria-invalid'       => $validation_error ? 'true' : 'false',
			'aria-required'      => $tag->is_required() ? 'true' : 'false',
			'data-max_files'     => $max_files,
			'data-max_size'      => $max_size ? size_format($max_size * 1024 * 1024) : '',
			'data-max_post_size' => eacf7_get_max_upload_size(),
			'data-extensions'    => $tag->get_option('extensions', '.*', true),
			'data-media_library' => in_array('media_library', $tag->options) ? 1 : 0,
			'data-form_id'       => $form->id(),
			'data-field_id'      => $tag->name,
			'data-upload_folder' => $upload_folder,
		];


		$atts = wpcf7_format_atts($atts);

		ob_start();
?>
		<span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr($tag->name); ?>">

			<span class="eacf7-uploader google-drive-upload">
				<span class="eacf7-uploader-body">
					<span class="uploader-text"><?php echo esc_html__("Drag and drop files here or", 'essential-addons-for-contact-form-7') ?></span>

					<span class="uploader-buttons">
						<button type="button" class="eacf7-uploader-browse">
							<span><?php echo esc_html__('Browse Files', 'essential-addons-for-contact-form-7') ?></span>
						</button>
					</span>

				</span>

				<span class="file-list"></span>

				<span class="uploader-hint">
					<span class="max-files-label <?php echo $max_files < 2 ? 'eacf7-hidden' : ''; ?>"><?php printf(__("Upload upto %s Files.", 'essential-addons-for-contact-form-7'), '<span class="number">' . $max_files . '</span>'); ?></span>
					<span class="max-size-label <?php echo empty($max_size) ? 'eacf7-hidden' : ''; ?>"><?php echo __("Max File Size: ", 'essential-addons-for-contact-form-7') . '<span class="size">' . $max_size . ' MB </span>'; ?></span>
				</span>
			</span>

			<input style="position:absolute!important;clip:rect(0,0,0,0)!important;height:1px!important;width:1px!important;border:0!important;overflow:hidden!important;padding:0!important;margin:0!important;" <?php echo $atts; ?> />

			<?php echo $validation_error; ?>
		</span>
	<?php
		return ob_get_clean();
	}

	public function enqueue_scripts() {
		wp_enqueue_style('dashicons');
		wp_enqueue_style('eacf7-frontend');

		wp_enqueue_script('wp-plupload');
		wp_enqueue_script('eacf7-frontend');
	}

	public function add_tag_generator() {
		$tag_generator = \WPCF7_TagGenerator::get_instance();

		if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
			$tag_generator->add(
				'google_drive_upload',
				__('Google Drive Upload', 'essential-addons-for-contact-form-7'),
				[$this, 'tag_generator_body_v6'],
				[
					'version' 	=> 2,
				]
			);
		} else {
			$tag_generator->add(
				'google_drive_upload',
				__('Google Drive Upload', 'essential-addons-for-contact-form-7'),
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
						__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Google Drive Upload</a>', 'essential-addons-for-contact-form-7'),
						'https://softlabbd.com/docs/'
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
					'google_drive_upload' => esc_html__('Google Drive Upload', 'essential-addons-for-contact-form-7'),
				),
			));

			$tgg->print('field_name');

			?>

			<fieldset>
				<legend><?php echo esc_html__('Upload Folder', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline">
					<button type="button" class="button button-large button-primary eacf7-select-folder-btn">
						<?php esc_html_e('Select Upload Folder', 'essential-addons-for-contact-form-7'); ?>
					</button>

					<input type="hidden" data-tag-part="option" data-tag-option="upload_folder:" name="upload_folder" class="oneline option" />

				<p class="description"><?php echo esc_html__('Select the folder where the files will be uploaded.', 'essential-addons-for-contact-form-7'); ?></p>
				</p>
				<p id="folder"></p>
			</fieldset>

			<fieldset>
				<legend><?php echo esc_html__('Allowed Files Extensions', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline">
					<label>
						<input type="text" data-tag-part="option" data-tag-option="extensions:" name="extensions" placeholder="<?php echo esc_attr__('jpg|png|gif') ?>" />
						<p><?php echo esc_html__('Enter pipe (|) separated list of allowed image extensions. Leave blank to allow all extensions.', 'essential-addons-for-contact-form-7'); ?></p>
					</label>
				</p>
			</fieldset>

			<fieldset>
				<legend><?php echo esc_html__('Max File Size (MB)', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline">
					<label>
						<input type="text" data-tag-part="option" data-tag-option="max_size:" name="max_size" />
						<p><?php echo esc_html__('Enter the max size of each file, in megabytes. If left blank, the value defaults to the maximum size the server allows which is 256 MB.', 'essential-addons-for-contact-form-7'); ?></p>
					</label>
				</p>
			</fieldset>

			<fieldset>
				<legend><?php echo esc_html__('Max Files Uploads', 'essential-addons-for-contact-form-7'); ?></legend>
				<p class="oneline">
					<label>
						<input type="number" data-tag-part="option" data-tag-option="max_files:" name="max_files" value="1" />
						<p><?php echo esc_html__('Enter the max number of images to allow. If left blank, the value defaults to 1.', 'essential-addons-for-contact-form-7'); ?></p>
					</label>
				</p>
			</fieldset>
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
		$type = 'google_drive_upload';

	?>
		<div class="control-box">
			<fieldset>
				<div class="eacf7-notice eacf7-notice-info">
					<p>
						<span class="dashicons dashicons-info-outline"></span>
						<?php
						echo sprintf(
							__('Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Google Drive Upload</a>', 'essential-addons-for-contact-form-7'),
							'https://softlabbd.com/docs-category/contact-form-7-extended/'
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
								<p></p>
							</td>
						</tr>

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

						<!-- Upload Folder -->
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr($args['content'] . '-upload_folder'); ?>">
									<?php echo esc_html__('Upload Folder', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</th>
							<td>
								<button type="button" class="button button-large button-primary eacf7-select-folder-btn">
									<?php esc_html_e('Select Upload Folder', 'essential-addons-for-contact-form-7'); ?>
								</button>

								<input type="hidden" name="upload_folder" class="oneline option"
									id="<?php echo esc_attr($args['content'] . '-upload_folder'); ?>" />

								<p class="description"><?php echo esc_html__('Select the folder where the files will be uploaded.', 'essential-addons-for-contact-form-7'); ?></p>
								<p id="folder"></p>
							</td>
						</tr>

						<!-- Allowed Files Extensions -->
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr($args['content'] . '-extensions'); ?>">
									<?php echo esc_html__('Allowed Files Extensions', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</th>
							<td>
								<input type="text" name="extensions" class="oneline option"
									id="<?php echo esc_attr($args['content'] . '-extensions'); ?>"
									placeholder="zip|jpg|png|pdf" />

								<p class="description"><?php echo esc_html__('Enter pipe (|) separated list of allowed file extensions. Leave blank to allow all extensions.', 'essential-addons-for-contact-form-7'); ?></p>
							</td>
						</tr>

						<!-- Max File Size -->
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr($args['content'] . '-max_size'); ?>">
									<?php echo esc_html__('Max File Size (MB)', 'essential-addons-for-contact-form-7'); ?>
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
									<?php echo esc_html__('Max Files Uploads', 'essential-addons-for-contact-form-7'); ?>
								</label>
							</th>
							<td>
								<input type="text" name="max_files" class="option oneline"
									id="<?php echo esc_attr($args['content'] . '-max_files'); ?>"
									value="1" />
								<p class="description"><?php echo esc_html__('Enter the max number of files to allow. If left blank, the value defaults to 1.', 'essential-addons-for-contact-form-7'); ?></p>
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

	public function validate($result, $tag) {
		$tag   = new \WPCF7_FormTag($tag);
		$name  = $tag->name;
		$value = isset($_POST[$name]) ? $_POST[$name] : '';

		if ($tag->is_required() && empty($value)) {
			$result->invalidate($tag, wpcf7_get_message('invalid_required'));
		}

		// Cf7 Conditional Field
		if (class_exists('CF7CF')) {

			$hidden_groups = json_decode(stripslashes($_POST['_wpcf7cf_hidden_groups']));
			$form_id       = \WPCF7_ContactForm::get_current()->id();
			$group_fields  = $this->check_conditional_fields($form_id);

			if (is_null($value) && $tag->is_required()) {
				if (isset($group_fields[$name]) && ! in_array($group_fields[$name], $hidden_groups)) {
					$result->invalidate($tag, wpcf7_get_message('invalid_required'));
				} elseif (! array_key_exists($name, $group_fields)) {
					$result->invalidate($tag, wpcf7_get_message('invalid_required'));
				}

				return $result;
			}

			return $result;
		}

		return $result;
	}

	public function check_conditional_fields($form_id) {

		if (! $form_id) {
			return false;
		}

		// Get visible groups
		$groups = array();

		// Get current form object
		$cf7_post = get_post($form_id);

		// Extract group shortcode
		$regex = get_shortcode_regex(array('group'));

		// Match pattern
		preg_match_all('/' . $regex . '/s', $cf7_post->post_content, $matches);

		if (array_key_exists(3, $matches)) {
			foreach ($matches[3] as $index => $group_name) {
				$name = array_filter(explode(" ", $group_name));
				preg_match('/\[google_drive_upload[*|\s].*?\]/', $matches[0][$index], $file_matches);
				if ($file_matches) {
					$field_name            = shortcode_parse_atts($file_matches[0]);
					$field_name            = preg_replace('/[^a-zA-Z0-9-_]/', '', $field_name[1]);
					$groups[$field_name] = $name[1];
				}
			}
		}

		return $groups;
	}

	public function set_mail_tag($output, $submission, $as_html) {
		if (! $as_html) {
			return $output;
		}

		$field_value = ! empty($_POST[$this->field_id]) ? $_POST[$this->field_id] : '';

		$field_value = str_replace('\\"', '"', $field_value);
		$files       = json_decode($field_value, true);

		if (empty($files)) {
			return $output;
		}

		ob_start();
		foreach ($files as $file) { ?>
			<p style="display: flex; align-items: center; margin-bottom: 5px; padding: 5px; border: 1px solid #ddd;background-color: #FAFAFA;border-radius:3px;">
				<span class="file-icon"><img src="<?php echo $file['iconLink']; ?>"
						style="margin-right:5px;height:auto;width:20px;vertical-align: middle;"
						width="%2$s" /></span>
				<a rel="noopener noreferrer"
					style="display:block;width: 100%;text-decoration: none; color: #ff7f50;vertical-align: middle;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;"
					href="<?php echo esc_url($file['webViewLink']); ?>"
					target="_blank"><?php echo esc_html($file['name']); ?></a>
			</p>
<?php }

		return ob_get_clean();
	}

	public function upload_complete($posted_data) {
		// Submission instance from CF7
		$submission = \WPCF7_Submission::get_instance();

		$this->submission = $submission;

		// Make sure we have the data
		if (! $posted_data) {
			$posted_data = $submission->get_posted_data();
		}


		// Scan and get all form tags from cf7 generator
		$form = $submission->get_contact_form();

		$this->form_id = $form->id();
		$tags          = $form->scan_form_tags();

		if (! empty($tags)) {
			foreach ($tags as $field) {

				if (empty($field->basetype) || $field->basetype != 'google_drive_upload') {
					continue;
				}


				$this->tag = $field;

				// Get the field name
				$this->field_id = $field->name;

				// Get the field value
				$field_value = isset($posted_data[$this->field_id]) ? $posted_data[$this->field_id] : '';

				$files = $this->sanitize_files_input($field_value);

				if (empty($files)) {
					continue;
				}

				$data = [];

				foreach ($files as $file) {
					$data[] = $this->generate_file_data($file);
				}

				$data = array_filter($data);

				$values = wp_list_pluck($data, 'webViewLink');

				$value = implode("\n", $values);

				$posted_data[$this->field_id] = $value;
			}
		}

		return $posted_data;
	}

	private function sanitize_files_input($files) {
		$files = json_decode($files, true);

		if (empty($files) || ! is_array($files)) {
			return [];
		}

		return array_filter(array_map([$this, 'sanitize_file'], $files));
	}

	private function sanitize_file($file) {

		if (empty($file['name'])) {
			return [];
		}

		$sanitized_file = [];
		$rules          = [
			'name'        => 'sanitize_file_name',
			'size'        => 'absint',
			'iconLink'    => 'esc_url_raw',
			'webViewLink' => 'esc_url_raw',
		];

		foreach ($rules as $rule => $callback) {
			$file_attribute          = isset($file[$rule]) ? $file[$rule] : '';
			$sanitized_file[$rule] = $callback($file_attribute);
		}

		return $sanitized_file;
	}

	protected function generate_file_data($file) {

		return [
			'name'        => sanitize_text_field($file['name']),
			'size'        => absint($file['size']),
			'iconLink'    => esc_url_raw($file['iconLink']),
			'webViewLink' => esc_url_raw($file['webViewLink']),
		];
	}


	/**
	 * @return Google_Drive_Upload|null
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Google_Drive_Upload::instance();
