<?php

namespace EACF7;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
class Address {
    /**
     * @var null
     */
    protected static $instance = null;

    public function __construct() {
        add_action( 'wpcf7_admin_init', [$this, 'add_tag_generator'], 99 );
        add_action( 'wpcf7_init', [$this, 'add_data_handler'] );
        add_action( 'wpcf7_enqueue_scripts', array($this, 'enqueue_scripts') );
        add_filter(
            'wpcf7_validate_address',
            [$this, 'validate'],
            10,
            2
        );
        add_filter(
            'wpcf7_validate_address*',
            [$this, 'validate'],
            10,
            2
        );
        // if (version_compare(WPCF7_VERSION, '6.0', '>=')) {
        // add_action('wpcf7_swv_create_schema', array($this, 'validate_address_rules'), 10, 2);
        // }
        add_filter( 'wpcf7_posted_data', [$this, 'format_address'] );
        //set mail tags
        add_filter(
            'wpcf7_mail_tag_replaced_address',
            [$this, 'mail_tag_replaced_address'],
            10,
            4
        );
        add_filter(
            'wpcf7_mail_tag_replaced_address*',
            [$this, 'mail_tag_replaced_address'],
            10,
            4
        );
    }

    public function mail_tag_replaced_address(
        $replaced,
        $submitted,
        $html,
        $tag
    ) {
        if ( !$html ) {
            return $replaced;
        }
        $name = $tag->tag_name();
        $format = $tag->get_option( 'format', '', true );
        $line1 = ( !empty( $_POST[$name . '_line1'] ) ? sanitize_text_field( $_POST[$name . '_line1'] ) : '' );
        $line2 = ( !empty( $_POST[$name . '_line2'] ) ? sanitize_text_field( $_POST[$name . '_line2'] ) : '' );
        $city = ( !empty( $_POST[$name . '_city'] ) ? sanitize_text_field( $_POST[$name . '_city'] ) : '' );
        $state = ( !empty( $_POST[$name . '_state'] ) ? sanitize_text_field( $_POST[$name . '_state'] ) : '' );
        $zip = ( !empty( $_POST[$name . '_zip'] ) ? sanitize_text_field( $_POST[$name . '_zip'] ) : '' );
        $country = ( !empty( $_POST[$name . '_country'] ) ? sanitize_text_field( $_POST[$name . '_country'] ) : '' );
        if ( 'us' == $format ) {
            if ( !empty( $state ) ) {
                $states = $this->get_us_states();
                if ( isset( $states[$state] ) ) {
                    $state = $states[$state] . ' (' . $state . ')';
                }
            }
        }
        if ( !empty( $country ) ) {
            $countries = eacf7_get_countries();
            if ( !empty( $countries[$country] ) ) {
                $country = $countries[$country] . ' (' . $country . ')';
            }
        }
        $address = '';
        if ( !empty( $line1 ) ) {
            $address .= $line1 . '<br>';
        }
        if ( !empty( $line2 ) ) {
            $address .= $line2 . '<br>';
        }
        if ( $city ) {
            $address .= $city . ', ';
        }
        if ( $state ) {
            $address .= $state . '<br>';
        }
        if ( $zip ) {
            $address .= $zip . '<br>';
        }
        if ( $country ) {
            $address .= $country . '<br>';
        }
        return $address;
    }

    public function get_us_states() {
        $states = array(
            'AL' => esc_html__( 'Alabama', 'essential-addons-for-contact-form-7' ),
            'AK' => esc_html__( 'Alaska', 'essential-addons-for-contact-form-7' ),
            'AZ' => esc_html__( 'Arizona', 'essential-addons-for-contact-form-7' ),
            'AR' => esc_html__( 'Arkansas', 'essential-addons-for-contact-form-7' ),
            'CA' => esc_html__( 'California', 'essential-addons-for-contact-form-7' ),
            'CO' => esc_html__( 'Colorado', 'essential-addons-for-contact-form-7' ),
            'CT' => esc_html__( 'Connecticut', 'essential-addons-for-contact-form-7' ),
            'DE' => esc_html__( 'Delaware', 'essential-addons-for-contact-form-7' ),
            'DC' => esc_html__( 'District of Columbia', 'essential-addons-for-contact-form-7' ),
            'FL' => esc_html__( 'Florida', 'essential-addons-for-contact-form-7' ),
            'GA' => esc_html_x( 'Georgia', 'US State', 'essential-addons-for-contact-form-7' ),
            'HI' => esc_html__( 'Hawaii', 'essential-addons-for-contact-form-7' ),
            'ID' => esc_html__( 'Idaho', 'essential-addons-for-contact-form-7' ),
            'IL' => esc_html__( 'Illinois', 'essential-addons-for-contact-form-7' ),
            'IN' => esc_html__( 'Indiana', 'essential-addons-for-contact-form-7' ),
            'IA' => esc_html__( 'Iowa', 'essential-addons-for-contact-form-7' ),
            'KS' => esc_html__( 'Kansas', 'essential-addons-for-contact-form-7' ),
            'KY' => esc_html__( 'Kentucky', 'essential-addons-for-contact-form-7' ),
            'LA' => esc_html__( 'Louisiana', 'essential-addons-for-contact-form-7' ),
            'ME' => esc_html__( 'Maine', 'essential-addons-for-contact-form-7' ),
            'MD' => esc_html__( 'Maryland', 'essential-addons-for-contact-form-7' ),
            'MA' => esc_html__( 'Massachusetts', 'essential-addons-for-contact-form-7' ),
            'MI' => esc_html__( 'Michigan', 'essential-addons-for-contact-form-7' ),
            'MN' => esc_html__( 'Minnesota', 'essential-addons-for-contact-form-7' ),
            'MS' => esc_html__( 'Mississippi', 'essential-addons-for-contact-form-7' ),
            'MO' => esc_html__( 'Missouri', 'essential-addons-for-contact-form-7' ),
            'MT' => esc_html__( 'Montana', 'essential-addons-for-contact-form-7' ),
            'NE' => esc_html__( 'Nebraska', 'essential-addons-for-contact-form-7' ),
            'NV' => esc_html__( 'Nevada', 'essential-addons-for-contact-form-7' ),
            'NH' => esc_html__( 'New Hampshire', 'essential-addons-for-contact-form-7' ),
            'NJ' => esc_html__( 'New Jersey', 'essential-addons-for-contact-form-7' ),
            'NM' => esc_html__( 'New Mexico', 'essential-addons-for-contact-form-7' ),
            'NY' => esc_html__( 'New York', 'essential-addons-for-contact-form-7' ),
            'NC' => esc_html__( 'North Carolina', 'essential-addons-for-contact-form-7' ),
            'ND' => esc_html__( 'North Dakota', 'essential-addons-for-contact-form-7' ),
            'OH' => esc_html__( 'Ohio', 'essential-addons-for-contact-form-7' ),
            'OK' => esc_html__( 'Oklahoma', 'essential-addons-for-contact-form-7' ),
            'OR' => esc_html__( 'Oregon', 'essential-addons-for-contact-form-7' ),
            'PA' => esc_html__( 'Pennsylvania', 'essential-addons-for-contact-form-7' ),
            'RI' => esc_html__( 'Rhode Island', 'essential-addons-for-contact-form-7' ),
            'SC' => esc_html__( 'South Carolina', 'essential-addons-for-contact-form-7' ),
            'SD' => esc_html__( 'South Dakota', 'essential-addons-for-contact-form-7' ),
            'TN' => esc_html__( 'Tennessee', 'essential-addons-for-contact-form-7' ),
            'TX' => esc_html__( 'Texas', 'essential-addons-for-contact-form-7' ),
            'UT' => esc_html__( 'Utah', 'essential-addons-for-contact-form-7' ),
            'VT' => esc_html__( 'Vermont', 'essential-addons-for-contact-form-7' ),
            'VA' => esc_html__( 'Virginia', 'essential-addons-for-contact-form-7' ),
            'WA' => esc_html__( 'Washington', 'essential-addons-for-contact-form-7' ),
            'WV' => esc_html__( 'West Virginia', 'essential-addons-for-contact-form-7' ),
            'WI' => esc_html__( 'Wisconsin', 'essential-addons-for-contact-form-7' ),
            'WY' => esc_html__( 'Wyoming', 'essential-addons-for-contact-form-7' ),
        );
        return apply_filters( 'cf7_extended_us_states', $states );
    }

    public function format_address( $posted_data ) {
        // Submission instance from CF7
        $submission = \WPCF7_Submission::get_instance();
        // Make sure we have the data
        if ( !$posted_data ) {
            $posted_data = $submission->get_posted_data();
        }
        // Scan and get all form tags from cf7 generator
        $form = $submission->get_contact_form();
        $tags = $form->scan_form_tags();
        if ( !empty( $tags ) ) {
            foreach ( $tags as $tag ) {
                if ( empty( $tag->basetype ) || $tag->basetype != 'address' ) {
                    continue;
                }
                $tag = new \WPCF7_FormTag($tag);
                $name = $tag->name;
                $format = $tag->get_option( 'format', '', true );
                $line1 = ( !empty( $_POST[$name . '_line1'] ) ? sanitize_text_field( $_POST[$name . '_line1'] ) : '' );
                $line2 = ( !empty( $_POST[$name . '_line2'] ) ? sanitize_text_field( $_POST[$name . '_line2'] ) : '' );
                $city = ( !empty( $_POST[$name . '_city'] ) ? sanitize_text_field( $_POST[$name . '_city'] ) : '' );
                $state = ( !empty( $_POST[$name . '_state'] ) ? sanitize_text_field( $_POST[$name . '_state'] ) : '' );
                $zip = ( !empty( $_POST[$name . '_zip'] ) ? sanitize_text_field( $_POST[$name . '_zip'] ) : '' );
                $country = ( !empty( $_POST[$name . '_country'] ) ? sanitize_text_field( $_POST[$name . '_country'] ) : '' );
                if ( 'us' == $format ) {
                    if ( !empty( $state ) ) {
                        $states = $this->get_us_states();
                        if ( isset( $states[$state] ) ) {
                            $state = $states[$state] . ' (' . $state . ')';
                        }
                    }
                }
                if ( !empty( $country ) ) {
                    $countries = eacf7_get_countries();
                    if ( !empty( $countries[$country] ) ) {
                        $country = $countries[$country] . ' (' . $country . ')';
                    }
                }
                $address = $line1 . ' ' . $line2 . ' ' . $city . ' ' . $state . ' ' . $zip . ' ' . $country;
                $posted_data[$name] = $address;
            }
        }
        return $posted_data;
    }

    public function validate( $result, $tag ) {
        $tag = new \WPCF7_FormTag($tag);
        $name = $tag->name;
        $line1 = ( !empty( $_POST[$name . '_line1'] ) ? sanitize_text_field( $_POST[$name . '_line1'] ) : '' );
        $line2 = ( !empty( $_POST[$name . '_line2'] ) ? sanitize_text_field( $_POST[$name . '_line2'] ) : '' );
        $city = ( !empty( $_POST[$name . '_city'] ) ? sanitize_text_field( $_POST[$name . '_city'] ) : '' );
        $state = ( !empty( $_POST[$name . '_state'] ) ? sanitize_text_field( $_POST[$name . '_state'] ) : '' );
        $zip = ( !empty( $_POST[$name . '_zip'] ) ? sanitize_text_field( $_POST[$name . '_zip'] ) : '' );
        $country = ( !empty( $_POST[$name . '_country'] ) ? sanitize_text_field( $_POST[$name . '_country'] ) : '' );
        $format = $tag->get_option( 'format', '.*', true );
        $required_fields = $tag->get_option( 'required_fields', '.*', true );
        $required_fields = explode( '|', $required_fields );
        $required_fields = array_map( 'trim', $required_fields );
        $missing_fields = [];
        $invalid_fields = [];
        if ( in_array( 'line1', $required_fields ) && empty( $line1 ) ) {
            $missing_fields[] = __( 'Address Line 1', 'essential-addons-for-contact-form-7' );
        }
        if ( in_array( 'line2', $required_fields ) && empty( $line2 ) ) {
            $missing_fields[] = __( 'Address Line 2', 'essential-addons-for-contact-form-7' );
        }
        if ( in_array( 'city', $required_fields ) ) {
            if ( empty( $city ) ) {
                $missing_fields[] = __( 'City', 'essential-addons-for-contact-form-7' );
            } elseif ( !preg_match( '/^[a-zA-Z\\s]+$/', $city ) ) {
                $invalid_fields[] = __( 'City (letters only)', 'essential-addons-for-contact-form-7' );
            }
        }
        if ( in_array( 'state', $required_fields ) ) {
            if ( empty( $state ) ) {
                $missing_fields[] = __( 'State', 'essential-addons-for-contact-form-7' );
            } elseif ( !preg_match( '/^[a-zA-Z\\s]{2,}$/', $state ) ) {
                $invalid_fields[] = __( 'State (valid format)', 'essential-addons-for-contact-form-7' );
            }
        }
        if ( in_array( 'zip', $required_fields ) ) {
            if ( empty( $zip ) ) {
                $missing_fields[] = __( 'Zip Code', 'essential-addons-for-contact-form-7' );
            } elseif ( !preg_match( '/^\\d{4}(-\\d{4})?$/', $zip ) ) {
                $invalid_fields[] = __( 'Zip Code (e.g., 1234 or 12345 or 12345-6789)', 'essential-addons-for-contact-form-7' );
            }
        }
        if ( 'international' == $format && in_array( 'country', $required_fields ) ) {
            if ( empty( $country ) ) {
                $missing_fields[] = __( 'Country', 'essential-addons-for-contact-form-7' );
            } elseif ( !preg_match( '/^[a-zA-Z\\s]+$/', $country ) ) {
                $invalid_fields[] = __( 'Country (letters only)', 'essential-addons-for-contact-form-7' );
            }
        }
        if ( !empty( $missing_fields ) || !empty( $invalid_fields ) ) {
            $error_message = '';
            if ( !empty( $missing_fields ) ) {
                $error_message .= sprintf( __( 'Please fill in the required fields: %s.', 'essential-addons-for-contact-form-7' ), implode( ', ', $missing_fields ) );
            }
            if ( !empty( $invalid_fields ) ) {
                if ( !empty( $error_message ) ) {
                    $error_message .= ' ';
                }
                $error_message .= sprintf( __( 'Please correct the following fields: %s.', 'essential-addons-for-contact-form-7' ), implode( ', ', $invalid_fields ) );
            }
            $result->invalidate( $tag, $error_message );
        }
        return $result;
    }

    public function validate_address_rules( $schema, $contact_form ) {
        $tags = $contact_form->scan_form_tags( array(
            'basetype' => array('address'),
        ) );
        foreach ( $tags as $tag ) {
            if ( $tag->is_required() ) {
                $tag = new \WPCF7_FormTag($tag);
                $name = $tag->name;
                $line1 = ( !empty( $_POST[$name . '_line1'] ) ? sanitize_text_field( $_POST[$name . '_line1'] ) : '' );
                $line2 = ( !empty( $_POST[$name . '_line2'] ) ? sanitize_text_field( $_POST[$name . '_line2'] ) : '' );
                $city = ( !empty( $_POST[$name . '_city'] ) ? sanitize_text_field( $_POST[$name . '_city'] ) : '' );
                $state = ( !empty( $_POST[$name . '_state'] ) ? sanitize_text_field( $_POST[$name . '_state'] ) : '' );
                $zip = ( !empty( $_POST[$name . '_zip'] ) ? sanitize_text_field( $_POST[$name . '_zip'] ) : '' );
                $country = ( !empty( $_POST[$name . '_country'] ) ? sanitize_text_field( $_POST[$name . '_country'] ) : '' );
                $format = $tag->get_option( 'format', '.*', true );
                $missing_fields = [];
                $invalid_fields = [];
                if ( empty( $line1 ) ) {
                    $missing_fields[] = __( 'Address Line 1', 'essential-addons-for-contact-form-7' );
                }
                if ( empty( $line2 ) ) {
                    $missing_fields[] = __( 'Address Line 2', 'essential-addons-for-contact-form-7' );
                }
                if ( empty( $city ) ) {
                    $missing_fields[] = __( 'City', 'essential-addons-for-contact-form-7' );
                } elseif ( !preg_match( '/^[a-zA-Z\\s]+$/', $city ) ) {
                    $invalid_fields[] = __( 'City (letters only)', 'essential-addons-for-contact-form-7' );
                }
                if ( empty( $state ) ) {
                    $missing_fields[] = __( 'State', 'essential-addons-for-contact-form-7' );
                } elseif ( !preg_match( '/^[a-zA-Z\\s]{2,}$/', $state ) ) {
                    $invalid_fields[] = __( 'State (valid format)', 'essential-addons-for-contact-form-7' );
                }
                if ( empty( $zip ) ) {
                    $missing_fields[] = __( 'Zip Code', 'essential-addons-for-contact-form-7' );
                } elseif ( !preg_match( '/^\\d{5}(-\\d{4})?$/', $zip ) ) {
                    $invalid_fields[] = __( 'Zip Code (e.g., 12345 or 12345-6789)', 'essential-addons-for-contact-form-7' );
                }
                if ( 'international' == $format ) {
                    if ( empty( $country ) ) {
                        $missing_fields[] = __( 'Country', 'essential-addons-for-contact-form-7' );
                    } elseif ( !preg_match( '/^[a-zA-Z\\s]+$/', $country ) ) {
                        $invalid_fields[] = __( 'Country (letters only)', 'essential-addons-for-contact-form-7' );
                    }
                }
                if ( !empty( $missing_fields ) || !empty( $invalid_fields ) ) {
                    $error_message = '';
                    if ( !empty( $missing_fields ) ) {
                        $error_message .= sprintf( __( 'Please fill in the required fields: %s.', 'essential-addons-for-contact-form-7' ), implode( ', ', $missing_fields ) );
                    }
                    if ( !empty( $invalid_fields ) ) {
                        if ( !empty( $error_message ) ) {
                            $error_message .= ' ';
                        }
                        $error_message .= sprintf( __( 'Please correct the following fields: %s.', 'essential-addons-for-contact-form-7' ), implode( ', ', $invalid_fields ) );
                    }
                    $schema->add_rule( wpcf7_swv_create_rule( 'required', array(
                        'field' => $tag->name,
                        'error' => $error_message,
                    ) ) );
                }
            }
        }
    }

    /**
     * Add shortcode handler to CF7.
     */
    public function add_data_handler() {
        if ( function_exists( 'wpcf7_add_form_tag' ) ) {
            wpcf7_add_form_tag( ['address', 'address*'], [$this, 'render_address'], [
                'name-attr' => true,
            ] );
        }
    }

    public function render_address( $tag ) {
        $tag = new \WPCF7_FormTag($tag);
        // check and make sure tag name is not empty
        if ( empty( $tag->name ) ) {
            return '';
        }
        // Validate our fields
        $validation_error = wpcf7_get_validation_error( $tag->name );
        $class = $tag->get_class_option( 'wpcf7-form-control eacf7-address' );
        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }
        $atts = [
            'class'        => $class,
            'id'           => $tag->get_id_option(),
            'name'         => $tag->name,
            'aria-invalid' => ( $validation_error ? 'true' : 'false' ),
        ];
        $atts = wpcf7_format_atts( $atts );
        $format = $tag->get_option( 'format', '.*', true );
        $required_fields = $tag->get_option( 'required_fields', '.*', true );
        $required_fields = explode( '|', $required_fields );
        $required_fields = array_map( 'trim', $required_fields );
        $labels = ( !empty( $tag['values'][0] ) ? esc_html( $tag['values'][0] ) : '' );
        $labels = explode( ',', $labels );
        $labels = array_map( 'trim', $labels );
        $placeholders = ( !empty( $tag['content'] ) ? esc_html( $tag['content'] ) : '' );
        $placeholders = explode( '|', $placeholders );
        $placeholders = array_map( 'trim', $placeholders );
        ob_start();
        ?>

		<span class="wpcf7-form-control-wrap" data-name="<?php 
        echo esc_attr( $tag->name );
        ?>">
			<div <?php 
        echo $atts;
        ?>>
				<div class="eacf7-address__fields">
					<div class="address-field field-line1">
						<label for="<?php 
        echo esc_attr( $tag->name );
        ?>_lin1">
							<?php 
        if ( isset( $labels[0] ) && !empty( $labels[0] ) ) {
            echo esc_html( $labels[0] );
        } else {
            echo esc_html__( 'Address Line 1', 'essential-addons-for-contact-form-7' );
        }
        ?>

							<?php 
        if ( in_array( 'line1', $required_fields ) ) {
            ?>
								<span class="required">*</span>
							<?php 
        }
        ?>
						</label>
						<input type="text" id="<?php 
        echo esc_attr( $tag->name );
        ?>_line1"
							name="<?php 
        echo esc_attr( $tag->name );
        ?>_line1"
							placeholder="<?php 
        echo ( !empty( $placeholders[0] ) ? esc_attr( $placeholders[0] ) : '' );
        ?>" />
					</div>

					<div class="address-field field-line2">
						<label for="<?php 
        echo esc_attr( $tag->name );
        ?>_line2">
							<?php 
        if ( isset( $labels[1] ) && !empty( $labels[1] ) ) {
            echo esc_html( $labels[1] );
        } else {
            echo esc_html__( 'Address Line 2', 'essential-addons-for-contact-form-7' );
        }
        ?>

							<?php 
        if ( in_array( 'line2', $required_fields ) ) {
            ?>
								<span class="required">*</span>
							<?php 
        }
        ?>

						</label>
						<input type="text" id="<?php 
        echo esc_attr( $tag->name );
        ?>_street"
							name="<?php 
        echo esc_attr( $tag->name );
        ?>_line2"
							placeholder="<?php 
        echo ( !empty( $placeholders[1] ) ? esc_attr( $placeholders[1] ) : '' );
        ?>" />
					</div>

					<div class="address-field field-city">
						<label for="<?php 
        echo esc_attr( $tag->name );
        ?>_city">
							<?php 
        if ( isset( $labels[2] ) && !empty( $labels[2] ) ) {
            echo esc_html( $labels[2] );
        } else {
            echo esc_html__( 'City', 'essential-addons-for-contact-form-7' );
        }
        ?>

							<?php 
        if ( in_array( 'city', $required_fields ) ) {
            ?>
								<span class="required">*</span>
							<?php 
        }
        ?>
						</label>
						<input type="text" id="<?php 
        echo esc_attr( $tag->name );
        ?>_city"
							name="<?php 
        echo esc_attr( $tag->name );
        ?>_city"
							placeholder="<?php 
        echo ( !empty( $placeholders[2] ) ? esc_attr( $placeholders[2] ) : '' );
        ?>" />
					</div>

					<div class="address-field field-state">
						<label for="<?php 
        echo esc_attr( $tag->name );
        ?>_state">
							<?php 
        if ( isset( $labels[3] ) && !empty( $labels[3] ) ) {
            echo esc_html( $labels[3] );
        } else {
            echo esc_html__( 'State', 'essential-addons-for-contact-form-7' );
        }
        ?>

							<?php 
        if ( in_array( 'state', $required_fields ) ) {
            ?>
								<span class="required">*</span>
							<?php 
        }
        ?>
						</label>

						<?php 
        if ( 'us' == $format ) {
            ?>
							<select id="<?php 
            echo esc_attr( $tag->name );
            ?>_state"
								name="<?php 
            echo esc_attr( $tag->name );
            ?>_state">
								<option value=""><?php 
            echo ( !empty( $placeholders[3] ) ? esc_attr( $placeholders[3] ) : esc_html__( 'Select a state', 'essential-addons-for-contact-form-7' ) );
            ?></option>
								<?php 
            foreach ( $this->get_us_states() as $key => $state ) {
                ?>
									<option value="<?php 
                echo esc_attr( $key );
                ?>"><?php 
                echo esc_html( $state );
                ?></option>
								<?php 
            }
            ?>
							</select>
						<?php 
        } else {
            ?>
							<input type="text" id="<?php 
            echo esc_attr( $tag->name );
            ?>_state"
								name="<?php 
            echo esc_attr( $tag->name );
            ?>_state"
								placeholder="<?php 
            echo ( !empty( $placeholders[3] ) ? esc_attr( $placeholders[3] ) : '' );
            ?>" />
						<?php 
        }
        ?>

					</div>

					<div class="address-field field-zip">
						<label for="<?php 
        echo esc_attr( $tag->name );
        ?>_zip">
							<?php 
        if ( 'us' == $format ) {
            if ( isset( $labels[4] ) && !empty( $labels[4] ) ) {
                echo esc_html( $labels[4] );
            } else {
                echo esc_html__( 'Zip', 'essential-addons-for-contact-form-7' );
            }
        } else {
            if ( isset( $labels[4] ) && !empty( $labels[4] ) ) {
                echo esc_html( $labels[4] );
            } else {
                echo esc_html__( 'Post Code', 'essential-addons-for-contact-form-7' );
            }
        }
        ?>

							<?php 
        if ( in_array( 'zip', $required_fields ) ) {
            ?>
								<span class="required">*</span>
							<?php 
        }
        ?>
						</label>

						<input type="text" id="<?php 
        echo esc_attr( $tag->name );
        ?>_zip"
							name="<?php 
        echo esc_attr( $tag->name );
        ?>_zip"
							placeholder="<?php 
        echo ( !empty( $placeholders[4] ) ? esc_attr( $placeholders[4] ) : '' );
        ?>" />

					</div>

					<?php 
        if ( 'international' == $format ) {
            ?>
						<div class="address-field field-country">
							<label for="<?php 
            echo esc_attr( $tag->name );
            ?>_country">
								<?php 
            if ( isset( $labels[5] ) ) {
                echo esc_html( $labels[5] );
            } else {
                echo esc_html__( 'Country', 'essential-addons-for-contact-form-7' );
            }
            ?>

								<?php 
            if ( in_array( 'country', $required_fields ) ) {
                ?>
									<span class="required">*</span>
								<?php 
            }
            ?>
							</label>
							<select id="<?php 
            echo esc_attr( $tag->name );
            ?>_country"
								name="<?php 
            echo esc_attr( $tag->name );
            ?>_country">
								<option value=""><?php 
            echo ( !empty( $placeholders[5] ) ? esc_html( $placeholders[5] ) : esc_html__( 'Select Country', 'essential-addons-for-contact-form-7' ) );
            ?></option>
								<?php 
            foreach ( eacf7_get_countries() as $key => $value ) {
                ?>
									<option value="<?php 
                echo esc_attr( $key );
                ?>"><?php 
                echo esc_html( $value );
                ?></option>
								<?php 
            }
            ?>
							</select>
						</div>
					<?php 
        }
        ?>
				</div>
			</div>

			<?php 
        echo $validation_error;
        ?>
		</span>
	<?php 
        return ob_get_clean();
    }

    public function enqueue_scripts() {
        wp_enqueue_style( 'eacf7-select2' );
        wp_enqueue_style( 'eacf7-frontend' );
        wp_enqueue_script( 'eacf7-select2' );
        wp_enqueue_script( 'eacf7-frontend' );
    }

    public function add_tag_generator() {
        $tag_generator = \WPCF7_TagGenerator::get_instance();
        if ( version_compare( WPCF7_VERSION, '6.0', '>=' ) ) {
            $tag_generator->add(
                'address',
                __( 'Address', 'essential-addons-for-contact-form-7' ),
                [$this, 'tag_generator_body_v6'],
                [
                    'version' => 2,
                ]
            );
        } else {
            $tag_generator->add( 'address', __( 'Address', 'essential-addons-for-contact-form-7' ), [$this, 'tag_generator_body'] );
        }
    }

    public function tag_generator_body_v6( $contact_form, $options ) {
        $tgg = new \WPCF7_TagGeneratorGenerator($options['content']);
        ?>
		<header class="description-box">
			<div class="eacf7-notice eacf7-notice-info">
				<p>
					<span class="dashicons dashicons-info-outline"></span>
					<?php 
        echo sprintf( __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Address</a>', 'essential-addons-for-contact-form-7' ), 'https://softlabbd.com/docs/address-field/' );
        ?>
				</p>
			</div>
		</header>
		<div class="control-box">
			<?php 
        $tgg->print( 'field_type', array(
            'with_required'  => true,
            'select_options' => array(
                'address' => esc_html__( 'Address', 'essential-addons-for-contact-form-7' ),
            ),
        ) );
        $tgg->print( 'field_name' );
        ?>

			<!-- Autocomplete -->
			<fieldset>
				<legend>
					<?php 
        echo sprintf( __( 'Autocomplete %s', 'essential-addons-for-contact-form-7' ), ( !eacf7_fs()->can_use_premium_code__premium_only() ? '(Pro)' : '' ) );
        ?>
				</legend>
				<p class="oneline address-autocomplete">
					<label>
						<input type="checkbox" <?php 
        echo ( eacf7_fs()->can_use_premium_code__premium_only() ? 'data-tag-part="option" data-tag-option="autocomplete:"' : '' );
        ?> name="autocomplete" value="1" />
						<?php 
        echo esc_html__( 'Autocomplete Country, City, State, Zip Fields [IP Geolocation based]', 'essential-addons-for-contact-form-7' );
        ?>
					</label>
				</p>
			</fieldset>

			<fieldset>
				<legend><?php 
        echo esc_html__( 'Format', 'essential-addons-for-contact-form-7' );
        ?></legend>
				<p class="oneline address-format">
					<label>
						<input type="radio" data-tag-part="option" data-tag-option="format:" name="format" value="us" />
						<?php 
        echo esc_html__( 'US', 'essential-addons-for-contact-form-7' );
        ?>
					</label>

					<label>
						<input type="radio" data-tag-part="option" data-tag-option="format:" name="format" value="international" checked="checked" />
						<?php 
        echo esc_html__( 'International', 'essential-addons-for-contact-form-7' );
        ?>
					</label>
				</p>

				<p class="description"><?php 
        echo esc_html__( 'Select the address format.', 'essential-addons-for-contact-form-7' );
        ?></p>
			</fieldset>

			<!-- Required Fields -->
			<fieldset>
				<legend><?php 
        echo esc_html__( 'Required Fields', 'essential-addons-for-contact-form-7' );
        ?></legend>

				<p class="address-required-fields oneline">
					<label>
						<input type="checkbox" value="line1" id="line1" checked />
						<?php 
        echo esc_html__( 'Address Line 1', 'essential-addons-for-contact-form-7' );
        ?>
					</label>

					<label>
						<input type="checkbox" value="line2" id="line2" />
						<?php 
        echo esc_html__( 'Address Line 2', 'essential-addons-for-contact-form-7' );
        ?>
					</label>

					<label>
						<input type="checkbox" value="city" id="city" checked />
						<?php 
        echo esc_html__( 'City', 'essential-addons-for-contact-form-7' );
        ?>
					</label>

					<label>
						<input type="checkbox" value="state" id="state" />
						<?php 
        echo esc_html__( 'State', 'essential-addons-for-contact-form-7' );
        ?>
					</label>

					<label>
						<input type="checkbox" value="zip" id="zip" checked />
						<?php 
        echo esc_html__( 'Zip', 'essential-addons-for-contact-form-7' );
        ?>
					</label>

					<label class="country-label">
						<input type="checkbox" value="country" id="country" checked />
						<?php 
        echo esc_html__( 'Country', 'essential-addons-for-contact-form-7' );
        ?>
					</label>

					<input type="hidden" data-tag-part="option" data-tag-option="required_fields:" name="required_fields" value="line1|city|zip|country" />
				</p>

				<p class="description"><?php 
        echo esc_html__( 'Select the required address fields.', 'essential-addons-for-contact-form-7' );
        ?></p>
			</fieldset>

			<!-- Label -->
			<fieldset>
				<legend><?php 
        echo esc_html__( 'Labels', 'essential-addons-for-contact-form-7' );
        ?></legend>
				<table>
					<tr>
						<td><?php 
        echo esc_html__( 'Line 1', 'essential-addons-for-contact-form-7' );
        ?></td>
						<td><input type="text" name="line1_label" /></td>
					</tr>
					<tr>
						<td><?php 
        echo esc_html__( 'Line 2', 'essential-addons-for-contact-form-7' );
        ?></td>
						<td><input type="text" name="line2_label" /></td>
					</tr>
					<tr>
						<td><?php 
        echo esc_html__( 'City', 'essential-addons-for-contact-form-7' );
        ?></td>
						<td><input type="text" name="city_label" /></td>
					</tr>
					<tr>
						<td><?php 
        echo esc_html__( 'State', 'essential-addons-for-contact-form-7' );
        ?></td>
						<td><input type="text" name="state_label" /></td>
					</tr>
					<tr>
						<td><?php 
        echo esc_html__( 'Zip/Postcode', 'essential-addons-for-contact-form-7' );
        ?></td>
						<td><input type="text" name="zip_label" /></td>
					</tr>
					<tr>
						<td><?php 
        echo esc_html__( 'Country', 'essential-addons-for-contact-form-7' );
        ?></td>
						<td><input type="text" name="country_label" /></td>
					</tr>
				</table>

				<input type="hidden" data-tag-part="value" name="values" value="" />

				<p class="description"><?php 
        echo esc_html__( 'Customize the labels for the address fields.', 'essential-addons-for-contact-form-7' );
        ?></p>
			</fieldset>

			<!-- Placeholder -->
			<fieldset>
				<legend><?php 
        echo esc_html__( 'Placeholders', 'essential-addons-for-contact-form-7' );
        ?></legend>

				<table>
					<tr>
						<td><?php 
        echo esc_html__( 'Line 1', 'essential-addons-for-contact-form-7' );
        ?></td>
						<td><input type="text" name="line1_placeholder" /></td>
					</tr>
					<tr>
						<td><?php 
        echo esc_html__( 'Line 2', 'essential-addons-for-contact-form-7' );
        ?></td>
						<td><input type="text" name="line2_placeholder" /></td>
					</tr>
					<tr>
						<td><?php 
        echo esc_html__( 'City', 'essential-addons-for-contact-form-7' );
        ?></td>
						<td><input type="text" name="city_placeholder" /></td>
					</tr>
					<tr>
						<td><?php 
        echo esc_html__( 'State', 'essential-addons-for-contact-form-7' );
        ?></td>
						<td><input type="text" name="state_placeholder" /></td>
					</tr>
					<tr>
						<td><?php 
        echo esc_html__( 'Zip/Postcode', 'essential-addons-for-contact-form-7' );
        ?></td>
						<td><input type="text" name="zip_placeholder" /></td>
					</tr>
					<tr>
						<td><?php 
        echo esc_html__( 'Country', 'essential-addons-for-contact-form-7' );
        ?></td>
						<td><input type="text" name="country_placeholder" /></td>
					</tr>
				</table>

				<input type="hidden" data-tag-part="content" name="content" value="" />

				<p class="description"><?php 
        echo esc_html__( 'If left empty, the placeholder does not appear. You can use the following placeholder: Write your address line1 here, Write your address line2 here, Write your city here, Write your state here, Write your zip/postcode here, Select country', 'essential-addons-for-contact-form-7' );
        ?></p>
			</fieldset>

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
        $type = 'address';
        ?>
		<div class="control-box">
			<fieldset>

				<div class="eacf7-notice eacf7-notice-info">
					<p>
						<span class="dashicons dashicons-info-outline"></span>
						<?php 
        echo sprintf( __( 'Not sure how to set this? Check our documentation on <a href="%1$s" target="_blank">Address Field</a>', 'essential-addons-for-contact-form-7' ), 'https://softlabbd.com/docs/address-field/' );
        ?>
					</p>
				</div>

				<table class="form-table">
					<tbody>

						<!-- Name -->
						<tr>
							<th scope="row">
								<label for="<?php 
        echo esc_attr( $args['content'] . '-name' );
        ?>">
									<?php 
        echo esc_html__( 'Name', 'essential-addons-for-contact-form-7' );
        ?>
								</label>
							</th>
							<td>
								<input type="text" name="name" class="tg-name oneline"
									id="<?php 
        echo esc_attr( $args['content'] . '-name' );
        ?>" />
							</td>
						</tr>

						<!-- Autocomplete -->
						<tr>
							<th scope="row"><?php 
        echo esc_html__( 'Autocomplete', 'essential-addons-for-contact-form-7' );
        ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<?php 
        echo sprintf( __( 'Autocomplete %s', 'essential-addons-for-contact-form-7' ), ( !eacf7_fs()->can_use_premium_code__premium_only() ? '(Pro)' : '' ) );
        ?>
									</legend>

									<p class="oneline address-autocomplete">
										<label>
											<?php 
        ?>
												<input type="checkbox" />
											<?php 
        ?>
											<?php 
        echo esc_html__( 'Autocomplete Country, City, State, Zip Fields [IP Geolocation based]', 'essential-addons-for-contact-form-7' );
        ?>
										</label>
									</p>
								</fieldset>
							</td>
						</tr>

						<!-- Format -->
						<tr>
							<th scope="row"><?php 
        echo esc_html__( 'Format', 'essential-addons-for-contact-form-7' );
        ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><?php 
        echo esc_html__( 'Format', 'essential-addons-for-contact-form-7' );
        ?></legend>

									<p class="oneline address-format">
										<label>

											<input type="radio" name="format" class="option" value="us" />
											<?php 
        echo esc_html__( 'US', 'essential-addons-for-contact-form-7' );
        ?>
										</label>

										<label>
											<input type="radio" name="format" class="option" value="international"
												checked="checked" />
											<?php 
        echo esc_html__( 'International', 'essential-addons-for-contact-form-7' );
        ?>
										</label>
									</p>

									<p class="description"><?php 
        echo esc_html__( 'Select the address format.', 'essential-addons-for-contact-form-7' );
        ?></p>
								</fieldset>
							</td>
						</tr>

						<!-- Required Fields -->
						<tr>
							<th scope="row"><?php 
        echo esc_html__( 'Required Fields', 'essential-addons-for-contact-form-7' );
        ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><?php 
        echo esc_html__( 'Required Fields', 'essential-addons-for-contact-form-7' );
        ?></legend>

									<p class="address-required-fields oneline">
										<label>
											<input type="checkbox" value="line1" checked id="line1" />
											<?php 
        echo esc_html__( 'Address Line 1', 'essential-addons-for-contact-form-7' );
        ?>
										</label>

										<label>
											<input type="checkbox" value="line2" id="line2" />
											<?php 
        echo esc_html__( 'Address Line 2', 'essential-addons-for-contact-form-7' );
        ?>
										</label>

										<label>
											<input type="checkbox" value="city" checked id="city" />
											<?php 
        echo esc_html__( 'City', 'essential-addons-for-contact-form-7' );
        ?>
										</label>

										<label>
											<input type="checkbox" value="state" checked id="state" />
											<?php 
        echo esc_html__( 'State', 'essential-addons-for-contact-form-7' );
        ?>
										</label>

										<label>
											<input type="checkbox" value="zip" checked id="zip" />
											<?php 
        echo esc_html__( 'Zip', 'essential-addons-for-contact-form-7' );
        ?>
										</label>

										<label class="country-label">
											<input type="checkbox" value="country" id="country" checked />
											<?php 
        echo esc_html__( 'Country', 'essential-addons-for-contact-form-7' );
        ?>
										</label>

										<input type="hidden" name="required_fields" id="required_fields" class="option"
											value="line1|city|state|zip|country" />
									</p>

									<p class="description"><?php 
        echo esc_html__( 'Select the required address fields.', 'essential-addons-for-contact-form-7' );
        ?></p>
								</fieldset>
							</td>
						</tr>

						<!-- ID -->
						<tr>
							<th scope="row">
								<label for="<?php 
        echo esc_attr( $args['content'] . '-id' );
        ?>">
									<?php 
        echo esc_html__( 'ID', 'essential-addons-for-contact-form-7' );
        ?>
								</label>
							</th>
							<td>
								<input type="text" name="id" class="idvalue oneline option"
									id="<?php 
        echo esc_attr( $args['content'] . '-id' );
        ?>" />
							</td>
						</tr>

						<!-- Class -->
						<tr>
							<th scope="row">
								<label for="<?php 
        echo esc_attr( $args['content'] . '-class' );
        ?>">
									<?php 
        echo esc_html__( 'Class', 'essential-addons-for-contact-form-7' );
        ?>
								</label>
							</th>
							<td>
								<input type="text" name="class" class="classvalue oneline option"
									id="<?php 
        echo esc_attr( $args['content'] . '-class' );
        ?>" />
							</td>
						</tr>

					</tbody>
				</table>
			</fieldset>
		</div>

		<div class="insert-box">
			<input type="text" name="<?php 
        echo esc_attr( $type );
        ?>" class="tag code" readonly="readonly"
				onfocus="this.select()" />

			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag"
					value="<?php 
        echo esc_attr__( 'Insert Tag', 'essential-addons-for-contact-form-7' );
        ?>" />
			</div>

			<br class="clear" />

			<p class="description mail-tag">
				<label for="<?php 
        echo esc_attr( $args['content'] . '-mailtag' );
        ?>">
					<?php 
        printf( 'To display the address fields in the email, insert the mail-tag (%s) in the Mail tab.', '<strong><span class="mail-tag"></span></strong>' );
        ?>
					<input type="text" class="mail-tag code eacf7-hidden" readonly="readonly"
						id="<?php 
        echo esc_attr( $args['content'] . '-mailtag' );
        ?>" />
				</label>
			</p>
		</div>
<?php 
    }

    /**
     * @return Address|null
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}

Address::instance();