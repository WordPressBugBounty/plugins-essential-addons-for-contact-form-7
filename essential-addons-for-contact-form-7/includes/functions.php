<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function eacf7_size_to_bytes( $size ) {

	if ( is_numeric( $size ) ) {
		return $size;
	}

	$suffix = substr( $size, - 1 );
	$value  = substr( $size, 0, - 1 );

	switch ( strtoupper( $suffix ) ) {
		case 'P':
			$value *= 1024;

		case 'T':
			$value *= 1024;

		case 'G':
			$value *= 1024;

		case 'M':
			$value *= 1024;

		case 'K':
			$value *= 1024;
			break;
	}

	return $value;
}

/**
 * Get Max Upload size
 */
function eacf7_get_max_upload_size( $bytes = false ) {

	$max = wp_max_upload_size();
	if ( $bytes ) {
		return $max;
	}

	return size_format( $max );
}

/**
 * Countries
 */
function eacf7_get_countries() {

	$countries = array(
		'AF' => __( 'Afghanistan', 'essential-addons-for-contact-form-7' ),
		'AL' => __( 'Albania', 'essential-addons-for-contact-form-7' ),
		'DZ' => __( 'Algeria', 'essential-addons-for-contact-form-7' ),
		'AS' => __( 'American Samoa', 'essential-addons-for-contact-form-7' ),
		'AD' => __( 'Andorra', 'essential-addons-for-contact-form-7' ),
		'AO' => __( 'Angola', 'essential-addons-for-contact-form-7' ),
		'AI' => __( 'Anguilla', 'essential-addons-for-contact-form-7' ),
		'AQ' => __( 'Antarctica', 'essential-addons-for-contact-form-7' ),
		'AG' => __( 'Antigua and Barbuda', 'essential-addons-for-contact-form-7' ),
		'AR' => __( 'Argentina', 'essential-addons-for-contact-form-7' ),
		'AM' => __( 'Armenia', 'essential-addons-for-contact-form-7' ),
		'AW' => __( 'Aruba', 'essential-addons-for-contact-form-7' ),
		'AU' => __( 'Australia', 'essential-addons-for-contact-form-7' ),
		'AT' => __( 'Austria', 'essential-addons-for-contact-form-7' ),
		'AZ' => __( 'Azerbaijan', 'essential-addons-for-contact-form-7' ),
		'BS' => __( 'Bahamas', 'essential-addons-for-contact-form-7' ),
		'BH' => __( 'Bahrain', 'essential-addons-for-contact-form-7' ),
		'BD' => __( 'Bangladesh', 'essential-addons-for-contact-form-7' ),
		'BB' => __( 'Barbados', 'essential-addons-for-contact-form-7' ),
		'BY' => __( 'Belarus', 'essential-addons-for-contact-form-7' ),
		'BE' => __( 'Belgium', 'essential-addons-for-contact-form-7' ),
		'BZ' => __( 'Belize', 'essential-addons-for-contact-form-7' ),
		'BJ' => __( 'Benin', 'essential-addons-for-contact-form-7' ),
		'BM' => __( 'Bermuda', 'essential-addons-for-contact-form-7' ),
		'BT' => __( 'Bhutan', 'essential-addons-for-contact-form-7' ),
		'BO' => __( 'Bolivia', 'essential-addons-for-contact-form-7' ),
		'BA' => __( 'Bosnia and Herzegovina', 'essential-addons-for-contact-form-7' ),
		'BW' => __( 'Botswana', 'essential-addons-for-contact-form-7' ),
		'BV' => __( 'Bouvet Island', 'essential-addons-for-contact-form-7' ),
		'BR' => __( 'Brazil', 'essential-addons-for-contact-form-7' ),
		'BQ' => __( 'British Antarctic Territory', 'essential-addons-for-contact-form-7' ),
		'IO' => __( 'British Indian Ocean Territory', 'essential-addons-for-contact-form-7' ),
		'VG' => __( 'British Virgin Islands', 'essential-addons-for-contact-form-7' ),
		'BN' => __( 'Brunei', 'essential-addons-for-contact-form-7' ),
		'BG' => __( 'Bulgaria', 'essential-addons-for-contact-form-7' ),
		'BF' => __( 'Burkina Faso', 'essential-addons-for-contact-form-7' ),
		'BI' => __( 'Burundi', 'essential-addons-for-contact-form-7' ),
		'KH' => __( 'Cambodia', 'essential-addons-for-contact-form-7' ),
		'CM' => __( 'Cameroon', 'essential-addons-for-contact-form-7' ),
		'CA' => __( 'Canada', 'essential-addons-for-contact-form-7' ),
		'CV' => __( 'Cape Verde', 'essential-addons-for-contact-form-7' ),
		'KY' => __( 'Cayman Islands', 'essential-addons-for-contact-form-7' ),
		'CF' => __( 'Central African Republic', 'essential-addons-for-contact-form-7' ),
		'TD' => __( 'Chad', 'essential-addons-for-contact-form-7' ),
		'CL' => __( 'Chile', 'essential-addons-for-contact-form-7' ),
		'CN' => __( 'China', 'essential-addons-for-contact-form-7' ),
		'CX' => __( 'Christmas Island', 'essential-addons-for-contact-form-7' ),
		'CC' => __( 'Cocos [Keeling] Islands', 'essential-addons-for-contact-form-7' ),
		'CO' => __( 'Colombia', 'essential-addons-for-contact-form-7' ),
		'KM' => __( 'Comoros', 'essential-addons-for-contact-form-7' ),
		'CG' => __( 'Congo - Brazzaville', 'essential-addons-for-contact-form-7' ),
		'CD' => __( 'Congo - Kinshasa', 'essential-addons-for-contact-form-7' ),
		'CK' => __( 'Cook Islands', 'essential-addons-for-contact-form-7' ),
		'CR' => __( 'Costa Rica', 'essential-addons-for-contact-form-7' ),
		'HR' => __( 'Croatia', 'essential-addons-for-contact-form-7' ),
		'CU' => __( 'Cuba', 'essential-addons-for-contact-form-7' ),
		'CY' => __( 'Cyprus', 'essential-addons-for-contact-form-7' ),
		'CZ' => __( 'Czech Republic', 'essential-addons-for-contact-form-7' ),
		'CI' => __( 'Côte d’Ivoire', 'essential-addons-for-contact-form-7' ),
		'DK' => __( 'Denmark', 'essential-addons-for-contact-form-7' ),
		'DJ' => __( 'Djibouti', 'essential-addons-for-contact-form-7' ),
		'DM' => __( 'Dominica', 'essential-addons-for-contact-form-7' ),
		'DO' => __( 'Dominican Republic', 'essential-addons-for-contact-form-7' ),
		'EC' => __( 'Ecuador', 'essential-addons-for-contact-form-7' ),
		'EG' => __( 'Egypt', 'essential-addons-for-contact-form-7' ),
		'SV' => __( 'El Salvador', 'essential-addons-for-contact-form-7' ),
		'GQ' => __( 'Equatorial Guinea', 'essential-addons-for-contact-form-7' ),
		'ER' => __( 'Eritrea', 'essential-addons-for-contact-form-7' ),
		'EE' => __( 'Estonia', 'essential-addons-for-contact-form-7' ),
		'ET' => __( 'Ethiopia', 'essential-addons-for-contact-form-7' ),
		'FK' => __( 'Falkland Islands', 'essential-addons-for-contact-form-7' ),
		'FO' => __( 'Faroe Islands', 'essential-addons-for-contact-form-7' ),
		'FJ' => __( 'Fiji', 'essential-addons-for-contact-form-7' ),
		'FI' => __( 'Finland', 'essential-addons-for-contact-form-7' ),
		'FR' => __( 'France', 'essential-addons-for-contact-form-7' ),
		'GF' => __( 'French Guiana', 'essential-addons-for-contact-form-7' ),
		'PF' => __( 'French Polynesia', 'essential-addons-for-contact-form-7' ),
		'TF' => __( 'French Southern Territories', 'essential-addons-for-contact-form-7' ),
		'GA' => __( 'Gabon', 'essential-addons-for-contact-form-7' ),
		'GM' => __( 'Gambia', 'essential-addons-for-contact-form-7' ),
		'GE' => __( 'Georgia', 'essential-addons-for-contact-form-7' ),
		'DE' => __( 'Germany', 'essential-addons-for-contact-form-7' ),
		'GH' => __( 'Ghana', 'essential-addons-for-contact-form-7' ),
		'GI' => __( 'Gibraltar', 'essential-addons-for-contact-form-7' ),
		'GR' => __( 'Greece', 'essential-addons-for-contact-form-7' ),
		'GL' => __( 'Greenland', 'essential-addons-for-contact-form-7' ),
		'GD' => __( 'Grenada', 'essential-addons-for-contact-form-7' ),
		'GP' => __( 'Guadeloupe', 'essential-addons-for-contact-form-7' ),
		'GU' => __( 'Guam', 'essential-addons-for-contact-form-7' ),
		'GT' => __( 'Guatemala', 'essential-addons-for-contact-form-7' ),
		'GG' => __( 'Guernsey', 'essential-addons-for-contact-form-7' ),
		'GN' => __( 'Guinea', 'essential-addons-for-contact-form-7' ),
		'GW' => __( 'Guinea-Bissau', 'essential-addons-for-contact-form-7' ),
		'GY' => __( 'Guyana', 'essential-addons-for-contact-form-7' ),
		'HT' => __( 'Haiti', 'essential-addons-for-contact-form-7' ),
		'HM' => __( 'Heard Island and McDonald Islands', 'essential-addons-for-contact-form-7' ),
		'HN' => __( 'Honduras', 'essential-addons-for-contact-form-7' ),
		'HK' => __( 'Hong Kong SAR China', 'essential-addons-for-contact-form-7' ),
		'HU' => __( 'Hungary', 'essential-addons-for-contact-form-7' ),
		'IS' => __( 'Iceland', 'essential-addons-for-contact-form-7' ),
		'IN' => __( 'India', 'essential-addons-for-contact-form-7' ),
		'ID' => __( 'Indonesia', 'essential-addons-for-contact-form-7' ),
		'IR' => __( 'Iran', 'essential-addons-for-contact-form-7' ),
		'IQ' => __( 'Iraq', 'essential-addons-for-contact-form-7' ),
		'IE' => __( 'Ireland', 'essential-addons-for-contact-form-7' ),
		'IM' => __( 'Isle of Man', 'essential-addons-for-contact-form-7' ),
		'IL' => __( 'Israel', 'essential-addons-for-contact-form-7' ),
		'IT' => __( 'Italy', 'essential-addons-for-contact-form-7' ),
		'JM' => __( 'Jamaica', 'essential-addons-for-contact-form-7' ),
		'JP' => __( 'Japan', 'essential-addons-for-contact-form-7' ),
		'JE' => __( 'Jersey', 'essential-addons-for-contact-form-7' ),
		'JO' => __( 'Jordan', 'essential-addons-for-contact-form-7' ),
		'KZ' => __( 'Kazakhstan', 'essential-addons-for-contact-form-7' ),
		'KE' => __( 'Kenya', 'essential-addons-for-contact-form-7' ),
		'KI' => __( 'Kiribati', 'essential-addons-for-contact-form-7' ),
		'KW' => __( 'Kuwait', 'essential-addons-for-contact-form-7' ),
		'KG' => __( 'Kyrgyzstan', 'essential-addons-for-contact-form-7' ),
		'LA' => __( 'Laos', 'essential-addons-for-contact-form-7' ),
		'LV' => __( 'Latvia', 'essential-addons-for-contact-form-7' ),
		'LB' => __( 'Lebanon', 'essential-addons-for-contact-form-7' ),
		'LS' => __( 'Lesotho', 'essential-addons-for-contact-form-7' ),
		'LR' => __( 'Liberia', 'essential-addons-for-contact-form-7' ),
		'LY' => __( 'Libya', 'essential-addons-for-contact-form-7' ),
		'LI' => __( 'Liechtenstein', 'essential-addons-for-contact-form-7' ),
		'LT' => __( 'Lithuania', 'essential-addons-for-contact-form-7' ),
		'LU' => __( 'Luxembourg', 'essential-addons-for-contact-form-7' ),
		'MO' => __( 'Macau SAR China', 'essential-addons-for-contact-form-7' ),
		'MK' => __( 'Macedonia', 'essential-addons-for-contact-form-7' ),
		'MG' => __( 'Madagascar', 'essential-addons-for-contact-form-7' ),
		'MW' => __( 'Malawi', 'essential-addons-for-contact-form-7' ),
		'MY' => __( 'Malaysia', 'essential-addons-for-contact-form-7' ),
		'MV' => __( 'Maldives', 'essential-addons-for-contact-form-7' ),
		'ML' => __( 'Mali', 'essential-addons-for-contact-form-7' ),
		'MT' => __( 'Malta', 'essential-addons-for-contact-form-7' ),
		'MH' => __( 'Marshall Islands', 'essential-addons-for-contact-form-7' ),
		'MQ' => __( 'Martinique', 'essential-addons-for-contact-form-7' ),
		'MR' => __( 'Mauritania', 'essential-addons-for-contact-form-7' ),
		'MU' => __( 'Mauritius', 'essential-addons-for-contact-form-7' ),
		'YT' => __( 'Mayotte', 'essential-addons-for-contact-form-7' ),
		'MX' => __( 'Mexico', 'essential-addons-for-contact-form-7' ),
		'FM' => __( 'Micronesia', 'essential-addons-for-contact-form-7' ),
		'MD' => __( 'Moldova', 'essential-addons-for-contact-form-7' ),
		'MC' => __( 'Monaco', 'essential-addons-for-contact-form-7' ),
		'MN' => __( 'Mongolia', 'essential-addons-for-contact-form-7' ),
		'ME' => __( 'Montenegro', 'essential-addons-for-contact-form-7' ),
		'MS' => __( 'Montserrat', 'essential-addons-for-contact-form-7' ),
		'MA' => __( 'Morocco', 'essential-addons-for-contact-form-7' ),
		'MZ' => __( 'Mozambique', 'essential-addons-for-contact-form-7' ),
		'MM' => __( 'Myanmar [Burma]', 'essential-addons-for-contact-form-7' ),
		'NA' => __( 'Namibia', 'essential-addons-for-contact-form-7' ),
		'NR' => __( 'Nauru', 'essential-addons-for-contact-form-7' ),
		'NP' => __( 'Nepal', 'essential-addons-for-contact-form-7' ),
		'NL' => __( 'Netherlands', 'essential-addons-for-contact-form-7' ),
		'NC' => __( 'New Caledonia', 'essential-addons-for-contact-form-7' ),
		'NZ' => __( 'New Zealand', 'essential-addons-for-contact-form-7' ),
		'NI' => __( 'Nicaragua', 'essential-addons-for-contact-form-7' ),
		'NE' => __( 'Niger', 'essential-addons-for-contact-form-7' ),
		'NG' => __( 'Nigeria', 'essential-addons-for-contact-form-7' ),
		'NU' => __( 'Niue', 'essential-addons-for-contact-form-7' ),
		'NF' => __( 'Norfolk Island', 'essential-addons-for-contact-form-7' ),
		'KP' => __( 'North Korea', 'essential-addons-for-contact-form-7' ),
		'MP' => __( 'Northern Mariana Islands', 'essential-addons-for-contact-form-7' ),
		'NO' => __( 'Norway', 'essential-addons-for-contact-form-7' ),
		'OM' => __( 'Oman', 'essential-addons-for-contact-form-7' ),
		'PK' => __( 'Pakistan', 'essential-addons-for-contact-form-7' ),
		'PW' => __( 'Palau', 'essential-addons-for-contact-form-7' ),
		'PS' => __( 'Palestinian Territories', 'essential-addons-for-contact-form-7' ),
		'PA' => __( 'Panama', 'essential-addons-for-contact-form-7' ),
		'PG' => __( 'Papua New Guinea', 'essential-addons-for-contact-form-7' ),
		'PY' => __( 'Paraguay', 'essential-addons-for-contact-form-7' ),
		'PE' => __( 'Peru', 'essential-addons-for-contact-form-7' ),
		'PH' => __( 'Philippines', 'essential-addons-for-contact-form-7' ),
		'PN' => __( 'Pitcairn Islands', 'essential-addons-for-contact-form-7' ),
		'PL' => __( 'Poland', 'essential-addons-for-contact-form-7' ),
		'PT' => __( 'Portugal', 'essential-addons-for-contact-form-7' ),
		'PR' => __( 'Puerto Rico', 'essential-addons-for-contact-form-7' ),
		'QA' => __( 'Qatar', 'essential-addons-for-contact-form-7' ),
		'RO' => __( 'Romania', 'essential-addons-for-contact-form-7' ),
		'RU' => __( 'Russia', 'essential-addons-for-contact-form-7' ),
		'RW' => __( 'Rwanda', 'essential-addons-for-contact-form-7' ),
		'BL' => __( 'Saint Barthélemy', 'essential-addons-for-contact-form-7' ),
		'SH' => __( 'Saint Helena', 'essential-addons-for-contact-form-7' ),
		'KN' => __( 'Saint Kitts and Nevis', 'essential-addons-for-contact-form-7' ),
		'LC' => __( 'Saint Lucia', 'essential-addons-for-contact-form-7' ),
		'MF' => __( 'Saint Martin', 'essential-addons-for-contact-form-7' ),
		'PM' => __( 'Saint Pierre and Miquelon', 'essential-addons-for-contact-form-7' ),
		'VC' => __( 'Saint Vincent and the Grenadines', 'essential-addons-for-contact-form-7' ),
		'WS' => __( 'Samoa', 'essential-addons-for-contact-form-7' ),
		'SM' => __( 'San Marino', 'essential-addons-for-contact-form-7' ),
		'SA' => __( 'Saudi Arabia', 'essential-addons-for-contact-form-7' ),
		'SN' => __( 'Senegal', 'essential-addons-for-contact-form-7' ),
		'RS' => __( 'Serbia', 'essential-addons-for-contact-form-7' ),
		'SC' => __( 'Seychelles', 'essential-addons-for-contact-form-7' ),
		'SL' => __( 'Sierra Leone', 'essential-addons-for-contact-form-7' ),
		'SG' => __( 'Singapore', 'essential-addons-for-contact-form-7' ),
		'SK' => __( 'Slovakia', 'essential-addons-for-contact-form-7' ),
		'SI' => __( 'Slovenia', 'essential-addons-for-contact-form-7' ),
		'SB' => __( 'Solomon Islands', 'essential-addons-for-contact-form-7' ),
		'SO' => __( 'Somalia', 'essential-addons-for-contact-form-7' ),
		'ZA' => __( 'South Africa', 'essential-addons-for-contact-form-7' ),
		'GS' => __( 'South Georgia and the South Sandwich Islands', 'essential-addons-for-contact-form-7' ),
		'KR' => __( 'South Korea', 'essential-addons-for-contact-form-7' ),
		'ES' => __( 'Spain', 'essential-addons-for-contact-form-7' ),
		'LK' => __( 'Sri Lanka', 'essential-addons-for-contact-form-7' ),
		'SD' => __( 'Sudan', 'essential-addons-for-contact-form-7' ),
		'SR' => __( 'Suriname', 'essential-addons-for-contact-form-7' ),
		'SJ' => __( 'Svalbard and Jan Mayen', 'essential-addons-for-contact-form-7' ),
		'SZ' => __( 'Swaziland', 'essential-addons-for-contact-form-7' ),
		'SE' => __( 'Sweden', 'essential-addons-for-contact-form-7' ),
		'CH' => __( 'Switzerland', 'essential-addons-for-contact-form-7' ),
		'SY' => __( 'Syria', 'essential-addons-for-contact-form-7' ),
		'ST' => __( 'São Tomé and Príncipe', 'essential-addons-for-contact-form-7' ),
		'TW' => __( 'Taiwan', 'essential-addons-for-contact-form-7' ),
		'TJ' => __( 'Tajikistan', 'essential-addons-for-contact-form-7' ),
		'TZ' => __( 'Tanzania', 'essential-addons-for-contact-form-7' ),
		'TH' => __( 'Thailand', 'essential-addons-for-contact-form-7' ),
		'TL' => __( 'Timor-Leste', 'essential-addons-for-contact-form-7' ),
		'TG' => __( 'Togo', 'essential-addons-for-contact-form-7' ),
		'TK' => __( 'Tokelau', 'essential-addons-for-contact-form-7' ),
		'TO' => __( 'Tonga', 'essential-addons-for-contact-form-7' ),
		'TT' => __( 'Trinidad and Tobago', 'essential-addons-for-contact-form-7' ),
		'TN' => __( 'Tunisia', 'essential-addons-for-contact-form-7' ),
		'TR' => __( 'Turkey', 'essential-addons-for-contact-form-7' ),
		'TM' => __( 'Turkmenistan', 'essential-addons-for-contact-form-7' ),
		'TC' => __( 'Turks and Caicos Islands', 'essential-addons-for-contact-form-7' ),
		'TV' => __( 'Tuvalu', 'essential-addons-for-contact-form-7' ),
		'UM' => __( 'U.S. Minor Outlying Islands', 'essential-addons-for-contact-form-7' ),
		'VI' => __( 'U.S. Virgin Islands', 'essential-addons-for-contact-form-7' ),
		'UG' => __( 'Uganda', 'essential-addons-for-contact-form-7' ),
		'UA' => __( 'Ukraine', 'essential-addons-for-contact-form-7' ),
		'AE' => __( 'United Arab Emirates', 'essential-addons-for-contact-form-7' ),
		'GB' => __( 'United Kingdom', 'essential-addons-for-contact-form-7' ),
		'US' => __( 'United States', 'essential-addons-for-contact-form-7' ),
		'UY' => __( 'Uruguay', 'essential-addons-for-contact-form-7' ),
		'UZ' => __( 'Uzbekistan', 'essential-addons-for-contact-form-7' ),
		'VU' => __( 'Vanuatu', 'essential-addons-for-contact-form-7' ),
		'VA' => __( 'Vatican City', 'essential-addons-for-contact-form-7' ),
		'VE' => __( 'Venezuela', 'essential-addons-for-contact-form-7' ),
		'VN' => __( 'Vietnam', 'essential-addons-for-contact-form-7' ),
		'WF' => __( 'Wallis and Futuna', 'essential-addons-for-contact-form-7' ),
		'EH' => __( 'Western Sahara', 'essential-addons-for-contact-form-7' ),
		'YE' => __( 'Yemen', 'essential-addons-for-contact-form-7' ),
		'ZM' => __( 'Zambia', 'essential-addons-for-contact-form-7' ),
		'ZW' => __( 'Zimbabwe', 'essential-addons-for-contact-form-7' ),
		'AX' => __( 'Åland Islands', 'essential-addons-for-contact-form-7' ),
	);

	return $countries;
}

/**
 * Sanitize Array
 */
function eacf7_sanitize_array( $array ) {
	foreach ( $array as $key => &$value ) {
		if ( is_array( $value ) ) {
			$value = eacf7_sanitize_array( $value );
		} elseif ( in_array( $value, array( 'true', 'false' ) ) ) {
			$value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
		} elseif ( is_numeric( $value ) ) {
			if ( strpos( $value, '.' ) !== false ) {
				$value = floatval( $value );
			} elseif ( filter_var( $value, FILTER_VALIDATE_INT ) !== false && $value <= PHP_INT_MAX ) {
				$value = intval( $value );
			} else {
				// Keep large integers or non-integer values as string
				$value = $value;
			}
		} else {
			$value = wp_kses_post( $value );
		}
	}

	return $array;
}

/**
 * Settings
 */
function eacf7_get_settings( $key = null, $default = null ) {
	$settings = (array) get_option( 'eacf7_settings', array() );

	if ( ! isset( $settings['fields'] ) ) {
		$settings['fields'] = array(
			'address',
			'country-list',
			'file-upload',
			'image-upload',
			'honeypot',
			'phone',
			'range-slider',
			'star-rating',
			'section-break',
			'dynamic-text',
			'date-time',
		);
	}

	if ( ! isset( $settings['features'] ) ) {
		$settings['features'] = array(
			'form-styler',
			'form-template',
			'preview',
			'entries',
			'redirection',
			'conditional',
			'submission-id',
			'form-template',
			'form-generator',
		);
	}

	if ( empty( $settings ) && ! empty( $default ) ) {
		return $default;
	}

	if ( empty( $key ) ) {
		return ! empty( $settings ) ? $settings : array();
	}

	return $settings[ $key ] ?? $default;
}

/**
 * Get Forms
 */
/**
 * All Contact Forms
 */
function eacf7_get_forms() {
	$posts = get_posts(
		array(
			'post_type'   => 'wpcf7_contact_form',
			'numberposts' => - 1,
		)
	);

	$forms = array();
	foreach ( $posts as $post ) {
		$post_id           = $post->ID;
		$post_title        = $post->post_title;
		$forms[ $post_id ] = ! empty( $post_title ) ? $post_title : '';
	}

	return $forms;
}

/**
 * Get Popup Settings
 *
 * @since 1.0.1
 */
function eacf7_get_popup_settings( $key = null, $default = null ) {
	$settings = (array) get_option( 'eacf7_settings', array() );

	$settings = isset( $settings['popup'] ) ? $settings['popup'] : '';

	if ( empty( $settings ) && ! empty( $default ) ) {
		return $default;
	}

	return ( ! empty( $settings->$key ) ) ? $settings->$key : $default;
}

/**
 * Get Unique Hash
 *
 * @since 1.0.1
 */
function eacf7_generate_hash() {
	$bytes = random_bytes( 16 );

	$bytes[6] = chr( ord( $bytes[6] ) & 0x0f | 0x40 );
	$bytes[8] = chr( ord( $bytes[8] ) & 0x3f | 0x80 );

	return vsprintf( '%s-%s-%s-%s-%s', str_split( bin2hex( $bytes ), 4 ) );
}

/**
 * Unique hash to base64
 *
 * @since 1.0.1
 */
function eacf7_hash_to_base64( $uuid ) {
	$uuid       = str_replace( '-', '', $uuid );
	$binaryData = hex2bin( $uuid );

	return base64_encode( $binaryData );
}

/**
 * Get all contact forms and their content
 *
 * @return array An array of contact forms and their content.
 * @since 1.0.1
 *
 */
function eacf7_get_forms_data() {
	$posts = get_posts(
		array(
			'post_type'   => 'wpcf7_contact_form',
			'numberposts' => - 1,
		)
	);

	$forms = array();

	foreach ( $posts as $post ) {
		$post_id = $post->ID;

		$meta_keys = array(
			'_form',
			'_mail',
			'_mail_2',
			'_messages',
			'_additional_settings',
			'_locale',
			'_hash',
			'eacf7_booking_data',
			'eacf7_conditional_rules',
			'eacf7_conversational_data',
			'eacf7_digital_signature_data',
			'eacf7_form_styler_data',
			'eacf7_multistep_data',
			'eacf7_pdfgenerator_data',
			'eacf7_post_submission_data',
			'eacf7_prepopulate_data',
			'eacf7_range_slider_data',
			'eacf7_redirection_data',
			'eacf7_repeater_data',
			'eacf7_save_data',
			'eacf7_submission_id_data',
			'eacf7_user_registration_data',
			'eacf7_checkout_data',
			'eacf7_integrations_data',
		);

		// Initialize form data with basic post details.
		$form_data = array_filter(
			array(
				'id'     => $post_id,
				'title'  => $post->post_title ?? '',
				'status' => $post->post_status ?? '',
			)
		);

		// Fetch and add meta values only if they exist.
		foreach ( $meta_keys as $key ) {
			$meta_value = get_post_meta( $post_id, $key, true );
			if ( ! empty( $meta_value ) ) {
				$form_data[ $key ] = $meta_value;
			}
		}

		// Add to the forms array.
		$forms[] = $form_data;
	}

	return $forms;
}

/**
 * Formats and sanitizes an entry array for a form.
 *
 * This function takes an associative array representing a form entry
 * and performs the following operations:
 * - Converts the 'id', 'form_id', and 'status' fields to integers.
 * - Sanitizes the 'form_name', 'form_data', 'created_at', and 'updated_at'
 *   fields as text.
 *
 * @param array $entry An associative array containing the form entry data.
 *
 * @return array The formatted and sanitized entry array.
 */
function eacf7_get_formatted_entry( $entry ) {
	if ( empty( $entry ) ) {
		return array();
	}

	$entry['id']         = intval( $entry['id'] );
	$entry['form_id']    = intval( $entry['form_id'] );
	$entry['form_name']  = sanitize_text_field( $entry['form_name'] );
	$entry['form_data']  = sanitize_text_field( $entry['form_data'] );
	$entry['status']     = intval( $entry['status'] );
	$entry['created_at'] = sanitize_text_field( $entry['created_at'] );
	$entry['updated_at'] = sanitize_text_field( $entry['updated_at'] );

	return $entry;
}

/**
 * Retrieves one or all entries from the database.
 *
 * If an ID is provided, retrieves the single entry with that ID.
 * If no ID is provided, retrieves all entries.
 *
 * @param int $id The ID of the entry to retrieve.
 *
 * @return array An array of entry data, or a single entry if $id is provided.
 */
function eacf7_get_entries( $id = false ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'eacf7_entries';

	if ( $id ) {
		$entry = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ), ARRAY_A );

		return eacf7_get_formatted_entry( $entry );
	}

	$entries = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );

	$formatted_entries = array();

	if ( ! empty( $entries ) ) {
		foreach ( $entries as $entry ) {
			$formatted_entries[] = eacf7_get_formatted_entry( $entry );
		}
	}

	if ( 0 === $id ) {
		return $formatted_entries[0];
	}

	return $formatted_entries;
}

/**
 * Retrieves all exportable data
 *
 * This function returns an array with all exportable data, including all form data,
 * all entries, and all settings.
 *
 * @return array An array of exportable data.
 */
function eacf7_get_all_export_data() {
	return array(
		'forms'    => eacf7_get_forms_data(),
		'entries'  => eacf7_get_entries(),
		'settings' => eacf7_get_settings(),
	);
}

/**
 * Check if the current page is the Contact Form 7 editor page.
 *
 * @return bool
 */
function eacf7_is_editor_page() {
	if ( ! is_admin() ) {
		return false;
	}

	$screen = get_current_screen();

	if ( ! $screen ) {
		return false;
	}

	// Check for "Add New" CF7 screen.
	if ( str_contains( $screen->base, '_page_wpcf7-new' ) ) {
		return true;
	}

	// Check for "Edit" CF7 screen.
	if ( 'toplevel_page_wpcf7' === $screen->base ) {
		$post = isset( $_GET['post'] ) ? sanitize_key( wp_unslash( $_GET['post'] ) ) : '';
		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
		
		if ( 'edit' === $action || ! empty( $post ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Check if the current page is the EACF7 Dashboard page.
 *
 * @return bool
 */
function eacf7_is_dashboard_page() {
	if ( ! is_admin() ) {
		return false;
	}

	$screen = get_current_screen();

	if ( ! $screen ) {
		return false;
	}

	// Check for "Add New" CF7 screen.
	if ( str_contains( $screen->base, '_page_eacf7' ) ) {
		return true;
	}

	return false;
}

/**
 * Get the current Contact Form 7 form ID.
 *
 * @return int|null The form ID if available, otherwise null.
 */
function eacf7_get_current_form_id() {
	$contact_form = \WPCF7_ContactForm::get_current();

	return ( $contact_form instanceof \WPCF7_ContactForm ) ? (int) $contact_form->id() : null;
}

