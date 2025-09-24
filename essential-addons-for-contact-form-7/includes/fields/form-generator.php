<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Form_Generator {
	/**
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wpcf7_admin_footer', array( $this, 'form_generator_popup' ) );

		add_action( 'wp_ajax_get_forms_tags', array( $this, 'get_forms_tags' ) );

		add_action( 'wp_ajax_get_form_tag_content', array( $this, 'get_form_tag_content' ) );
	}

	/**
	 * AI Form Generator Popup
	 * @since 1.0.0
	 */
	public function form_generator_popup() {
		ob_start();
		?>
		<div id="eacf7-form-generator" class="eacf7-form-generator"></div>
		<?php
		echo ob_get_clean();
	}

	/**
	 * Get Forms & Tags on Ajax
	 * @since 1.0.0
	 */
	public function get_forms_tags() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'eacf7' ) ) {
			exit( esc_html__( "Security error", 'essential-addons-for-contact-form-7' ) );
		}

		$tag_generator = \WPCF7_TagGenerator::get_instance( 'panel', true );

		$reflector = new \ReflectionClass( 'WPCF7_TagGenerator' );
		$property  = $reflector->getProperty( 'panels' );
		$property->setAccessible( true );

		$panels   = $property->getValue( $tag_generator );
		$tag_data = [];

		foreach ( $panels as $key => $value ) {
			if ( $key != 'step_start' && $key != 'step_end' && $key != 'conditional' && $key != 'repeater' ) {
				$tag_value['value'] = $key;
				$tag_value['label'] = $value['title'];
				$tag_data[]         = $tag_value;
			}
		}

		$form_data = [
			[
				"label" => __('Advertising Form', 'essential-addons-for-contact-form-7'),
				"value" => "advertising-form",
			],
			[
				"label" => __('Attendance Form', 'essential-addons-for-contact-form-7'),
				"value" => "attendance-form",
			],
			[
				"label" => __('Attendance Certificate Form', 'essential-addons-for-contact-form-7'),
				"value" => "attendance-certificate-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Appointment Request Form', 'essential-addons-for-contact-form-7'),
				"value" => "appointment-request-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Black Friday Deals Submission Form', 'essential-addons-for-contact-form-7'),
				"value" => "black-friday-deals-submission-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Blood Donation Form', 'essential-addons-for-contact-form-7'),
				"value" => "blood-donation-form",
			],
			[
				"label" => __('Hotel Booking Form', 'essential-addons-for-contact-form-7'),
				"value" => "hotel-booking-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Client Survey Form', 'essential-addons-for-contact-form-7'),
				"value" => "client-survey-form",
			],
			[
				"label" => __('Client Consultation Form', 'essential-addons-for-contact-form-7'),
				"value" => "client-consultation-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Car Rental Form', 'essential-addons-for-contact-form-7'),
				"value" => "car-rental-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Conference Registration', 'essential-addons-for-contact-form-7'),
				"value" => "conference-registration",
			],
			[
				"label" => __('Course Registration Form', 'essential-addons-for-contact-form-7'),
				"value" => "course-registration-form",
			],
			[
				"label" => __('Complaint Form', 'essential-addons-for-contact-form-7'),
				"value" => "complaint-form",
			],
			[
				"label" => __('Conversational Restaurant Order Form', 'essential-addons-for-contact-form-7'),
				"value" => "conversational-restaurant-order-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Simple Conversational Form', 'essential-addons-for-contact-form-7'),
				"value" => "simple-conversational-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Multistep Survey Form', 'essential-addons-for-contact-form-7'),
				"value" => "multistep-survey-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Document Verification Form', 'essential-addons-for-contact-form-7'),
				"value" => "document-verification-form",
			],
			[
				"label" => __('Doctor Appointment Form', 'essential-addons-for-contact-form-7'),
				"value" => "doctor-appointment-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Email Newsletter Subscription Form', 'essential-addons-for-contact-form-7'),
				"value" => "blog-newsletter-subscription-form",
			],
			[
				"label" => __('Employee Information Form', 'essential-addons-for-contact-form-7'),
				"value" => "employee-information-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Employee Nomination Form', 'essential-addons-for-contact-form-7'),
				"value" => "employee-nomination-form",
			],
			[
				"label" => __('Employee Reference Request Form', 'essential-addons-for-contact-form-7'),
				"value" => "employee-reference-request-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Event Sponsorship Form', 'essential-addons-for-contact-form-7'),
				"value" => "event-sponsorship-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Flight Reservation Form', 'essential-addons-for-contact-form-7'),
				"value" => "flight-reservation-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Frontend Post Submission', 'essential-addons-for-contact-form-7'),
				"value" => "frontend-post-submission",
				"isPro"	=> true,
			],
			[
				"label" => __('Mailing Address', 'essential-addons-for-contact-form-7'),
				"value" => "mailing-address",
			],
			[
				"label" => __('Identity Verification Form', 'essential-addons-for-contact-form-7'),
				"value" => "identity-verification-form",
				"isPro"	=> true,
			],
			[
				"label" => __('IT Service Ticket Form', 'essential-addons-for-contact-form-7'),
				"value" => "it-service-ticket-form",
			],
			[
				"label" => __('Information Request Form', 'essential-addons-for-contact-form-7'),
				"value" => "information-request-form",
			],
			[
				"label" => __('Job Application Form', 'essential-addons-for-contact-form-7'),
				"value" => "job-application-form",
			],
			[
				"label" => __('Leave Request Form', 'essential-addons-for-contact-form-7'),
				"value" => "leave-request-form",
			],
			[
				"label" => __('Simple Application Form', 'essential-addons-for-contact-form-7'),
				"value" => "simple-application-form",
			],
			[
				"label" => __('Simple Contact Form', 'essential-addons-for-contact-form-7'),
				"value" => "simple-contact-form",
			],
			[
				"label" => __('Non-Profit Dinner RSVP Form', 'essential-addons-for-contact-form-7'),
				"value" => "non-profit-dinner-rsvp-form",
			],
			[
				"label" => __('Installation Check Form', 'essential-addons-for-contact-form-7'),
				"value" => "installation-check-form",
			],
			[
				"label" => __('Employee Complaint Form', 'essential-addons-for-contact-form-7'),
				"value" => "employee-complaint-form",
			],
			[
				"label" => __('Online Complaint Form', 'essential-addons-for-contact-form-7'),
				"value" => "online-complaint-form",
			],
			[
				"label" => __('Online Donation Form', 'essential-addons-for-contact-form-7'),
				"value" => "online-donation-form",
			],
			[
				"label" => __('Quick Donation Form', 'essential-addons-for-contact-form-7'),
				"value" => "quick-donation-form",
			],
			[
				"label" => __('Online Event Registration', 'essential-addons-for-contact-form-7'),
				"value" => "online-event-registration",
			],
			[
				"label" => __('Order Cancellation Form', 'essential-addons-for-contact-form-7'),
				"value" => "order-cancellation-form",
			],
			[
				"label" => __('Simple Feedback Form', 'essential-addons-for-contact-form-7'),
				"value" => "simple-feedback-form",
			],
			[
				"label" => __('Item Request Form', 'essential-addons-for-contact-form-7'),
				"value" => "item-request-form",
			],
			[
				"label" => __('General Inquiry Form', 'essential-addons-for-contact-form-7'),
				"value" => "general-inquiry-form",
			],
			[
				"label" => __('Service Request Form', 'essential-addons-for-contact-form-7'),
				"value" => "service-request-form",
			],
			[
				"label" => __('Newsletter Registration Form', 'essential-addons-for-contact-form-7'),
				"value" => "newsletter-registration-form",
			],
			[
				"label" => __('Straw Poll Form', 'essential-addons-for-contact-form-7'),
				"value" => "straw-poll-form",
			],
			[
				"label" =>  __('WooCommerce Product Review Form', 'essential-addons-for-contact-form-7'),
				"value" => "woocommerce-product-review",
				"isPro"	=> true,
			],
			[
				"label" => __('General Product Review Form', 'essential-addons-for-contact-form-7'),
				"value" => "general-product-review",
				"isPro"	=> true,
			],
			[
				"label" => __('Service Rating Form', 'essential-addons-for-contact-form-7'),
				"value" => "service-rating-form",
			],
			[
				"label" => __('Simple Repeater Form', 'essential-addons-for-contact-form-7'),
				"value" => "simple-repeater-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Bug Report Form', 'essential-addons-for-contact-form-7'),
				"value" => "bug-report-form",
			],
			[
				"label" => __('Product Quote Form', 'essential-addons-for-contact-form-7'),
				"value" => "product-quote-form",
			],
			[
				"label" => __('Online Booking Form', 'essential-addons-for-contact-form-7'),
				"value" => "online-booking-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Online Petition Form', 'essential-addons-for-contact-form-7'),
				"value" => "online-petition-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Product Order Form', 'essential-addons-for-contact-form-7'),
				"value" => "product-order-form",
			],
			[
				"label" => __('Pizza Order Form', 'essential-addons-for-contact-form-7'),
				"value" => "pizza-order-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Project Proposal Form', 'essential-addons-for-contact-form-7'),
				"value" => "project-proposal-form",
			],
			[
				"label" => __('Newsletter Subscription Form', 'essential-addons-for-contact-form-7'),
				"value" => "newsletter-subscription-form",
			],
			[
				"label" => __('Referral Program Form', 'essential-addons-for-contact-form-7'),
				"value" => "refferral-program-form",
			],
			[
				"label" => __('Restaurant Evaluation Form', 'essential-addons-for-contact-form-7'),
				"value" => "restaurant-evaluation-form",
			],
			[
				"label" => __('RSVP Form', 'essential-addons-for-contact-form-7'),
				"value" => "rsvp-form",
			],
			[
				"label" => __('New Customer Registration Form', 'essential-addons-for-contact-form-7'),
				"value" => "new-customer-registration-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Support Request Form', 'essential-addons-for-contact-form-7'),
				"value" => "support-request-form",
			],
			[
				"label" => __('Support Satisfaction Survey Form', 'essential-addons-for-contact-form-7'),
				"value" => "support-satisfaction-survey-form",
			],
			[
				"label" => __('Software Survey Form', 'essential-addons-for-contact-form-7'),
				"value" => "software-survey-form",
			],
			[
				"label" => __('Website Survey', 'essential-addons-for-contact-form-7'),
				"value" => "website-survey",
			],
			[
				"label" => __('Volunteer Candidate Registration Form', 'essential-addons-for-contact-form-7'),
				"value" => "volunteer-candidate-registration-form",
			],
			[
				"label" => __('Video Submit Form', 'essential-addons-for-contact-form-7'),
				"value" => "video-submit-form",
			],
			[
				"label" => __('Volunteer Recruitment Form', 'essential-addons-for-contact-form-7'),
				"value" => "volunteer-recruitment-form",
			],
			[
				"label" => __('University Admission Form', 'essential-addons-for-contact-form-7'),
				"value" => "university-admission-form",
			],
			[
				"label" => __('Transcript Request Form', 'essential-addons-for-contact-form-7'),
				"value" => "transcript-request-form",
			],
			[
				"label" => __('Training Application Form', 'essential-addons-for-contact-form-7'),
				"value" => "training-application-form",
			],
			[
				"label" => __('Training Feedback Form', 'essential-addons-for-contact-form-7'),
				"value" => "training-feedback-form",
				"isPro"	=> true,
			],
			[
				"label" => __('Behavior Assessment Multistep Form', 'essential-addons-for-contact-form-7'),
				"value" => "behavior-assessment-multistep-form",
				"isPro"	=> true,
			],
		];

		$data = [
			'tags'  => $tag_data,
			'forms' => $form_data,
		];

		echo wp_send_json_success( $data );

		die();
	}

	public function get_form_tag_content() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'eacf7' ) ) {
			exit( esc_html__( "Security error", 'essential-addons-for-contact-form-7' ) );
		}

		$value = ( isset( $_POST['data'] ) && is_array( $_POST['data'] ) ) ? array_map( 'sanitize_text_field', $_POST['data'] ) : null;
		
		$data = '';

		// check & get form
		if ( ! empty( $value ) && count( $value ) >= 2 && 'form' == $value['type'] ) {
			require_once EACF7_INCLUDES . '/fields/templates/forms.php';
		}

		// get tag
		if ( ! empty( $value ) && count( $value ) >= 2 && 'tag' == $value['type'] ) {
			require_once EACF7_INCLUDES . '/fields/templates/tags.php';
		}

		wp_send_json_success( $data );

		die();
	}

	/**
	 * @return Form_Generator|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Form_Generator::instance();
