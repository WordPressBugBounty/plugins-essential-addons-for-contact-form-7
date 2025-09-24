<?php

namespace EACF7;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
class Column {
    /**
     * @var null
     */
    protected static $instance = null;

    public function __construct() {
        add_action( 'wpcf7_init', array($this, 'add_shortcodes'), 99 );
        add_action( 'wpcf7_admin_init', array($this, 'add_tag_generator'), 99 );
        // Assets
        add_action( 'wpcf7_enqueue_scripts', array($this, 'enqueue_scripts') );
        add_filter( 'wpcf7_contact_form_properties', array($this, 'eacf7_column_properties'), 99 );
        add_filter( 'wpcf7_contact_form_properties', array($this, 'eacf7_row_properties'), 99 );
    }

    /*
     * Form tag
     */
    public function add_shortcodes() {
        wpcf7_add_form_tag( 'eacf7-col', array($this, 'column_tag_handler'), true );
        wpcf7_add_form_tag( 'eacf7-row', array($this, 'column_tag_handler'), true );
    }

    public function column_tag_handler( $tag ) {
        ob_start();
        $tag = new \WPCF7_FormTag($tag);
        ?>
		<div>
			<?php 
        $tag->content;
        ?>
		</div>
	<?php 
        return ob_get_clean();
    }

    /**
     * Tag Generator
     */
    public function add_tag_generator() {
        $tag_generator = \WPCF7_TagGenerator::get_instance();
        if ( version_compare( WPCF7_VERSION, '6.0', '>=' ) ) {
            $tag_generator->add(
                'eacf7_column',
                __( 'Add Column', 'essential-addons-for-contact-form-7' ),
                [$this, 'tag_generator_body_v6'],
                [
                    'version' => 2,
                ]
            );
        } else {
            $tag_generator->add( 'eacf7_column', __( 'Add Column', 'essential-addons-for-contact-form-7' ), [$this, 'tag_generator_body'] );
        }
    }

    /**
     * Tag Generator
     * @since 1.0.1
     * @author monzuralam
     */
    public function tag_generator_body_v6( $contact_form, $options ) {
        $tgg = new \WPCF7_TagGeneratorGenerator($options['content']);
        ?>
		<header class="description-box">
			<div class="eacf7-notice eacf7-notice-info">
				<p>
					<span class="dashicons dashicons-info-outline"></span>
					<?php 
        echo sprintf( __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Column layout</a>', 'essential-addons-for-contact-form-7' ), 'https://softlabbd.com/docs/column-layout/' );
        ?>
				</p>
			</div>
		</header>
		<div class="control-box">
			<h3><?php 
        echo esc_html__( 'Contact form 7 columns / Grid Layout', 'essential-addons-for-contact-form-7' );
        ?></h3>
			<p><?php 
        echo esc_html__( 'You can easily create two columns, three Columns even Four columns form with Contact form 7 using this feature. Just insert tag you need from below list.', 'essential-addons-for-contact-form-7' );
        ?></p>

			<!-- 1 Column -->
			<fieldset class="column-1 eacf7-column-select example-active" data-column="[eacf7-row][eacf7-col col:1] --your field code-- [/eacf7-col][/eacf7-row]">
				<legend>
					<?php 
        echo esc_html__( '1 Column', 'essential-addons-for-contact-form-7' );
        ?>
				</legend>
				<div>
					<pre>[eacf7-row]
    [eacf7-col col:1] --your field code-- [/eacf7-col]
[/eacf7-row]</pre>
					<a class="button eacf7-column-button"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
				</div>
			</fieldset>

			<!-- 2 Column -->
			<fieldset class="column-2 eacf7-column-select" data-column="[eacf7-row][eacf7-col col:2] --your field code-- [/eacf7-col][eacf7-col col:2] --your field code-- [/eacf7-col][/eacf7-row]">
				<legend>
					<?php 
        echo esc_html__( '2 Column', 'essential-addons-for-contact-form-7' );
        ?>
				</legend>
				<div>
					<pre>[eacf7-row]
    [eacf7-col col:2] --your field code-- [/eacf7-col]
    [eacf7-col col:2] --your field code-- [/eacf7-col]
[/eacf7-row]</pre>
					<a class="button eacf7-column-button"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
				</div>
			</fieldset>

			<!-- 3 Column -->
			<fieldset class="column-3 eacf7-column-select" data-column="[eacf7-row][eacf7-col col:3] --your field code-- [/eacf7-col][eacf7-col col:3] --your field code-- [/eacf7-col][eacf7-col col:3] --your field code-- [/eacf7-col][/eacf7-row]">
				<legend>
					<?php 
        echo esc_html__( '3 Column', 'essential-addons-for-contact-form-7' );
        ?>
				</legend>
				<div>
					<pre>[eacf7-row]
    [eacf7-col col:3] --your field code-- [/eacf7-col]
    [eacf7-col col:3] --your field code-- [/eacf7-col]
    [eacf7-col col:3] --your field code-- [/eacf7-col]
[/eacf7-row]</pre>
					<a class="button eacf7-column-button"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
				</div>
			</fieldset>

			<!-- 4 Column -->
			<fieldset class="column-4 eacf7-column-select" data-column="[eacf7-row][eacf7-col col:4] --your field code-- [/eacf7-col][eacf7-col col:4] --your field code-- [/eacf7-col][eacf7-col col:4] --your field code-- [/eacf7-col][eacf7-col col:4] --your field code-- [/eacf7-col][/eacf7-row]">
				<legend>
					<?php 
        echo esc_html__( '4 Column', 'essential-addons-for-contact-form-7' );
        ?>
				</legend>
				<div>
					<pre>[eacf7-row]
    [eacf7-col col:4] --your field code-- [/eacf7-col]
    [eacf7-col col:4] --your field code-- [/eacf7-col]
    [eacf7-col col:4] --your field code-- [/eacf7-col]
    [eacf7-col col:4] --your field code-- [/eacf7-col]
[/eacf7-row]</pre>
					<a class="button eacf7-column-button"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
				</div>
			</fieldset>

			<!-- 5 Column -->
			<fieldset class="column-5 eacf7-column-select" data-column="[eacf7-row][eacf7-col col:5] --your field code-- [/eacf7-col][eacf7-col col:5] --your field code-- [/eacf7-col][eacf7-col col:5] --your field code-- [/eacf7-col][eacf7-col col:5] --your field code-- [/eacf7-col][eacf7-col col:5] --your field code-- [/eacf7-col][/eacf7-row]">
				<legend>
					<?php 
        echo esc_html__( '5 Column', 'essential-addons-for-contact-form-7' );
        ?>
				</legend>
				<div>
					<pre>[eacf7-row]
    [eacf7-col col:5] --your field code-- [/eacf7-col]
    [eacf7-col col:5] --your field code-- [/eacf7-col]
    [eacf7-col col:5] --your field code-- [/eacf7-col]
    [eacf7-col col:5] --your field code-- [/eacf7-col]
    [eacf7-col col:5] --your field code-- [/eacf7-col]
[/eacf7-row]</pre>
					<a class="button eacf7-column-button"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
				</div>
			</fieldset>

			<!-- 6 Column -->
			<fieldset class="column-6 eacf7-column-select" data-column="[eacf7-row][eacf7-col col:6] --your field code-- [/eacf7-col][eacf7-col col:6] --your field code-- [/eacf7-col][eacf7-col col:6] --your field code-- [/eacf7-col][eacf7-col col:6] --your field code-- [/eacf7-col][eacf7-col col:6] --your field code-- [/eacf7-col][eacf7-col col:6] --your field code-- [/eacf7-col][/eacf7-row]">
				<legend>
					<?php 
        echo esc_html__( '6 Column', 'essential-addons-for-contact-form-7' );
        ?>
				</legend>
				<div>
					<pre>[eacf7-row]
    [eacf7-col col:6] --your field code-- [/eacf7-col]
    [eacf7-col col:6] --your field code-- [/eacf7-col]
    [eacf7-col col:6] --your field code-- [/eacf7-col]
    [eacf7-col col:6] --your field code-- [/eacf7-col]
    [eacf7-col col:6] --your field code-- [/eacf7-col]
    [eacf7-col col:6] --your field code-- [/eacf7-col]
[/eacf7-row]</pre>
					<a class="button eacf7-column-button"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
				</div>
			</fieldset>

			<!-- Custom Column -->
			<fieldset>
				<legend><?php 
        echo esc_html__( 'Custom Column', 'essential-addons-for-contact-form-7' );
        ?></legend>
				<div class="eacf7-custom-column">
					<button class="add-custom-column button-primary" type="button">
						<?php 
        echo esc_html__( '+Add Column', 'essential-addons-for-contact-form-7' );
        ?>
					</button>
				</div>
				<div class="custom-column-insert-btn-wrap">
					<a class="button eacf7-column-button eacf7-custom-column-insert"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
				</div>
			</fieldset>
		</div>
		<footer class="insert-box">
			<?php 
        $tgg->print( 'insert_box_content' );
        ?>
		</footer>
	<?php 
    }

    public function tag_generator_body( $args = '' ) {
        $args = wp_parse_args( $args, array() );
        $eacf7_field_type = 'eacf7-col';
        ?>
		<div class="control-box eacf7-column-control-box">
			<fieldset>
				<div class="eacf7-notice eacf7-notice-info">
					<p>
						<span class="dashicons dashicons-info-outline"></span>
						<?php 
        echo sprintf( __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Column</a>', 'essential-addons-for-contact-form-7' ), 'https://softlabbd.com/docs/column-layout/' );
        ?>
					</p>
				</div>

				<table class="form-table">

					<h3><?php 
        echo esc_html__( 'Contact form 7 columns / Grid Layout', 'essential-addons-for-contact-form-7' );
        ?></h3>
					<p><?php 
        echo esc_html__( 'You can easily create two columns, three Columns even Four columns form with Contact form 7 using this feature. Just insert tag you need from below list.', 'essential-addons-for-contact-form-7' );
        ?></p>
					<tbody>
						<tr class="column-1 eacf7-column-select example-active"
							data-column="[eacf7-row][eacf7-col col:1] --your field code-- [/eacf7-col][/eacf7-row]">
							<th>
								<?php 
        echo esc_html__( '1 Column', 'essential-addons-for-contact-form-7' );
        ?>
								<a class="button eacf7-column-button"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
							</th>
							<td class="eacf7_code">
								<pre>
[eacf7-row]
    [eacf7-col col:1] --your field code-- [/eacf7-col]
[/eacf7-row]
                                </pre>
							</td>
						</tr>
						<tr class="column-2 eacf7-column-select"
							data-column="[eacf7-row][eacf7-col col:2] --your field code-- [/eacf7-col][eacf7-col col:2] --your field code-- [/eacf7-col][/eacf7-row]">
							<th>
								<?php 
        echo esc_html__( '2 Column', 'essential-addons-for-contact-form-7' );
        ?>
								<a class="button eacf7-column-button"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
							</th>
							<td class="eacf7_code">
								<pre>
[eacf7-row]
    [eacf7-col col:2] --your field code-- [/eacf7-col]
    [eacf7-col col:2] --your field code-- [/eacf7-col]
[/eacf7-row]
                            </pre>
							</td>
						</tr>
						<tr class="column-3 eacf7-column-select"
							data-column="[eacf7-row][eacf7-col col:3] --your field code-- [/eacf7-col][eacf7-col col:3] --your field code-- [/eacf7-col][eacf7-col col:3] --your field code-- [/eacf7-col][/eacf7-row]">
							<th>
								<?php 
        echo esc_html__( '3 Column', 'essential-addons-for-contact-form-7' );
        ?>
								<a class="button eacf7-column-button"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
							</th>
							<td class="eacf7_code">
								<pre>
[eacf7-row]
    [eacf7-col col:3] --your field code-- [/eacf7-col]
    [eacf7-col col:3] --your field code-- [/eacf7-col]
    [eacf7-col col:3] --your field code-- [/eacf7-col]
[/eacf7-row]
                                </pre>
							</td>
						</tr>
						<tr class="column-4 eacf7-column-select"
							data-column="[eacf7-row][eacf7-col col:4] --your field code-- [/eacf7-col][eacf7-col col:4] --your field code-- [/eacf7-col][eacf7-col col:4] --your field code-- [/eacf7-col][eacf7-col col:4] --your field code-- [/eacf7-col][/eacf7-row]">
							<th>
								<?php 
        echo esc_html__( '4 Column', 'essential-addons-for-contact-form-7' );
        ?>
								<a class="button eacf7-column-button"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
							</th>
							<td class="eacf7_code">
								<pre>
[eacf7-row]
    [eacf7-col col:4] --your field code-- [/eacf7-col]
    [eacf7-col col:4] --your field code-- [/eacf7-col]
    [eacf7-col col:4] --your field code-- [/eacf7-col]
    [eacf7-col col:4] --your field code-- [/eacf7-col]
[/eacf7-row]
                                </pre>
							</td>
						</tr>
						<tr class="column-5 eacf7-column-select"
							data-column="[eacf7-row][eacf7-col col:5] --your field code-- [/eacf7-col][eacf7-col col:5] --your field code-- [/eacf7-col][eacf7-col col:5] --your field code-- [/eacf7-col][eacf7-col col:5] --your field code-- [/eacf7-col][eacf7-col col:5] --your field code-- [/eacf7-col][/eacf7-row]">
							<th>
								<?php 
        echo esc_html__( '5 Column', 'essential-addons-for-contact-form-7' );
        ?>
								<a class="button eacf7-column-button"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
							</th>
							<td class="eacf7_code">
								<pre>
[eacf7-row]
    [eacf7-col col:5] --your field code-- [/eacf7-col]
    [eacf7-col col:5] --your field code-- [/eacf7-col]
    [eacf7-col col:5] --your field code-- [/eacf7-col]
    [eacf7-col col:5] --your field code-- [/eacf7-col]
    [eacf7-col col:5] --your field code-- [/eacf7-col]
[/eacf7-row]
                                </pre>
							</td>
						</tr>
						<tr class="column-6 eacf7-column-select"
							data-column="[eacf7-row][eacf7-col col:6] --your field code-- [/eacf7-col][eacf7-col col:6] --your field code-- [/eacf7-col][eacf7-col col:6] --your field code-- [/eacf7-col][eacf7-col col:6] --your field code-- [/eacf7-col][eacf7-col col:6] --your field code-- [/eacf7-col][eacf7-col col:6] --your field code-- [/eacf7-col][/eacf7-row]">
							<th>
								<?php 
        echo esc_html__( '6 Column', 'essential-addons-for-contact-form-7' );
        ?>
								<a class="button eacf7-column-button"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
							</th>
							<td class="eacf7_code">
								<pre>
[eacf7-row]
    [eacf7-col col:6] --your field code-- [/eacf7-col]
    [eacf7-col col:6] --your field code-- [/eacf7-col]
    [eacf7-col col:6] --your field code-- [/eacf7-col]
    [eacf7-col col:6] --your field code-- [/eacf7-col]
    [eacf7-col col:6] --your field code-- [/eacf7-col]
    [eacf7-col col:6] --your field code-- [/eacf7-col]
[/eacf7-row]
                                </pre>
							</td>
						</tr>
						<tr class="eacf7-custom-column-wrap">
							<th class="column-1">
								<?php 
        echo esc_html__( 'Custom Column', 'essential-addons-for-contact-form-7' );
        ?>
								<a class="button eacf7-column-button eacf7-custom-column-insert"><?php 
        echo esc_html__( 'Insert tag', 'essential-addons-for-contact-form-7' );
        ?></a>
							</th>
							<td>
								<span class="eacf7-custom-column"></span>
								<span><button
										class="add-custom-column button-primary"><?php 
        echo esc_html__( '+Add Column', 'essential-addons-for-contact-form-7' );
        ?></button></span>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>

		<div class="insert-box eacf7-column-insert-box">
			<input type="text" name="<?php 
        echo esc_attr( $eacf7_field_type );
        ?>"
				class="tag code eacf7-column-tag-insert" readonly="readonly" onfocus="this.select()" />

			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag eacf7-column-insert-tag"
					value="<?php 
        echo esc_attr( __( 'Insert Tag', 'essential-addons-for-contact-form-7' ) );
        ?>" />
			</div>
		</div>
<?php 
    }

    /**
     * Assets
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 'eacf7-frontend' );
    }

    /**
     * Column properties
     *
     * @param array $properties
     *
     * @return array
     */
    public function eacf7_column_properties( $properties ) {
        // Check if not in admin or doing AJAX.
        if ( !is_admin() || defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            $form = $properties['form'];
            $form_parts = preg_split(
                '/(\\[\\/?eacf7-col(?:\\]|\\s.*?\\]))/',
                $form,
                -1,
                PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
            );
            ob_start();
            foreach ( $form_parts as $form_part ) {
                if ( substr( $form_part, 0, 11 ) == '[eacf7-col ' ) {
                    $tag_parts = explode( ' ', rtrim( $form_part, ']' ) );
                    array_shift( $tag_parts );
                    $eacf7_column_class = '';
                    $eacf7_column_custom_width = '';
                    foreach ( $tag_parts as $tag_part ) {
                        switch ( $tag_part ) {
                            case 'col:1':
                                $eacf7_column_class = 'eacf7-col eacf7-col-12';
                                break;
                            case 'col:2':
                                $eacf7_column_class = 'eacf7-col eacf7-col-2';
                                break;
                            case 'col:3':
                                $eacf7_column_class = 'eacf7-col eacf7-col-3';
                                break;
                            case 'col:4':
                                $eacf7_column_class = 'eacf7-col eacf7-col-4';
                                break;
                            case 'col:5':
                                $eacf7_column_class = 'eacf7-col eacf7-col-5';
                                break;
                            case 'col:6':
                                $eacf7_column_class = 'eacf7-col eacf7-col-6';
                                break;
                            default:
                        }
                    }
                    if ( !empty( $eacf7_column_class ) ) {
                        $html = '<div class="' . esc_attr( $eacf7_column_class ) . '">';
                    } else {
                    }
                    echo $html;
                } else {
                    if ( $form_part == '[/eacf7-col]' ) {
                        echo '</div>';
                    } else {
                        echo $form_part;
                    }
                }
            }
            $properties['form'] = ob_get_clean();
        }
        return $properties;
    }

    /**
     * Row properties
     *
     * @param array $properties
     *
     * @return array
     */
    public function eacf7_row_properties( $properties ) {
        // Check if not in admin or doing AJAX.
        if ( !is_admin() || defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            $form = $properties['form'];
            $form_parts = preg_split(
                '/(\\[\\/?eacf7-row(?:\\]|\\s.*?\\]))/',
                $form,
                -1,
                PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
            );
            ob_start();
            foreach ( $form_parts as $form_part ) {
                if ( preg_match( '/^\\[eacf7-row/', $form_part, $matches ) ) {
                    // Check if there's a class attribute
                    if ( preg_match( '/class="([^"]*)"/', $form_part, $matches ) ) {
                        $custom_class = $matches[1];
                        echo '<div class="eacf7-row ' . esc_attr( $custom_class ) . '">';
                    } else {
                        echo '<div class="eacf7-row">';
                    }
                } elseif ( $form_part == '[/eacf7-row]' ) {
                    echo '</div>';
                } else {
                    echo $form_part;
                }
            }
            $properties['form'] = ob_get_clean();
        }
        return $properties;
    }

    /**
     * @return Column|null
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}

Column::instance();