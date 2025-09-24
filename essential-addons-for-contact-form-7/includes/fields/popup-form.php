<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Popup_Form {
    /**
     * @var null
     */
    protected static $instance = null;

    public function __construct() {
        add_shortcode( 'eacf7_popup', [ $this, 'render_shortcode' ] );

        add_action( 'wp_footer', array( $this, 'custom_css' ) );

        add_action( 'wpcf7_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_filter( 'eacf7_localize_data', array( $this, 'add_localize_data' ) );

        // Fire Contact Form
        add_action( 'wp_ajax_eacf7_fire_contact_form', array( $this, 'fire_contact_form' ) );
        add_action( 'wp_ajax_nopriv_eacf7_fire_contact_form', array( $this, 'fire_contact_form' ) );
    }

    public function render_shortcode( $atts, $content = null ) {
        $form_id             = isset( $atts['form_id'] ) ? intval( $atts['form_id'] ) : '';
        $btn_text            = ! empty( eacf7_get_popup_settings( 'buttonText' ) ) ? sanitize_text_field( eacf7_get_popup_settings( 'buttonText' ) ) : __( 'Leave a Message', 'essential-addons-for-contact-form-7' );
        $btn_icon            = isset( $atts['btn_icon'] ) ? sanitize_text_field( $atts['btn_icon'] ) : '';
        $btn_style           = isset( $atts['btn_style'] ) ? intval( $atts['btn_style'] ) : 'global';
        $btn_size            = isset( $atts['btn_size'] ) ? sanitize_text_field( $atts['btn_size'] ) : sanitize_text_field( eacf7_get_popup_settings( 'buttonSize', 'medium' ) );
        $btn_class           = isset( $atts['btn_class'] ) ? sanitize_text_field( $atts['btn_class'] ) : '';
        $btn_alignment       = isset( $atts['btn_alignment'] ) ? sanitize_text_field( $atts['btn_alignment'] ) : 'left';
        $popup_outside_click = isset( $atts['popup_outside_click'] ) ? intval( $atts['popup_outside_click'] ) : intval( eacf7_get_popup_settings( 'popupOutsideClick', 1 ) );
        $popup_width         = isset( $atts['popup_width'] ) ? intval( $atts['popup_width'] ) : intval( eacf7_get_popup_settings( 'popupWidth', 500 ) ) . sanitize_text_field( eacf7_get_popup_settings( 'popupWidthUnit', 'px' ) );
        $popup_class         = isset( $atts['popup_class'] ) ? intval( $atts['popup_class'] ) : '';

        switch ( $btn_icon ) {
            case 'contact':
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none"><path d="M0 5.79365C0.06875 5.61865 0.14375 5.44365 0.2 5.30615C2.61875 7.6999 5.03125 10.0937 7.45625 12.4999C5.03125 14.8999 2.61875 17.2937 0.2 19.6874C0.14375 19.5437 0.06875 19.3687 0 19.1999C0 14.7312 0 10.2624 0 5.79365Z" fill="white"/><path d="M24.0002 19.2C23.9314 19.375 23.8564 19.5437 23.8002 19.6875C21.3814 17.2937 18.9689 14.9 16.5439 12.5C18.9689 10.1 21.3814 7.70625 23.8002 5.3125C23.8564 5.46875 23.9252 5.65625 23.9939 5.84375C24.0002 10.2937 24.0002 14.75 24.0002 19.2Z" fill="white"/><path d="M22.7877 4.26866C22.4189 4.63116 22.0627 4.97491 21.7127 5.31866C18.7002 8.33741 15.6814 11.3499 12.6877 14.3874C12.3064 14.7749 11.7002 14.7749 11.3127 14.3874C8.0252 11.0624 4.7127 7.75616 1.4127 4.44366C1.35645 4.38741 1.30645 4.33116 1.2002 4.21866C1.54395 4.16241 1.8252 4.08116 2.11895 4.06866C2.6752 4.04366 3.2252 4.06241 3.78145 4.06241C9.7752 4.06241 15.7627 4.06241 21.7564 4.06241C22.0939 4.05616 22.4314 4.10616 22.7877 4.26866Z" fill="white"/><path d="M8.49961 13.5685C9.10586 14.1685 9.74961 14.8122 10.3996 15.456C11.1059 16.1497 12.1059 16.3372 12.9621 15.906C13.2121 15.781 13.4434 15.5872 13.6496 15.3935C14.2746 14.7935 14.8809 14.1685 15.4871 13.556C15.4996 13.5435 15.5121 13.5372 15.4996 13.5435C17.9121 15.931 20.3184 18.3247 22.7809 20.7622C22.5059 20.8247 22.2746 20.8872 22.0371 20.931C21.9496 20.9497 21.8496 20.9372 21.7559 20.9372C15.2496 20.9372 8.74336 20.9372 2.23711 20.931C1.91211 20.931 1.58086 20.8497 1.19336 20.7997C3.67461 18.3497 6.08711 15.9622 8.49961 13.5685Z" fill="white"/></svg>';
                break;

            case 'message':
                $icon = '<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M2.25 5.5C2.25 4.77065 2.53973 4.07118 3.05546 3.55546C3.57118 3.03973 4.27065 2.75 5 2.75H19C19.7293 2.75 20.4288 3.03973 20.9445 3.55546C21.4603 4.07118 21.75 4.77065 21.75 5.5V15.5C21.75 16.2293 21.4603 16.9288 20.9445 17.4445C20.4288 17.9603 19.7293 18.25 19 18.25H7.961C7.581 18.25 7.222 18.423 6.985 18.72L4.655 21.633C3.857 22.629 2.25 22.066 2.25 20.79V5.5Z" fill="#1E62B9"/></svg>';
                break;

            case 'newsletter':
                $icon = '<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_464_807)"><path d="M16.0312 24.4998C15.9062 24.4998 15.7812 24.4998 15.6562 24.4998C15.4937 24.3436 15.2875 24.2123 15.175 24.0186C13.5563 21.2561 11.95 18.4873 10.3375 15.7186C10.1187 15.3498 10.1437 15.0186 10.4 14.6811C10.9 14.0248 11.3875 13.3561 11.8813 12.6936C11.9125 12.6498 11.9375 12.5998 11.9625 12.5498C11.8875 12.5623 11.8313 12.5936 11.7875 12.6311C11.1313 13.1186 10.4688 13.5998 9.8125 14.0936C9.475 14.3498 9.14375 14.3748 8.775 14.1561C6.0875 12.5873 3.39375 11.0186 0.69375 9.45605C0.4125 9.29355 0.15 9.13105 0 8.8373C0 8.68105 0 8.5248 0 8.36855C0.16875 8.06855 0.4125 7.8998 0.74375 7.79355C2.93125 7.09355 5.10625 6.36855 7.2875 5.65605C12.0938 4.0873 16.9 2.51855 21.7062 0.943555C22.1437 0.799805 22.575 0.637305 23.0125 0.487305C23.1375 0.487305 23.2812 0.487305 23.3875 0.487305C23.6875 0.593555 23.8875 0.799805 24 1.0998C24 1.24355 24 1.34357 23.9688 1.49982C23.9375 1.62482 23.9 1.69355 23.8687 1.78105C21.475 9.09355 19.0875 16.4061 16.7062 23.7186C16.5875 24.0998 16.4188 24.3873 16.0312 24.4998Z" fill="white"/><path d="M2.44414 22.831C2.13789 22.831 1.90039 22.6935 1.75664 22.4247C1.61289 22.156 1.63164 21.881 1.80039 21.6247C1.83789 21.5685 1.88789 21.5122 1.93789 21.4685C3.61914 19.7872 5.29414 18.1122 6.97539 16.431C7.21289 16.1935 7.48164 16.0935 7.80664 16.1872C8.30664 16.331 8.51289 16.931 8.21914 17.3622C8.16914 17.4372 8.10039 17.506 8.03164 17.5747C6.38164 19.2247 4.72539 20.8747 3.07539 22.531C2.90039 22.6997 2.70664 22.831 2.44414 22.831Z" fill="white"/><path d="M7.4192 23.4246C7.0817 23.3809 6.8442 23.2434 6.70045 22.9621C6.55045 22.6684 6.5692 22.3684 6.7942 22.1371C7.4942 21.4121 8.2067 20.6996 8.92545 19.9996C9.2192 19.7184 9.67545 19.7559 9.96295 20.0434C10.2442 20.3246 10.2817 20.7934 10.0067 21.0809C9.31295 21.7996 8.6067 22.4996 7.88795 23.1996C7.76295 23.3121 7.57545 23.3496 7.4192 23.4246Z" fill="white"/><path d="M3.90646 14.3125C4.23146 14.3188 4.47521 14.4687 4.61271 14.7625C4.75646 15.0687 4.70646 15.3625 4.47521 15.6062C4.15021 15.95 3.80646 16.2812 3.46896 16.6187C3.13771 16.95 2.80646 17.2812 2.47521 17.6125C2.11896 17.9625 1.65021 17.975 1.33146 17.65C1.01896 17.3312 1.03771 16.8625 1.38771 16.5063C2.02521 15.8625 2.66896 15.225 3.30646 14.5875C3.48146 14.425 3.66271 14.3125 3.90646 14.3125Z" fill="white"/></g><defs><clipPath id="clip0_464_807"><rect width="24" height="24" fill="white" transform="translate(0 0.5)"/></clipPath></defs></svg>';
                break;

            case 'quote':
                $icon = '<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_465_1062)"><path d="M12.1879 0.5C12.3254 0.5375 12.4629 0.56875 12.6004 0.6125C13.0629 0.75 13.4691 0.99375 13.8129 1.3375C14.4316 1.95625 15.0504 2.575 15.6691 3.19375C16.2316 3.75625 16.5004 4.44375 16.5004 5.2375C16.5004 6.91875 16.5004 8.59375 16.5004 10.275C16.5004 10.8687 16.1129 11.2812 15.5691 11.2875C15.0191 11.2875 14.6316 10.875 14.6254 10.2875C14.6254 8.625 14.6191 6.9625 14.6254 5.29375C14.6254 4.9625 14.5191 4.6875 14.2816 4.45625C13.6941 3.88125 13.1191 3.3 12.5441 2.71875C12.3129 2.48125 12.0379 2.375 11.7066 2.375C8.77539 2.38125 5.85039 2.375 2.91914 2.375C2.25664 2.375 1.87539 2.75625 1.87539 3.41875C1.87539 9.475 1.87539 15.525 1.87539 21.5812C1.87539 22.2437 2.25664 22.6187 2.92539 22.6187C4.16914 22.6187 5.40664 22.6187 6.65039 22.6187C7.11289 22.6187 7.46914 22.925 7.56289 23.375C7.65039 23.775 7.43789 24.2062 7.06289 24.3937C6.98789 24.4312 6.91289 24.4625 6.83789 24.4937C6.80664 24.4937 6.77539 24.4937 6.74414 24.4937C6.66914 24.4812 6.60039 24.45 6.52539 24.45C5.25664 24.45 3.98789 24.45 2.71914 24.45C2.65664 24.45 2.58789 24.4812 2.52539 24.4937C2.46289 24.4937 2.40039 24.4937 2.33789 24.4937C2.28789 24.4688 2.23789 24.4313 2.18164 24.4188C1.06914 24.125 0.362891 23.4187 0.0691406 22.3062C0.0566406 22.25 0.0191406 22.2 -0.00585938 22.15C-0.00585938 22.0688 -0.00585938 21.9937 -0.00585938 21.9125C0.0128906 21.825 0.0378906 21.7375 0.0378906 21.6437C0.0378906 15.5187 0.0378906 9.3875 0.0378906 3.2625C0.0378906 3.18125 0.00664063 3.1 -0.00585938 3.01875C-0.00585938 2.95625 -0.00585938 2.89375 -0.00585938 2.83125C0.0253906 2.7625 0.0628906 2.7 0.0816406 2.63125C0.319141 1.75625 0.856641 1.125 1.68164 0.74375C1.90664 0.6375 2.15664 0.58125 2.39414 0.5C5.65664 0.5 8.92539 0.5 12.1879 0.5Z" fill="white"/><path d="M10.8755 24.5002C10.8068 24.469 10.7443 24.4315 10.6755 24.4127C9.58805 24.0502 9.0568 22.844 9.56305 21.819C10.0193 20.8877 10.5005 19.9752 10.9943 19.0627C11.1255 18.819 11.313 18.594 11.5068 18.4002C14.0568 15.8377 16.613 13.2815 19.1755 10.7252C20.7068 9.20024 23.238 9.81899 23.8755 11.8627C23.9193 12.0002 23.963 12.1315 24.0005 12.269C24.0005 12.569 24.0005 12.8627 24.0005 13.1627C23.9755 13.2127 23.938 13.2627 23.9255 13.319C23.8005 13.8752 23.5193 14.3377 23.1193 14.7377C20.5818 17.2752 18.0443 19.8127 15.5005 22.3502C15.3068 22.544 15.0818 22.7377 14.838 22.8627C13.7443 23.4252 12.638 23.9565 11.538 24.5065C11.313 24.5002 11.0943 24.5002 10.8755 24.5002ZM11.2568 22.6002C11.3318 22.569 11.388 22.5565 11.4318 22.5315C12.2193 22.1252 13.013 21.719 13.7943 21.3065C13.9505 21.2252 14.0943 21.1065 14.2193 20.9815C16.7318 18.4752 19.2443 15.9627 21.7568 13.4502C21.8193 13.3877 21.8818 13.3315 21.9318 13.2627C22.338 12.7315 22.0943 11.9815 21.4505 11.794C21.0568 11.6815 20.738 11.8127 20.4568 12.094C17.963 14.5815 15.4755 17.0627 12.988 19.5627C12.7818 19.769 12.6068 20.019 12.4693 20.2752C12.0568 21.0315 11.6693 21.8002 11.2568 22.6002Z" fill="white"/><path d="M0 3.03125C0.01875 3.1125 0.04375 3.19375 0.04375 3.275C0.04375 9.4 0.04375 15.5312 0.04375 21.6562C0.04375 21.7437 0.0125 21.8312 0 21.925C0 15.625 0 9.33125 0 3.03125Z" fill="white"/><path d="M2.53125 24.4998C2.59375 24.4811 2.6625 24.4561 2.725 24.4561C3.99375 24.4561 5.2625 24.4561 6.53125 24.4561C6.60625 24.4561 6.675 24.4873 6.75 24.4998C5.34375 24.4998 3.9375 24.4998 2.53125 24.4998Z" fill="white"/><path d="M8.26257 7.625C9.41882 7.625 10.5751 7.625 11.7313 7.625C12.3251 7.625 12.7438 8.00625 12.7501 8.55C12.7563 9.10625 12.3313 9.5 11.7251 9.5C9.40632 9.5 7.08757 9.5 4.76882 9.5C4.16882 9.5 3.74382 9.1 3.75007 8.55C3.75632 8.00625 4.17507 7.625 4.77507 7.625C5.93757 7.625 7.10007 7.625 8.26257 7.625Z" fill="white"/><path d="M8.22492 13.25C7.05617 13.25 5.88117 13.2563 4.71242 13.25C4.03742 13.2437 3.59367 12.6312 3.79992 12.0125C3.93117 11.6187 4.28117 11.375 4.73742 11.375C6.19992 11.375 7.65617 11.375 9.11867 11.375C9.99367 11.375 10.8687 11.375 11.7437 11.375C12.3312 11.375 12.7499 11.7688 12.7499 12.3125C12.7499 12.8563 12.3312 13.25 11.7374 13.25C10.5687 13.25 9.39992 13.25 8.22492 13.25Z" fill="white"/><path d="M6.39403 15.1248C6.93778 15.1248 7.48778 15.1248 8.03153 15.1248C8.58153 15.1248 8.99403 15.5248 9.00028 16.0498C9.00653 16.5748 8.59403 16.9936 8.04403 16.9936C6.92528 16.9998 5.81278 16.9998 4.69403 16.9936C4.15028 16.9873 3.73778 16.5686 3.75028 16.0436C3.75653 15.5248 4.16903 15.1248 4.70653 15.1248C5.26903 15.1186 5.83153 15.1248 6.39403 15.1248Z" fill="white"/></g><defs><clipPath id="clip0_465_1062"><rect width="24" height="24" fill="white" transform="translate(0 0.5)"/></clipPath></defs></svg>';
                break;

            default:
                $icon = '';
                break;
        }

        $data = shortcode_atts( array(
                'form_id'             => $form_id,
                'btn_icon'            => $icon,
                'btn_style'           => $btn_style,
                'btn_size'            => $btn_size,
                'btn_class'           => $btn_class,
                'btn_alignment'       => $btn_alignment,
                'popup_outside_click' => $popup_outside_click,
                'popup_width'         => $popup_width,
                'popup_class'         => $popup_class,
                'text'                => $btn_text,
        ), $atts );

        ob_start();
        ?>
        <div class="eacf7-popup-form-button-wrap <?php echo esc_attr( $btn_alignment ); ?>">
            <button
                    type="button"
                    class="button eacf7-button eacf7-popup-form-button style-<?php echo esc_attr( $data['btn_style'] ) . ' ' . esc_attr( $data['btn_class'] ) . ' ' . esc_attr( $data['btn_size'] ); ?>"
                    id="eacf7-button-<?php echo esc_attr( $data['form_id'] ); ?>"
                    data-id="<?php echo esc_attr( $data['form_id'] ); ?>"
                    data-click="<?php echo esc_attr( $data['popup_outside_click'] ); ?>"
                    data-width="<?php echo esc_attr( $data['popup_width'] ); ?>"
                    data-class="<?php echo esc_attr( $data['popup_class'] ); ?>">
                <?php
                $allow_html = array(
                        'svg'  => array(
                                'fill'    => array(),
                                'width'   => array(),
                                'height'  => array(),
                                'viewBox' => array(),
                                'xmlns'   => array(),
                        ),
                        'path' => array(
                                'd'    => array(),
                                'fill' => array(),
                        )
                );
                echo ! empty( $icon ) ? wp_kses( $icon, $allow_html ) : '';
                ?>
                <?php echo esc_html( $data['text'] ) ?>
            </button>
        </div>
        <?php
        return ob_get_clean();
    }

    public function custom_css() {
        $text_color       = eacf7_get_popup_settings( 'buttonTextColor', '#fff' );
        $text_hover_color = eacf7_get_popup_settings( 'buttonTextHoverColor', '#fff' );
        $bg_color         = eacf7_get_popup_settings( 'buttonBgColor', '#1e62b9' );
        $bg_hover_color   = eacf7_get_popup_settings( 'buttonBgHoverColor', '#1e62b9' );
        $css              = '.eacf7-popup-form-button.style-global{';
        if ( ! empty( $text_color ) ) {
            $css .= 'color:' . $text_color . ' !important;';
        }
        if ( ! empty( $bg_color ) ) {
            $css .= 'background:' . $bg_color . ' !important;';
            $css .= 'border-color:' . $bg_color . ' !important;';
        }
        $css .= '}';

        $css .= '.eacf7-popup-form-button.style-global svg, .eacf7-popup-form-button.style-global svg path{';
        if ( ! empty( $text_color ) ) {
            $css .= 'fill:' . $text_color . ' !important;';
        }
        $css .= '}';

        $css .= '.eacf7-popup-form-button.style-global:hover svg, .eacf7-popup-form-button.style-global:hover svg path{';
        if ( ! empty( $text_hover_color ) ) {
            $css .= 'fill:' . $text_hover_color . ' !important;';
        }
        $css .= '}';

        $css .= '.eacf7-popup-form-button.style-global:hover{';
        if ( ! empty( $text_hover_color ) ) {
            $css .= 'color:' . $text_hover_color . ' !important;';
        }

        if ( ! empty( $bg_hover_color ) ) {
            $css .= 'background:' . $bg_hover_color . ' !important;';
            $css .= 'border-color:' . $bg_hover_color . ' !important;';
        }
        $css .= '}';
        ?>
        <style>
            <?php echo esc_html($css); ?>
        </style>
        <?php
    }

    public function enqueue_scripts() {
        wp_enqueue_style( 'eacf7-swal', EACF7_URL . '/assets/vendor/sweetalert2/sweetalert2.min.css', array( 'eacf7-frontend' ), '11.12.3' );

        wp_enqueue_script( 'eacf7-swal', EACF7_URL . '/assets/vendor/sweetalert2/sweetalert2.min.js', array( 'eacf7-frontend' ), '11.12.3', true );
    }

    public function add_localize_data($data) {
        if( ! is_admin() ){
            $data['settings'] = eacf7_get_settings();
        }

        return $data;
    }

    /**
     * Popup Contact Form Ajax
     * @since 1.0.0
     * @author monzuralam
     */
    public function fire_contact_form() {
        // Check nonce
        if ( ! check_ajax_referer( 'eacf7', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'essential-addons-for-contact-form-7' ) );
        }

        $id = ! empty( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;

        $shortcode = apply_filters( 'eacf7_shortcode', '[contact-form-7 id="' . esc_attr( $id ) . '"]' );

        $form = do_shortcode( $shortcode );

        if ( ! empty( $form ) ) {
            wp_send_json_success( $form );
        }
    }

    /**
     * @return Popup_Form|null
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

Popup_Form::instance();
