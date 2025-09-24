<?php

namespace CF7_Extended;

if ( ! defined( 'ABSPATH' ) ) exit;

class Uploader {

	private static $instance = null;

	private $type = 'file_upload';
	public $tag;
	public $form_id;
	public $field_id;
	public $field_data;
	public $submission;

	private $denylist = array(
		'ade',
		'adp',
		'app',
		'asp',
		'bas',
		'bat',
		'cer',
		'cgi',
		'chm',
		'cmd',
		'com',
		'cpl',
		'crt',
		'csh',
		'csr',
		'dll',
		'drv',
		'exe',
		'fxp',
		'flv',
		'hlp',
		'hta',
		'htaccess',
		'htm',
		'html',
		'htpasswd',
		'inf',
		'ins',
		'isp',
		'jar',
		'js',
		'jse',
		'jsp',
		'ksh',
		'lnk',
		'mdb',
		'mde',
		'mdt',
		'mdw',
		'msc',
		'msi',
		'msp',
		'mst',
		'ops',
		'pcd',
		'php',
		'pif',
		'pl',
		'prg',
		'ps1',
		'ps2',
		'py',
		'rb',
		'reg',
		'scr',
		'sct',
		'sh',
		'shb',
		'shs',
		'sys',
		'swf',
		'tmp',
		'torrent',
		'url',
		'vb',
		'vbe',
		'vbs',
		'vbscript',
		'wsc',
		'wsf',
		'wsf',
		'wsh',
		'dfxp',
		'onetmp'
	);

	public function __construct() {
		add_action( 'wp_ajax_eacf7_upload_file', [ $this, 'upload_file' ] );
		add_action( 'wp_ajax_nopriv_eacf7_upload_file', [ $this, 'upload_file' ] );

		add_filter( 'wpcf7_posted_data', [ $this, 'upload_complete' ], 10, 1 );

		add_filter( 'wpcf7_mail_tag_replaced_file_upload', [ $this, 'set_mail_tag' ], 10, 3 );
		add_filter( 'wpcf7_mail_tag_replaced_file_upload*', [ $this, 'set_mail_tag' ], 10, 3 );

		add_filter( 'wpcf7_mail_tag_replaced_image_upload', [ $this, 'set_mail_tag' ], 10, 3 );
		add_filter( 'wpcf7_mail_tag_replaced_image_upload*', [ $this, 'set_mail_tag' ], 10, 3 );

		//validation filter
		add_filter( 'wpcf7_validate_file_upload', [ $this, 'validate' ], 10, 2 );
		add_filter( 'wpcf7_validate_file_upload*', [ $this, 'validate' ], 10, 2 );

		add_filter( 'wpcf7_validate_image_upload', [ $this, 'validate' ], 10, 2 );
		add_filter( 'wpcf7_validate_image_upload*', [ $this, 'validate' ], 10, 2 );
	}

	public function validate( $result, $tag ) {
		$tag   = new \WPCF7_FormTag( $tag );
		$name  = $tag->name;
		$value = isset( $_POST[ $name ] ) ? $_POST[ $name ] : '';

		if ( $tag->is_required() && empty( $value ) ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}

		// Cf7 Conditional Field
		if ( class_exists( 'CF7CF' ) ) {

			$hidden_groups = json_decode( stripslashes( $_POST['_wpcf7cf_hidden_groups'] ) );
			$form_id       = \WPCF7_ContactForm::get_current()->id();
			$group_fields  = $this->check_conditional_fields( $form_id );

			if ( is_null( $value ) && $tag->is_required() ) {
				if ( isset( $group_fields[ $name ] ) && ! in_array( $group_fields[ $name ], $hidden_groups ) ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
				} elseif ( ! array_key_exists( $name, $group_fields ) ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
				}

				return $result;
			}

			return $result;
		}

		return $result;
	}

	public function check_conditional_fields( $form_id ) {

		if ( ! $form_id ) {
			return false;
		}

		// Get visible groups
		$groups = array();

		// Get current form object
		$cf7_post = get_post( $form_id );

		// Extract group shortcode
		$regex = get_shortcode_regex( array( 'group' ) );

		// Match pattern
		preg_match_all( '/' . $regex . '/s', $cf7_post->post_content, $matches );

		if ( array_key_exists( 3, $matches ) ) {
			foreach ( $matches[3] as $index => $group_name ) {
				$name = array_filter( explode( " ", $group_name ) );
				preg_match( '/\[(file_upload|image_upload)[*|\s].*?\]/', $matches[0][ $index ], $file_matches );
				if ( $file_matches ) {
					$field_name            = shortcode_parse_atts( $file_matches[0] );
					$field_name            = preg_replace( '/[^a-zA-Z0-9-_]/', '', $field_name[1] );
					$groups[ $field_name ] = $name[1];
				}
			}
		}

		return $groups;
	}

	public function set_mail_tag( $output, $submission, $as_html ) {
		if ( ! $as_html ) {
			return $output;
		}

		//explode the output by new line
		$files = explode( PHP_EOL, $output );

		if ( empty( $files ) ) {
			return $output;
		}

		ob_start();
		foreach ( $files as $key => $file ) { ?>
            <p style="display: flex; align-items: center; margin-bottom: 5px; padding: 5px; border: 1px solid #ddd;background-color: #FAFAFA;border-radius:3px;">
				<?php $this->file_icon_html( $file ); ?>
                <a rel="noopener noreferrer"
                   style="display:block;width: 100%;text-decoration: none; color: #ff7f50;vertical-align: middle;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;"
                   href="<?php echo esc_url( $file ); ?>"
                   target="_blank"><?php echo esc_html( basename( $file ) ); ?></a>
            </p>
		<?php }

		return ob_get_clean();
	}

	public function file_icon_html( $file ) {
		$src = $file;
		$ext = pathinfo( $src, PATHINFO_EXTENSION );

		$ext_types = wp_get_ext_types();

		$w = '30px';

		if ( ! in_array( $ext, $ext_types['image'], true ) ) {
			$w   = '20px';
			$src = wp_mime_type_icon( wp_ext2type( $ext ) );
		}

		printf( '<span class="file-icon"><img src="%s" style="margin-right:5px;height:auto;width:%2$spx;vertical-align: middle;" width="%2$s" /></span>', esc_url( $src ), $w );
	}

	public function upload_file() {
		$default_error = esc_html__( 'Something went wrong, please try again.', 'essential-addons-for-contact-form-7' );

		$validated_form_field = $this->ajax_validate_form_field();

		if ( empty( $validated_form_field ) ) {
			wp_send_json_error( $default_error, 403 );
		}

		// Make sure we have required values from $_FILES.
		if ( empty( $_FILES['file']['name'] ) ) {
			wp_send_json_error( $default_error, 403 );
		}
		if ( empty( $_FILES['file']['tmp_name'] ) ) {
			wp_send_json_error( $default_error, 403 );
		}

		$error          = empty( $_FILES['file']['error'] ) ? 0 : (int) $_FILES['file']['error'];
		$name           = sanitize_file_name( wp_unslash( $_FILES['file']['name'] ) );
		$file_user_name = sanitize_text_field( wp_unslash( $_FILES['file']['name'] ) );
		$path           = $_FILES['file']['tmp_name'];
		$extension      = strtolower( pathinfo( $name, PATHINFO_EXTENSION ) );


		$errors = [];
		// Validate basic file upload errors.
		$errors = array_merge( $errors, [ $this->validate_basic( $error ) ] );

		//Validate size
		$errors = array_merge( $errors, [ $this->validate_size() ] );

		//Validate extension
		$errors = array_merge( $errors, [ $this->validate_extension( $extension ) ] );

		//Validate wp file type and extension
		$errors = array_merge( $errors, [ $this->validate_wp_filetype_and_ext( $path, $name ) ] );

		$errors = array_filter( $errors );
		$errors = array_unique( $errors );

		if ( count( $errors ) ) {
			wp_send_json_error( implode( ',', $errors ), 400 );
		}

		$tmp_dir  = $this->get_tmp_dir();
		$tmp_name = $this->get_tmp_file_name( $extension );
		$tmp_path = wp_normalize_path( $tmp_dir . '/' . $tmp_name );
		$tmp      = $this->move_file( $path, $tmp_path );

		if ( ! $tmp ) {
			wp_send_json_error( $default_error, 400 );
		}

		$this->clean_tmp_files();

		wp_send_json_success(
			array(
				'name'           => $name,
				'file'           => pathinfo( $tmp, PATHINFO_FILENAME ) . '.' . pathinfo( $tmp, PATHINFO_EXTENSION ),
				'file_user_name' => $file_user_name,
				'url'            => $this->get_tmp_file_url( $tmp_name ),
				'size'           => filesize( $tmp ),
				'type'           => $this->get_mime_type( $tmp ),
			)
		);
	}

	public function get_tmp_file_url( $tmp_name ) {
		$upload_dir = $this->get_upload_dir();
		$upload_url = $upload_dir['url'];
		$upload_url = trailingslashit( $upload_url );
		$upload_url = $upload_url . 'tmp/' . $tmp_name;

		return $upload_url;
	}

	public function get_mime_type( $tmp ) {
		$mime_type = wp_check_filetype( $tmp );
		$mime_type = $mime_type['type'];

		return $mime_type;
	}

	protected function ajax_validate_form_field() {

		if ( empty( $_POST['form_id'] ) || empty( $_POST['field_id'] ) ) {
			return false;
		}

		$form = \WPCF7_ContactForm::get_instance( $_POST['form_id'] );

		if ( empty( $form ) || ! is_object( $form ) ) {
			return false;
		}

		$tag = $form->scan_form_tags( ( [ 'name' => $_POST['field_id'] ] ) );

		if ( empty( $tag ) || ! is_array( $tag ) ) {
			return false;
		}

		$tag = reset( $tag );

		if ( empty( $tag->name ) ) {
			return false;
		}

		$this->tag = $tag;

		return $tag;

	}

	protected function validate_basic( $error ) {

		if ( $error === 0 || $error === 4 ) {
			return false;
		}

		$errors = [
			false,
			esc_html__( 'The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'essential-addons-for-contact-form-7' ),
			esc_html__( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', 'essential-addons-for-contact-form-7' ),
			esc_html__( 'The uploaded file was only partially uploaded.', 'essential-addons-for-contact-form-7' ),
			esc_html__( 'No file was uploaded.', 'essential-addons-for-contact-form-7' ),
			'',
			esc_html__( 'Missing a temporary folder.', 'essential-addons-for-contact-form-7' ),
			esc_html__( 'Failed to write file to disk.', 'essential-addons-for-contact-form-7' ),
			esc_html__( 'File upload stopped by extension.', 'essential-addons-for-contact-form-7' ),
		];

		if ( array_key_exists( $error, $errors ) ) {
			return sprintf( /* translators: %s - error text. */
				esc_html__( 'File upload error. %s', 'essential-addons-for-contact-form-7' ),
				$errors[ $error ]
			);
		}

		return false;
	}

	protected function validate_size( $sizes = null ) {

		if ( $sizes === null && ! empty( $_FILES ) ) {
			$sizes = [];

			foreach ( $_FILES as $file ) {
				$sizes[] = $file['size'];
			}
		}

		if ( ! is_array( $sizes ) ) {
			return false;
		}

		$max_size = min( wp_max_upload_size(), $this->max_file_size() );

		foreach ( $sizes as $size ) {
			if ( $size > $max_size ) {
				return sprintf( /* translators: $s - allowed file size in MB. */
					esc_html__( 'File exceeds max size allowed (%s).', 'essential-addons-for-contact-form-7' ),
					size_format( $max_size )
				);
			}
		}

		return false;
	}

	public function max_file_size() {
		$max_size = $this->tag->get_option( 'max_size', '', true );

		if ( ! empty( $max_size ) ) {

			// Strip any suffix provided (eg M, MB etc), which leaves us with the raw MB value.
			$max_size = preg_replace( '/[^0-9.]/', '', $max_size );

			return eacf7_size_to_bytes( $max_size . 'M' );
		}

		return eacf7_get_max_upload_size( true );
	}

	protected function validate_extension( $ext ) {

		// Make sure file has an extension first.
		if ( empty( $ext ) ) {
			return esc_html__( 'File must have an extension.', 'essential-addons-for-contact-form-7' );
		}

		// Validate extension against all allowed values.
		if ( ! in_array( $ext, $this->get_extensions(), true ) ) {
			return esc_html__( 'File type is not allowed.', 'essential-addons-for-contact-form-7' );
		}

		return false;
	}

	protected function get_extensions() {

		// Allowed file extensions by default.
		$default_extensions = $this->get_default_extensions();

		// Allowed file extensions.
		$extensions = $this->tag->get_option( 'extensions', '', true );
		$extensions = ! empty( $extensions ) ? explode( '|', $extensions ) : $default_extensions;

		$extensions = array_map( function ( $ext ) {
			return strtolower( preg_replace( '/[^A-Za-z0-9_-]/', '', $ext ) );
		}, $extensions );

		$extensions = array_filter( $extensions );

		return array_intersect( $extensions, $default_extensions );
	}

	protected function get_default_extensions() {
		$allowed_mime_types = get_allowed_mime_types();

		$allowed_mime_types = array_keys( $allowed_mime_types );

		$allowed_mime_types = implode( '|', $allowed_mime_types );

		$allowed_mime_types = explode( '|', $allowed_mime_types );

		$allowed_mime_types = array_diff( $allowed_mime_types, $this->denylist );

		return $allowed_mime_types;
	}

	protected function validate_wp_filetype_and_ext( $path, $name ) {

		$wp_filetype = wp_check_filetype_and_ext( $path, $name );

		$ext             = empty( $wp_filetype['ext'] ) ? '' : $wp_filetype['ext'];
		$type            = empty( $wp_filetype['type'] ) ? '' : $wp_filetype['type'];
		$proper_filename = empty( $wp_filetype['proper_filename'] ) ? '' : $wp_filetype['proper_filename'];

		if ( $proper_filename || ! $ext || ! $type ) {
			return esc_html__( 'File type is not allowed.', 'essential-addons-for-contact-form-7' );
		}

		return false;
	}

	public function get_tmp_dir() {

		$upload_dir = $this->get_upload_dir();
		$tmp_root   = $upload_dir['path'] . '/tmp';

		if ( ! file_exists( $tmp_root ) || ! wp_is_writable( $tmp_root ) ) {
			wp_mkdir_p( $tmp_root );
		}

		// Check if the index.html exists in the directory, if not - create it.
		$this->create_index_html_file( $tmp_root );

		return $tmp_root;
	}

	protected function get_tmp_file_name( $extension ) {

		return wp_hash( wp_rand() . microtime() . $this->form_id . $this->field_id ) . '.' . $extension;
	}

	protected function move_file( $path_from, $path_to ) {

		$this->create_dir( dirname( $path_to ) );

		if ( false === move_uploaded_file( $path_from, $path_to ) ) {
			error_log( 'Upload Error, could not upload file' );

			return false;
		}

		$this->set_file_fs_permissions( $path_to );

		return $path_to;
	}

	protected function create_dir( $path ) {

		if ( ! file_exists( $path ) ) {
			wp_mkdir_p( $path );
		}

		// Check if the index.html exists in the path, if not - create it.
		$this->create_index_html_file( $path );

		return $path;
	}

	public function set_file_fs_permissions( $path ) {

		$stat = stat( dirname( $path ) );

		@chmod( $path, $stat['mode'] & 0000666 );
	}

	protected function clean_tmp_files() {

		$files = glob( trailingslashit( $this->get_tmp_dir() ) . '*' );

		if ( ! is_array( $files ) || empty( $files ) ) {
			return;
		}

		$lifespan = (int) apply_filters( 'cf7_field_' . $this->type . '_clean_tmp_files_lifespan', DAY_IN_SECONDS );

		foreach ( $files as $file ) {
			if ( $file === 'index.html' || ! is_file( $file ) ) {
				continue;
			}

			// In some cases filemtime() can return false, in that case - pretend this is a new file and do nothing.
			$modified = (int) filemtime( $file );

			if ( empty( $modified ) ) {
				$modified = time();
			}

			if ( ( time() - $modified ) >= $lifespan ) {
				@unlink( $file );
			}
		}
	}

	public function upload_complete( $posted_data ) {
		// Submission instance from CF7
		$submission = \WPCF7_Submission::get_instance();

		$this->submission = $submission;

		// Make sure we have the data
		if ( ! $posted_data ) {
			$posted_data = $submission->get_posted_data();
		}


		// Scan and get all form tags from cf7 generator
		$form = $submission->get_contact_form();

		$this->form_id = $form->id();
		$tags          = $form->scan_form_tags();

		if ( ! empty( $tags ) ) {
			foreach ( $tags as $field ) {

				if ( empty( $field->basetype )
				     || ! in_array( $field->basetype, array( 'file_upload', 'image_upload' ) ) ) {
					continue;
				}


				$this->tag = $field;

				// Get the field name
				$this->field_id = $field->name;

				// Get the field value
				$field_value = isset( $posted_data[ $this->field_id ] ) ? $posted_data[ $this->field_id ] : '';

				$files = $this->sanitize_files_input( $field_value );

				if ( empty( $files ) ) {
					continue;
				}

				$this->create_upload_dir_htaccess_file();

				$upload_dir = $this->get_upload_dir();

				if ( empty( $upload_dir['error'] ) ) {
					$this->create_index_html_file( $upload_dir['path'] );
				}

				$data = [];

				foreach ( $files as $file ) {
					$data[] = $this->process_file( $file );
				}

				$data = array_filter( $data );

				$values = wp_list_pluck( $data, 'value' );

				$value = implode( "\n", $values );

				$posted_data[ $this->field_id ] = $value;

			}
		}

		return $posted_data;
	}

	public function process_file( $file ) {

		$file['tmp_name'] = trailingslashit( $this->get_tmp_dir() ) . $file['file'];
		$file['type']     = 'application/octet-stream';

		if ( is_file( $file['tmp_name'] ) ) {
			$filetype     = wp_check_filetype( $file['tmp_name'] );
			$file['type'] = $filetype['type'];
			$file['size'] = filesize( $file['tmp_name'] );
		}

		$file_name     = sanitize_file_name( $file['name'] );
		$file_ext      = pathinfo( $file_name, PATHINFO_EXTENSION );
		$file_base     = $this->get_file_basename( $file_name, $file_ext );
		$file_name_new = sprintf( '%s-%s.%s', $file_base, wp_hash( wp_rand() . microtime() . $this->form_id . $this->field_id ), strtolower( $file_ext ) );

		$file_details = [
			'file_name'     => $file_name,
			'file_name_new' => $file_name_new,
			'file_ext'      => $file_ext,
		];

		$is_media_integrated = in_array( 'media_library', $this->tag->options );

		if ( $is_media_integrated ) {
			$uploaded_file = $this->process_media_storage( $file_details, $file );
		} else {
			$uploaded_file = $this->process_cf7_extended_storage( $file_details, $file );
		}

		if ( empty( $uploaded_file ) ) {
			return [];
		}

		$uploaded_file['file']           = $file['file'];
		$uploaded_file['file_user_name'] = $file['file_user_name'];
		$uploaded_file['type']           = $file['type'];

		return $this->generate_file_data( $uploaded_file );
	}

	private function process_media_storage( $file_details, $file ) {

		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$file_args = [
			'error'    => '',
			'tmp_name' => $file['tmp_name'],
			'name'     => $file_details['file_name_new'],
			'type'     => $file['type'],
			'size'     => $file['size'],
		];

		$upload = wp_handle_sideload( $file_args, [ 'test_form' => false ] );

		if ( empty( $upload['file'] ) ) {
			return [];
		}

		$attachment_id = $this->insert_attachment( $file, $upload['file'] );

		if ( $attachment_id === 0 ) {
			return [];
		}

		$file_details['attachment_id'] = $attachment_id;
		$file_details['file_url']      = wp_get_attachment_url( $attachment_id );
		$file_details['file_name_new'] = wp_basename( $file_details['file_url'] );
		$file_details['upload_path']   = wp_normalize_path( trailingslashit( dirname( get_attached_file( $attachment_id ) ) ) );

		return $file_details;
	}

	private function insert_attachment( $file, $upload_file ) {

		$attachment_id = wp_insert_attachment(
			[
				'post_status'    => 'publish',
				'post_mime_type' => $file['type'],
			],
			$upload_file
		);

		if ( empty( $attachment_id ) || is_wp_error( $attachment_id ) ) {
			error_log( "Upload Error, attachment wasn't created" );

			return 0;
		}

		wp_update_attachment_metadata(
			$attachment_id,
			wp_generate_attachment_metadata( $attachment_id, $upload_file )
		);

		return $attachment_id;
	}

	private function process_cf7_extended_storage( $file_details, $file ) {

		$form_id          = $this->form_id;
		$upload_dir       = $this->get_upload_dir();
		$upload_path      = $upload_dir['path'];
		$form_directory   = $this->get_form_directory( $form_id, $this->submission->get_meta( 'timestamp' ) );
		$upload_path_form = $this->get_form_upload_path( $upload_path, $form_directory );
		$file_new         = trailingslashit( $upload_path_form ) . $file_details['file_name_new'];
		$file_url         = trailingslashit( $upload_dir['url'] ) . trailingslashit( $form_directory ) . $file_details['file_name_new'];

		$this->create_upload_dir_htaccess_file();
		$this->create_index_html_file( $upload_path );
		$this->create_index_html_file( $upload_path_form );

		$move_new_file = @rename( $file['tmp_name'], $file_new );

		if ( $move_new_file === false ) {
			error_log( 'Upload Error, could not upload file' );

			return [];
		}

		$this->set_file_fs_permissions( $file_new );

		$file_details['attachment_id'] = '0';
		$file_details['upload_path']   = $upload_path_form;
		$file_details['file_url']      = $file_url;

		return $file_details;
	}

	public function get_form_directory( $form_id, $date_created ) {

		return absint( $form_id ) . '-' . md5( $form_id . $date_created );
	}

	private function get_form_upload_path( $upload_path, $form_directory ) {

		$upload_path_form = wp_normalize_path( trailingslashit( $upload_path ) . $form_directory );

		if ( ! file_exists( $upload_path_form ) ) {
			wp_mkdir_p( $upload_path_form );
		}

		return $upload_path_form;
	}

	private function get_file_basename( $file_name, $file_ext ) {

		return mb_substr( wp_basename( $file_name, '.' . $file_ext ), 0, 64, 'UTF-8' );
	}

	private function sanitize_files_input( $value ) {
		$json_value = sanitize_text_field( wp_unslash( $value ) );
		$files      = json_decode( $json_value, true );

		if ( empty( $files ) || ! is_array( $files ) ) {
			return [];
		}

		return array_filter( array_map( [ $this, 'sanitize_file' ], $files ) );
	}

	private function sanitize_file( $file ) {

		if ( empty( $file['file'] ) || empty( $file['name'] ) ) {
			return [];
		}

		$sanitized_file = [];
		$rules          = [
			'name'           => 'sanitize_file_name',
			'file'           => 'sanitize_file_name',
			'url'            => 'esc_url_raw',
			'size'           => 'absint',
			'type'           => 'sanitize_text_field',
			'file_user_name' => 'sanitize_text_field',
		];

		foreach ( $rules as $rule => $callback ) {
			$file_attribute          = isset( $file[ $rule ] ) ? $file[ $rule ] : '';
			$sanitized_file[ $rule ] = $callback( $file_attribute );
		}

		return $sanitized_file;
	}

	protected function generate_file_data( $file ) {

		$ext = explode( '.', $file['file'] );
		$ext = end( $ext );

		return [
			'name'           => sanitize_text_field( $file['file_name'] ),
			'value'          => esc_url_raw( $file['file_url'] ),
			'file'           => $file['file_name_new'],
			'file_original'  => $file['file_name'],
			'file_user_name' => sanitize_text_field( $file['file_user_name'] ),
			'ext'            => $ext,
			'attachment_id'  => isset( $file['attachment_id'] ) ? absint( $file['attachment_id'] ) : 0,
			'id'             => $this->field_id,
			'type'           => $file['type'],
		];
	}

	private function create_upload_dir_htaccess_file() {

		if ( ! apply_filters( 'cf7_extended_create_upload_dir_htaccess_file', true ) ) {
			return false;
		}

		$upload_dir = $this->get_upload_dir();

		if ( ! empty( $upload_dir['error'] ) ) {
			return false;
		}

		$htaccess_file = wp_normalize_path( trailingslashit( $upload_dir['path'] ) . '.htaccess' );
		$cache_key     = 'cf7_extended_htaccess_file';

		if ( is_file( $htaccess_file ) ) {
			$cached_stat = get_transient( $cache_key );
			$stat        = array_intersect_key(
				stat( $htaccess_file ),
				[
					'size'  => 0,
					'mtime' => 0,
					'ctime' => 0,
				]
			);

			if ( $cached_stat === $stat ) {
				return true;
			}

			@unlink( $htaccess_file );
		}

		if ( ! function_exists( 'insert_with_markers' ) ) {
			require_once ABSPATH . 'wp-admin/includes/misc.php';
		}

		$contents = apply_filters(
			'cf7_extended_create_upload_dir_htaccess_file_content',
			'# Disable PHP and Python scripts parsing.
<Files *>
  SetHandler none
  SetHandler default-handler
  RemoveHandler .cgi .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo
  RemoveType .cgi .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo
</Files>
<IfModule mod_php5.c>
  php_flag engine off
</IfModule>
<IfModule mod_php7.c>
  php_flag engine off
</IfModule>
<IfModule mod_php8.c>
  php_flag engine off
</IfModule>
<IfModule headers_module>
  Header set X-Robots-Tag "noindex"
</IfModule>'
		);

		$created = insert_with_markers( $htaccess_file, 'CF7_Extended', $contents );

		if ( $created ) {
			clearstatcache( true, $htaccess_file );
			$stat = array_intersect_key(
				stat( $htaccess_file ),
				[
					'size'  => 0,
					'mtime' => 0,
					'ctime' => 0,
				]
			);

			set_transient( $cache_key, $stat );
		}

		return $created;
	}

	private function create_index_html_file( $path ) {

		if ( ! is_dir( $path ) || is_link( $path ) ) {
			return false;
		}

		$index_file = wp_normalize_path( trailingslashit( $path ) . 'index.html' );

		// Do nothing if index.html exists in the directory.
		if ( file_exists( $index_file ) ) {
			return false;
		}

		// Create empty index.html.
		return file_put_contents( $index_file, '' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}

	private function get_upload_dir() {

		$upload_dir = wp_upload_dir();

		if ( ! empty( $upload_dir['error'] ) ) {
			return [ 'error' => $upload_dir['error'] ];
		}

		$basedir = wp_is_stream( $upload_dir['basedir'] ) ? $upload_dir['basedir'] : realpath( $upload_dir['basedir'] );

		$cf7_extended_upload_root = trailingslashit( $basedir ) . 'eacf7';


		$custom_uploads_root = apply_filters( 'cf7_extended_upload_root', $cf7_extended_upload_root );

		if ( is_dir( $custom_uploads_root ) && wp_is_writable( $custom_uploads_root ) ) {
			$cf7_extended_upload_root = wp_is_stream( $custom_uploads_root )
				? $custom_uploads_root
				: realpath( $custom_uploads_root );
		}

		return [
			'path'  => $cf7_extended_upload_root,
			'url'   => trailingslashit( $upload_dir['baseurl'] ) . 'eacf7',
			'error' => false,
		];
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}

Uploader::instance();