<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$key = ( !empty( $value['tag'] ) ? sanitize_text_field( $value['tag'] ) : null );
$label = ( isset( $value['label'] ) ? filter_var( $value['label'], FILTER_VALIDATE_BOOL ) : '' );
$column = ( isset( $value['column'] ) ? intval( $value['column'] ) : null );
$required = ( isset( $value['required'] ) ? filter_var( $value['required'], FILTER_VALIDATE_BOOLEAN ) : '' );
( $required ? $required = '*' : '' );
$data = '';
switch ( $key ) {
    case 'acceptance':
        ( $label ? $data .= esc_html__( 'Acceptance', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[acceptance' . $required . ' acceptance-946 optional] I agree to the terms and conditions. [/acceptance]';
        break;
    case 'eacf7_column':
        if ( 1 == $column ) {
            $data .= '[eacf7-row]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . '[/eacf7-row]' . PHP_EOL;
        } else {
            if ( 2 == $column ) {
                $data .= '[eacf7-row]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . "\t" . '[/eacf7-row]' . PHP_EOL;
            } else {
                if ( 3 == $column ) {
                    $data .= '[eacf7-row]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . "\t" . '[/eacf7-row]' . PHP_EOL;
                } else {
                    if ( 4 == $column ) {
                        $data .= '[eacf7-row]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . "\t" . '[/eacf7-row]' . PHP_EOL;
                    } else {
                        if ( 5 == $column ) {
                            $data .= '[eacf7-row]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . "\t" . '[/eacf7-row]' . PHP_EOL;
                        } else {
                            if ( 6 == $column ) {
                                $data .= '[eacf7-row]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . PHP_EOL . "\t\t" . '[eacf7-col col:' . $column . '] --your field code-- [/eacf7-col]' . "\t" . '[/eacf7-row]' . PHP_EOL;
                            }
                        }
                    }
                }
            }
        }
        break;
    case 'address':
        ( $label ? $data .= esc_html__( 'Address', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[address' . $required . ' address-581 required_fields:line1|city|state|zip|country format:international]';
        break;
    case 'checkbox':
        ( $label ? $data .= esc_html__( 'Checkbox', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[checkbox' . $required . ' checkbox-973 use_label_element "Option 1" "Option 2" "Option 3"]';
        break;
    case 'date':
        ( $label ? $data .= esc_html__( 'Date', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[date' . $required . ' date-356]';
        break;
    case 'menu':
        ( $label ? $data .= esc_html__( 'Menu', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[select' . $required . ' menu-732 "Beef" "Chicken" "Vegetable" "Rice"]';
        break;
    case 'email':
        ( $label ? $data .= esc_html__( 'Email', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[email' . $required . ' your-email]';
        break;
    case 'file':
        ( $label ? $data .= esc_html__( 'File', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[file' . $required . ' file-651 limit:10240 filetypes:jpeg|jpg|png|webp|pdf|doc|zip]';
        break;
    case 'file_upload':
        ( $label ? $data .= 'File Upload' . PHP_EOL . "\t" : '' );
        $data .= '[file_upload' . $required . ' file_upload-398 extensions:jpeg|jpg|png|webp|pdf|zip max_size:10 max_files:1]';
        break;
    case 'honeypot':
        ( $label ? $data .= 'Honeypot' . PHP_EOL . "\t" : '' );
        $data .= '[honeypot honeypot-529 placeholder:Please ignore it]';
        break;
    case 'math_captcha':
        ( $label ? $data .= esc_html__( 'Math Captcha', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[math_captcha' . $required . ' math_captcha-168 display:inline]';
        break;
    case 'number':
        ( $label ? $data .= esc_html__( 'Number', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[number' . $required . ' number-597]';
        break;
    case 'product_dropdown':
        ( $label ? $data .= esc_html__( 'Product Dropdown', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[product_dropdown' . $required . ' product_dropdown-664 product_order:default layout:dropdown]';
        break;
    case 'quiz':
        ( $label ? $data .= esc_html__( 'Quiz', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[quiz quiz-308 "What is the capital of Bangladesh?|Dhaka"]';
        break;
    case 'radio':
        ( $label ? $data .= esc_html__( 'Radio', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[radio radio-443 use_label_element default:1 "Option 1" "Option 2" "Option 3"]';
        break;
    case 'rating':
        ( $label ? $data .= esc_html__( 'Rating', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[rating' . $required . ' rating-390]';
        break;
    case 'submission_id':
        $data .= '[submission_id submission_id-430]';
        break;
    case 'submit':
        $data .= '[submit "Submit"]';
        break;
    case 'tel':
        ( $label ? $data .= esc_html__( 'Tel', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[tel' . $required . ' tel-601]';
        break;
    case 'text':
        ( $label ? $data .= esc_html__( 'Text', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[text' . $required . ' text-390]';
        break;
    case 'textarea':
        ( $label ? $data .= esc_html__( 'Textarea', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[textarea' . $required . ' textarea-541]';
        break;
    case 'country_list':
        ( $label ? $data .= esc_html__( 'Country', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[country_list' . $required . ' country_list-256]';
        break;
    case 'date_time':
        ( $label ? $data .= esc_html__( 'Date Time', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[date_time' . $required . ' date_time-399]';
        break;
    case 'digital_signature':
        ( $label ? $data .= esc_html__( 'Digital Signature', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[digital_signature' . $required . ' digital_signature-247]';
        break;
    case 'dynamic_text':
        ( $label ? $data .= esc_html__( 'Dynamic Text', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[dynamic_text' . $required . ' dynamic_text-321 visibility:show "url_param"]';
        break;
    case 'file_upload':
        ( $label ? $data .= esc_html__( 'File Upload', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[file_upload' . $required . ' file_upload-895 max_size:10M max_files:1]';
        break;
    case 'google_drive_upload':
        break;
    case 'image_upload':
        ( $label ? $data .= esc_html__( 'Image Upload', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[image_upload' . $required . ' image_upload-875 extensions:jpeg|jpg|png|webp max_files:1]';
        break;
    case 'leads_info':
        $data .= '[leads_info leads_info-705]';
        break;
    case 'phone':
        ( $label ? $data .= esc_html__( 'Phone', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[phone' . $required . ' phone-32]';
        break;
    case 'password':
        break;
    case 'range_slider':
        ( $label ? $data .= esc_html__( 'Range Slider', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[range_slider' . $required . ' range_slider-386 min:0 max:100 step:1 default:0]';
        break;
    case 'url':
        ( $label ? $data .= esc_html__( 'URL', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[url' . $required . ' url-182]';
        break;
    case 'section_break':
        $data .= '[section_break section_break-860 values:This is a section title "' . esc_html__( 'This is a section title', 'essential-addons-for-contact-form-7' ) . '"] ' . esc_html__( 'lorem ipsum doller sit ammet', 'essential-addons-for-contact-form-7' ) . ' [/section_break]';
        break;
    case 'post_title':
        break;
    case 'post_excerpt':
        break;
    case 'post_content':
        break;
    case 'post_thumbnail':
        break;
    case 'post_taxonomies':
        break;
    case 'booking_date':
        break;
    case 'booking_time':
        break;
    case 'mask_input':
        break;
    case 'rich_text':
        break;
    case 'recaptcha':
        ( $label ? $data .= esc_html__( 'Google Captcha', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[recaptcha theme:light size:normal]';
        break;
    case 'hcaptcha':
        ( $label ? $data .= esc_html__( 'hCaptcha', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[hcaptcha hcaptcha-767 theme:light]';
        break;
    case 'cloudflare_turnstile':
        ( $label ? $data .= esc_html__( 'Cloudflare Turnstile', 'essential-addons-for-contact-form-7' ) . PHP_EOL . "\t" : '' );
        $data .= '[cloudflare_turnstile cloudflare_turnstile-166 theme:light]';
        break;
    case 'hook':
        break;
    case 'color_picker':
        break;
    case 'eacf7_repeater':
        break;
    case 'save':
        break;
    case 'shortcode':
        break;
    case 'image_choice':
        break;
    case 'custom_html':
        break;
    case 'conversational_start':
        break;
    case 'conversational_end':
        break;
    default:
        $data = esc_html__( 'Something wrong! Please try correct field with proper option.', 'essential-addons-for-contact-form-7' );
        break;
}
/**
 * Exclude Label
 * @since 1.0.0
 */
if ( 'eacf7_column' !== $key && $label ) {
    $data = '<label>' . $data . '</label>' . PHP_EOL;
}