<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Post_Submission {
    /**
     * @var null
     */
    protected static $instance = null;

    public function __construct() {
        add_action( 'wpcf7_admin_init', [ $this, 'add_tag_generator' ], 99 );
        add_action( 'wpcf7_init', [ $this, 'add_data_handler' ] );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_filter( 'wpcf7_validate_post_title', [ $this, 'validate' ], 10, 2 );
        add_filter( 'wpcf7_validate_post_title*', [ $this, 'validate' ], 10, 2 );
        add_filter( 'wpcf7_validate_post_excerpt', [ $this, 'validate' ], 10, 2 );
        add_filter( 'wpcf7_validate_post_excerpt*', [ $this, 'validate' ], 10, 2 );
        add_filter( 'wpcf7_validate_post_content', [ $this, 'validate' ], 10, 2 );
        add_filter( 'wpcf7_validate_post_content*', [ $this, 'validate' ], 10, 2 );
        add_filter( 'wpcf7_validate_post_thumbnail', [ $this, 'validate' ], 10, 2 );
        add_filter( 'wpcf7_validate_post_thumbnail*', [ $this, 'validate' ], 10, 2 );
        add_filter( 'wpcf7_validate_post_taxonomies', [ $this, 'validate' ], 10, 2 );
        add_filter( 'wpcf7_validate_post_taxonomies*', [ $this, 'validate' ], 10, 2 );

        add_action( 'wpcf7_before_send_mail', [ $this, 'handle_insert_post' ] );

        add_action( 'wpcf7_after_save', array( $this, 'post_submission_save_data' ) );

        // add post-submission data to localize script
        add_filter( 'eacf7_localize_data', array( $this, 'add_localize_data' ) );
    }

    public function add_localize_data( $data ) {

        if ( eacf7_is_editor_page() ) {
            $data['postSubmissionData'] = $this->get_post_submission_data();

            $data['getPostType'] = get_post_types( array( 'public' => true ) );
        }

        return $data;
    }

    function get_post_submission_data( $form_id = null ) {

        if ( ! $form_id ) {
            $form_id = eacf7_get_current_form_id();
        }

        $data = get_post_meta( $form_id, 'eacf7_post_submission_data', true );

        return ! empty( $data ) ? $data : [];

    }

    public function add_tag_generator() {
        $tags = [
                'post_title'      => __( 'Post Title', 'essential-addons-for-contact-form-7' ),
                'post_excerpt'    => __( 'Post Excerpt', 'essential-addons-for-contact-form-7' ),
                'post_content'    => __( 'Post Content', 'essential-addons-for-contact-form-7' ),
                'post_thumbnail'  => __( 'Post Thumbnail', 'essential-addons-for-contact-form-7' ),
                'post_taxonomies' => __( 'Post Taxonomies', 'essential-addons-for-contact-form-7' ),
        ];

        $tag_generator = \WPCF7_TagGenerator::get_instance();

        if ( version_compare( WPCF7_VERSION, '6.0', '>=' ) ) {
            foreach ( $tags as $type => $label ) {
                $tag_generator->add(
                        $type,
                        $label,
                        [ $this, 'tag_generator_body_v6' ],
                        [ 'version' => 2 ]
                );
            }
        } else {
            foreach ( $tags as $type => $label ) {
                $tag_generator->add( $type, $label, [ $this, 'tag_generator_body' ] );
            }
        }
    }

    /**
     * Tag Generator Body v6
     * @since 1.0.1
     */
    public function tag_generator_body_v6( $contact_form, $options ) {
        $tgg = new \WPCF7_TagGeneratorGenerator( $options['content'] );
        ?>
        <header class="description-box">
            <div class="eacf7-notice eacf7-notice-info">
                <p>
                    <span class="dashicons dashicons-info-outline"></span>
                    <?php
                    echo sprintf(
                            __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Post Submission</a>', 'essential-addons-for-contact-form-7' ),
                            'https://softlabbd.com/docs/'
                    );
                    ?>
                </p>
            </div>
        </header>

        <div class="control-box">
            <?php
            $tgg->print( 'field_type', array(
                    'with_required'  => true,
                    'select_options' => array(
                            $options['id'] => $options['title'],
                    ),
            ) );

            $tgg->print( 'field_name' );
            ?>

            <?php if ( $options['id'] === 'post_excerpt' || $options['id'] === 'post_content' ) { ?>
                <!-- Enable TinyMCE Editor -->
                <fieldset>
                    <legend><?php echo esc_html__( 'TinyMCE Editor', 'essential-addons-for-contact-form-7' ); ?></legend>
                    <div>
                        <label>
                            <input type="checkbox" data-tag-part="option" data-tag-option="editor" name="editor"
                                   value="1"/>
                            <p class="description"><?php echo esc_html__( 'Enable editor to your text field look change to TinyMCE Editor.', 'essential-addons-for-contact-form-7' ); ?></p>
                        </label>
                    </div>
                </fieldset>
            <?php } ?>

            <?php
            if ( $options['id'] === 'post_taxonomies' ) {
                $taxonomies = get_taxonomies();
                ?>
                <fieldset>
                    <legend><?php echo esc_html( __( "Taxonomy Type", 'essential-addons-for-contact-form-7' ) ); ?></legend>
                    <div>
                        <label>
                            <select class="select-post-taxonomies"
                                    id="<?php echo esc_attr( $options['content'] . '-type' ); ?>">
                                <option><?php echo esc_html__( 'Select Taxonomy', 'essential-addons-for-contact-form-7' ); ?></option>
                                <?php
                                if ( ! empty( $taxonomies ) ) {
                                    foreach ( $taxonomies as $taxonomy ) {
                                        echo sprintf( '<option value="%s">%s</option>', $taxonomy, $taxonomy );
                                    }
                                }
                                ?>
                            </select>
                            <input type="hidden" data-tag-part="option" data-tag-option="type:" name="type"
                                   class="oneline option">
                        </label>
                    </div>
                </fieldset>
                <?php
            }
            ?>
            <?php
            if ( $options['id'] === 'post_thumbnail' ) {
                ?>

                <fieldset>
                    <legend><?php echo esc_html__( 'File size limit (bytes)', 'essential-addons-for-contact-form-7' ); ?></legend>
                    <p class="oneline">
                        <label>
                            <input type="text" data-tag-part="option" data-tag-option="limit:" name="limit"/>
                        </label>
                    </p>
                </fieldset>

                <fieldset>
                    <legend><?php echo esc_html__( 'Acceptable file types', 'essential-addons-for-contact-form-7' ); ?></legend>
                    <p class="oneline">
                        <label>
                            <input type="text" data-tag-part="option" data-tag-option="filetypes:" name="filetypes"/>
                        </label>
                    </p>
                </fieldset>
            <?php } ?>
            <?php

            $tgg->print( 'class_attr' );

            $tgg->print( 'id_attr' );
            ?>
        </div>
        <footer class="insert-box">
            <?php
            $tgg->print( 'insert_box_content' );

            $tgg->print( 'mail_tag_tip' );
            ?>
        </footer>
        <?php
    }

    public function tag_generator_body( $contact_form, $args = '' ) {
        $args = wp_parse_args( $args, [] );
        $type = isset( $args['id'] ) ? $args['id'] : '';
        ?>
        <div class="control-box">

            <fieldset>
                <div class="eacf7-notice eacf7-notice-info">
                    <p>
                        <span class="dashicons dashicons-info-outline"></span>
                        <?php
                        echo sprintf(
                                __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Post Submission</a>', 'essential-addons-for-contact-form-7' ),
                                'https://softlabbd.com/docs-category/contact-form-7-extended/'
                        );
                        ?>
                    </p>
                </div>

                <table class="form-table">
                    <tbody>

                    <!-- Field Type -->
                    <tr>
                        <th scope="row"><?php echo esc_html__( 'Field type', 'essential-addons-for-contact-form-7' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php echo esc_html__( 'Field type', 'essential-addons-for-contact-form-7' ); ?></legend>
                                <label>
                                    <input type="checkbox"
                                           name="required"/> <?php echo esc_html__( 'Required field', 'essential-addons-for-contact-form-7' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <!-- Name -->
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>">
                                <?php echo esc_html__( 'Name', 'essential-addons-for-contact-form-7' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline"
                                   id="<?php echo esc_attr( $args['content'] . '-name' ); ?>"/>
                        </td>
                    </tr>

                    <?php
                    if ( 'post_taxonomies' == $type ) {
                        $taxonomies = get_taxonomies();
                        ?>
                        <tr>
                            <th scope="row"><label
                                        for="<?php echo esc_attr( $args['content'] . '-type' ); ?>"><?php echo esc_html( __( "Taxonomy Type", 'essential-addons-for-contact-form-7' ) ); ?></label>
                            </th>
                            <td>
                                <select class="select-post-taxonomies"
                                        id="<?php echo esc_attr( $args['content'] . '-type' ); ?>">
                                    <option><?php echo esc_html__( 'Select Taxonomy', 'essential-addons-for-contact-form-7' ); ?></option>
                                    <?php
                                    if ( ! empty( $taxonomies ) ) {
                                        foreach ( $taxonomies as $taxonomy ) {
                                            echo sprintf( '<option value="%s">%s</option>', $taxonomy, $taxonomy );
                                        }
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="type" class="oneline option">
                        </tr>
                        <?php
                    }
                    ?>

                    <?php
                    if ( 'post_thumbnail' == $type ) {
                        ?>
                        <tr>
                            <th scope="row"><label
                                        for="<?php echo esc_attr( $args['content'] . '-limit' ); ?>"><?php echo esc_html( __( "File size limit (bytes)", 'essential-addons-for-contact-form-7' ) ); ?></label>
                            </th>
                            <td><input type="text" name="limit" class="filesize oneline option"
                                       id="<?php echo esc_attr( $args['content'] . '-limit' ); ?>"/></td>
                        </tr>

                        <tr>
                            <th scope="row"><label
                                        for="<?php echo esc_attr( $args['content'] . '-filetypes' ); ?>"><?php echo esc_html( __( 'Acceptable file types', 'essential-addons-for-contact-form-7' ) ); ?></label>
                            </th>
                            <td><input type="text" name="filetypes" class="filetype oneline option"
                                       id="<?php echo esc_attr( $args['content'] . '-filetypes' ); ?>"/></td>
                        </tr>
                    <?php } ?>

                    <?php
                    if ( $type !== 'post_excerpt' && $type !== 'post_content' ) {
                        ?>
                        <!-- ID -->
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>">
                                    <?php echo esc_html__( 'ID', 'essential-addons-for-contact-form-7' ); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="id" class="idvalue oneline option"
                                       id="<?php echo esc_attr( $args['content'] . '-id' ); ?>"/>
                            </td>
                        </tr>
                    <?php } ?>

                    <!-- Class -->
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>">
                                <?php echo esc_html__( 'Class', 'essential-addons-for-contact-form-7' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" name="class" class="classvalue oneline option"
                                   id="<?php echo esc_attr( $args['content'] . '-class' ); ?>"/>
                        </td>
                    </tr>

                    <?php
                    if ( 'post_excerpt' == $type || 'post_content' == $type ) {
                        ?>
                        <!-- Enable TinyMCE Editor -->
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr( $args['content'] . '-editor' ); ?>">
                                    <?php echo esc_html__( 'TinyMCE Editor', 'essential-addons-for-contact-form-7' ); ?>
                                </label>
                            </th>
                            <td>
                                <input type="checkbox" name="editor" class="option"
                                       id="<?php echo esc_attr( $args['content'] . '-editor' ); ?>" value="1"/>
                                <p class="description"><?php echo esc_html__( 'Enable editor to your text field look change to TinyMCE Editor.', 'essential-addons-for-contact-form-7' ); ?></p>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="<?php echo esc_attr( $type ); ?>" class="tag code" readonly="readonly"
                   onfocus="this.select()"/>

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag"
                       value="<?php echo esc_attr__( 'Insert Tag', 'essential-addons-for-contact-form-7' ); ?>"/>
            </div>

            <br class="clear"/>

            <p class="description mail-tag">
                <label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>">
                    <?php printf( 'To display the %s in the email, insert the mail-tag (%s) in the Mail tab.', $type, '<strong><span class="mail-tag"></span></strong>' ); ?>
                    <input type="text" class="mail-tag code eacf7-hidden" readonly="readonly"
                           id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"/>
                </label>
            </p>
        </div>
        <?php
    }

    /**
     * Add shortcode handler to CF7.
     */
    public function add_data_handler() {
        if ( function_exists( 'wpcf7_add_form_tag' ) ) {
            $tags = [
                    'post_title'      => 'render_post_title',
                    'post_excerpt'    => 'render_post_excerpt',
                    'post_content'    => 'render_post_content',
                    'post_thumbnail'  => 'render_post_thumbnail',
                    'post_taxonomies' => 'render_post_taxonomy',
            ];

            foreach ( $tags as $tag => $callback ) {
                $attributes = [ 'name-attr' => true ];
                if ( $tag === 'post_thumbnail' ) {
                    $attributes['file-uploading'] = true;
                }
                if ( $tag === 'post_taxonomies' ) {
                    $attributes['multiple'] = 'multiple';
                }
                wpcf7_add_form_tag( [ $tag, $tag . '*' ], [ $this, $callback ], $attributes );
            }
        }
    }

    /**
     * Render post title
     * @since 1.0.0
     * @author monzuralam
     */
    public function render_post_title( $tag ) {

        $tag = new \WPCF7_FormTag( $tag );

        // check and make sure tag name is not empty
        if ( empty( $tag->name ) ) {
            return '';
        }

        // Validate our fields
        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = $tag->get_class_option( 'wpcf7-form-control eacf7-post-title' );

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = [
                'class'         => $class,
                'id'            => $tag->get_id_option(),
                'name'          => $tag->name,
                'aria-invalid'  => $validation_error ? 'true' : 'false',
                'aria-required' => $tag->is_required() ? 'true' : 'false',
        ];


        $atts = wpcf7_format_atts( $atts );

        ob_start();
        ?>

        <span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr( $tag->name ); ?>">

			<input type="text" <?php echo $atts; ?> />

			<?php echo $validation_error; ?>
		</span>
        <?php
        return ob_get_clean();
    }

    /**
     * Render post excerpt
     * @since 1.0.0
     * @author monzuralam
     */
    public function render_post_excerpt( $tag ) {

        $tag = new \WPCF7_FormTag( $tag );

        // check and make sure tag name is not empty
        if ( empty( $tag->name ) ) {
            return '';
        }

        // Validate our fields
        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = $tag->get_class_option( 'wpcf7-form-control eacf7-post-excerpt' );

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }

        // check editor enable
        $has_editor = $tag->has_option( 'editor', '', true );

        $editor_id = (string) $has_editor ? 'eacf7-post-excerpt' : $tag->get_id_option();

        $atts = [
                'class'         => $class,
                'id'            => $editor_id,
                'name'          => $tag->name,
                'aria-invalid'  => $validation_error ? 'true' : 'false',
                'aria-required' => $tag->is_required() ? 'true' : 'false',
        ];

        if ( $has_editor ) {
            $atts['class'] .= ' init-tinymce-editor';
        }

        $atts = wpcf7_format_atts( $atts );

        ob_start();
        ?>

        <span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr( $tag->name ); ?>">

			<input type="text" <?php echo $atts; ?> />

			<?php echo $validation_error; ?>
		</span>
        <?php
        return ob_get_clean();
    }

    /**
     * Render post content
     * @since 1.0.0
     * @author monzuralam
     */
    public function render_post_content( $tag ) {

        $tag = new \WPCF7_FormTag( $tag );

        // check and make sure tag name is not empty
        if ( empty( $tag->name ) ) {
            return '';
        }

        // Validate our fields
        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = $tag->get_class_option( 'wpcf7-form-control eacf7-post-content' );

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }

        // check editor is enable
        $has_editor = $tag->has_option( 'editor', '', true );

        $editor_id = (string) $has_editor ? 'eacf7-post-content' : $tag->get_id_option();

        $atts = [
                'class'         => $class,
                'id'            => $editor_id,
                'name'          => $tag->name,
                'aria-invalid'  => $validation_error ? 'true' : 'false',
                'aria-required' => $tag->is_required() ? 'true' : 'false',
        ];

        if ( $has_editor ) {
            $atts['class'] .= ' init-tinymce-editor';
        }

        $atts = wpcf7_format_atts( $atts );

        ob_start();
        ?>

        <span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr( $tag->name ); ?>">

			<textarea <?php echo $atts; ?>></textarea>

			<?php echo $validation_error; ?>
		</span>
        <?php
        return ob_get_clean();
    }

    /**
     * Render post thumbnail
     * @since 1.0.0
     * @author monzuralam
     */
    public function render_post_thumbnail( $tag ) {

        $tag = new \WPCF7_FormTag( $tag );

        // check and make sure tag name is not empty
        if ( empty( $tag->name ) ) {
            return '';
        }

        // Validate our fields
        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = $tag->get_class_option( 'wpcf7-form-control eacf7-post-thumbnail' );

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = [
                'class'         => $class,
                'id'            => $tag->get_id_option(),
                'name'          => $tag->name,
                'aria-invalid'  => $validation_error ? 'true' : 'false',
                'aria-required' => $tag->is_required() ? 'true' : 'false',
        ];


        $atts = wpcf7_format_atts( $atts );

        ob_start();
        ?>

        <span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr( $tag->name ); ?>">

			<input type="file" <?php echo $atts; ?>>

			<?php echo $validation_error; ?>
		</span>
        <?php
        return ob_get_clean();
    }

    /**
     * Render post taxonomy
     * @since 1.0.0
     * @author monzuralam
     */
    public function render_post_taxonomy( $tag ) {

        $tag = new \WPCF7_FormTag( $tag );

        $wpcf7  = \WPCF7_ContactForm::get_current();
        $formid = $wpcf7->id();

        $data   = get_post_meta( $formid, 'eacf7_post_submission_data', true );
        $status = isset( $data['postSubmission'] ) ? $data['postSubmission'] : false;

        if ( ! $status ) {
            return;
        }

        // check and make sure tag name is not empty
        if ( empty( $tag->name ) ) {
            return '';
        }

        // Validate our fields
        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = $tag->get_class_option( 'wpcf7-form-control eacf7-post-submission post-submission-taxonomies' );

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = [
                'class'         => $class,
                'id'            => $tag->get_id_option(),
                'name'          => $tag->name . '[]',
                'aria-invalid'  => $validation_error ? 'true' : 'false',
                'aria-required' => $tag->is_required() ? 'true' : 'false',
                'multiple'      => true,
        ];

        // get taxonomy
        $taxonomy = (string) ! empty( $tag->get_option( 'type', '', true ) ) ? $tag->get_option( 'type', '', true ) : 'category';

        // check is taxonomy or not
        $is_taxonomy = (bool) get_taxonomy( $taxonomy ) ? get_taxonomy( $taxonomy )->hierarchical : false;

        ( ! $is_taxonomy ) ? $atts['class'] .= ' post-submission-tags' : '';

        $atts = wpcf7_format_atts( $atts );

        if ( ! empty( $taxonomy ) ) {
            $args = array(
                    'taxonomy'   => $taxonomy,
                    'hide_empty' => false,
            );

            $categories = get_terms( $args );
        }

        ob_start();
        ?>

        <span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr( $tag->name ); ?>">

			<select <?php echo $atts; ?>>
				<option><?php echo esc_html__( 'Select', 'essential-addons-for-contact-form-7' ); ?></option>
				<?php
                foreach ( $categories as $category ) {
                    $id = $category->term_id;
                    echo sprintf( '<option value="%s">%s</option>', $id, $category->name );
                }
                ?>
			</select>

			<?php echo $validation_error; ?>
		</span>
        <?php
        return ob_get_clean();
    }

    public function enqueue_scripts() {
        wp_enqueue_style( 'eacf7-select2' );
        wp_enqueue_style( 'eacf7-frontend' );

        wp_enqueue_editor();
        wp_enqueue_script( 'eacf7-select2' );
        wp_enqueue_script( 'eacf7-frontend' );
    }

    public function validate( $result, $tag ) {
        $tag   = new \WPCF7_FormTag( $tag );
        $name  = $tag->name;
        $type  = $tag->basetype;
        $value = isset( $_POST[ $name ] ) ? $_POST[ $name ] : '';

        $file = isset( $_FILES[ $name ] ) ? $_FILES[ $name ] : null;

        // Check if the file is uploaded and not empty
        if ( $file ) {
            if ( $file['size'] == 0 ) {
                $result->invalidate( $tag, esc_html__( 'Please upload image.', 'essential-addons-for-contact-form-7' ) );
            } else {
                $file_type     = wp_check_filetype( $file['name'] );
                $allowed_types = array( 'jpg', 'jpeg', 'png', 'gif' );

                if ( ! in_array( $file_type['ext'], $allowed_types ) ) {
                    $result->invalidate( $tag, esc_html__( 'Please upload a valid image', 'essential-addons-for-contact-form-7' ) );
                }
            }
        }

        if ( $tag->is_required() && empty( $value ) ) {
            if ( 'post_thumbnail' !== $type ) {
                $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
            }
        }

        return $result;
    }

    public function handle_insert_post( $contact_form ) {
        $submission = \WPCF7_Submission::get_instance();
        $formid     = $contact_form->id();

        $data                  = get_post_meta( $formid, 'eacf7_post_submission_data', true );
        $status                = isset( $data['postSubmission'] ) ? $data['postSubmission'] : false;
        $post_type             = isset( $data['postType'] ) ? $data['postType'] : 'post';
        $post_status           = isset( $data['postStatus'] ) ? $data['postStatus'] : 'pending';
        $comment_status        = isset( $data['commentStatus'] ) ? $data['commentStatus'] : 'closed';
        $post_field_map_status = isset( $data['postFieldMapping'] ) ? $data['postFieldMapping'] : false;
        $post_title            = isset( $data['postTitle'] ) ? $data['postTitle'] : '';
        $post_content          = isset( $data['postContent'] ) ? $data['postContent'] : '';
        $post_excerpt          = isset( $data['postExcerpt'] ) ? $data['postExcerpt'] : '';
        $metafields            = isset( $data['metafields'] ) ? $data['metafields'] : array();

        if ( $submission && $status ) {

            $posted_data = $submission->get_posted_data();

            $args = array(
                    'post_type'   => $post_type,
                    'post_status' => $post_status,
            );

            $taxonomy_values = array();
            $meta_keys       = array();
            $meta_values     = array();
            $meta            = array();

            foreach ( $metafields as $metafield ) {
                array_push( $meta_keys, $metafield['key'] );
                array_push( $meta_values, $metafield['field'] );
            }

            foreach ( $posted_data as $key => $value ) {
                if ( $post_field_map_status ) {
                    // Replace 'post_title' with the value of $post_title in the regex pattern
                    if ( preg_match( '/\b' . preg_quote( $post_title, '/' ) . '\b/', $key ) ) {
                        $args['post_title'] = $value;
                    }
                    // Replace 'post_content' with the value of $post_content in the regex pattern
                    if ( preg_match( '/\b' . preg_quote( $post_content, '/' ) . '\b/', $key ) ) {
                        $args['post_content'] = $value;
                    }
                    // Replace 'post_excerpt' with the value of $post_excerpt in the regex pattern
                    if ( preg_match( '/\b' . preg_quote( $post_excerpt, '/' ) . '\b/', $key ) ) {
                        $args['post_excerpt'] = $value;
                    }
                } else {
                    if ( preg_match( '/\bpost_title-\d+\b/', $key ) ) {
                        $args['post_title'] = $value;
                    }
                    if ( preg_match( '/\bpost_excerpt-\d+\b/', $key ) ) {
                        $args['post_excerpt'] = $value;
                    }
                    if ( preg_match( '/\bpost_content-\d+\b/', $key ) ) {
                        $args['post_content'] = $value;
                    }
                }

                if ( preg_match( '/\bpost_taxonomies-\d+\b/', $key ) ) {
                    $taxonomy_values[] = $value;
                }
                // Check if the key is in meta_keys
                if ( in_array( $key, $meta_values ) ) {
                    $meta[] = $value;
                }
            }

            // Combine $meta_keys and $field into $meta
            if ( ! empty( $meta_keys ) ) {
                foreach ( $meta_keys as $index => $meta_key ) {
                    if ( isset( $meta_values[ $index ] ) ) {
                        $meta[ $meta_key ] = $meta[ $index ];
                    }
                }
            }

            // Remove numeric indices if they exist
            foreach ( $meta as $key => $value ) {
                if ( is_numeric( $key ) ) {
                    unset( $meta[ $key ] );
                }
            }

            // added meta
            $args['meta_input'] = $meta;

            $uploaded_files = $submission->uploaded_files();

            foreach ( $uploaded_files as $key => $value ) {
                if ( preg_match( '/\bpost_thumbnail-\d+\b/', $key ) ) {
                    $uploaded_file_url = $value[0];
                }
            }

            // Comment status
            $args['comment_status'] = $comment_status;

            // Insert the post
            $post_id = wp_insert_post( $args );

            // Taxonomy values check & set
            if ( ! empty( $taxonomy_values ) ) {
                foreach ( $taxonomy_values as $taxonomy_value ) {
                    foreach ( (array) $taxonomy_value as $value ) {
                        $term = get_term( $value );
                        if ( $term && ! is_wp_error( $term ) ) {
                            $taxonomy = $term->taxonomy;
                            $term_id  = intval( $value );

                            // Set terms to post
                            wp_set_post_terms( $post_id, array( $term_id ), $taxonomy, true );
                        }
                    }
                }
            }

            /**
             * Check
             */
            if ( isset( $uploaded_file_url ) && $post_id ) {

                // Fetch the file content
                $img = file_get_contents( $uploaded_file_url );

                // Create a finfo instance
                $finfo = new \finfo( FILEINFO_MIME_TYPE );

                // Detect MIME type from the file content
                $mime_type = $finfo->buffer( $img );

                // file name
                $file_name = basename( parse_url( $uploaded_file_url )['path'] );

                // Optionally get the file extension from MIME type
                $file_extensions = array(
                        'jpg'  => 'image/jpg',
                        'jpeg' => 'image/jpeg',
                        'png'  => 'image/png',
                        'gif'  => 'image/gif',
                        'webp' => 'image/webp',
                );

                $file_extension = array_search( $mime_type, $file_extensions );

                // Upload the file to the Media Library
                $upload = wp_upload_bits( $file_name, null, file_get_contents( $uploaded_file_url ) );

                if ( ! $upload['error'] ) {
                    $file_path = $upload['file'];
                    $file_url  = $upload['url'];
                    $file_type = $upload['type'];

                    $attachment_id = wp_insert_attachment( [
                            'guid'           => $file_url,
                            'post_mime_type' => $file_type,
                            'post_title'     => preg_replace( '/\.[^.]+$/', '', $file_name ),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                    ], $file_path );

                    require_once( ABSPATH . 'wp-admin/includes/image.php' );
                    $attach_data = wp_generate_attachment_metadata( $attachment_id, $file_path );
                    wp_update_attachment_metadata( $attachment_id, $attach_data );

                    if ( $attachment_id ) {
                        set_post_thumbnail( $post_id, $attachment_id );
                    }
                }
            }
        }
    }

    /**
     * Save meta
     */
    public function post_submission_save_data( $contact_form ) {
        $post_id = $contact_form->id();

        if ( empty( $_POST['eacf7_post_submission_data'] ) ) {
            return;
        }

        $post_submission_data = stripslashes( $_POST['eacf7_post_submission_data'] );
        $post_submission_data = json_decode( $post_submission_data, true );

        update_post_meta( $post_id, 'eacf7_post_submission_data', $post_submission_data );
    }

    /**
     * @return Post_Submission|null
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

Post_Submission::instance();
