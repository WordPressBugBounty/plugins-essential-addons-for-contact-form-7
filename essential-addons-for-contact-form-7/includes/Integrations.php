<?php

namespace EACF7;

class Integrations {
    private static $instance = null;

    public function __construct() {
        // Add integration settings to localize script
        add_filter( 'eacf7_localize_data', array($this, 'add_localize_data') );
        // Add integration data to the form footer
        add_filter( 'wpcf7_form_elements', array($this, 'print_integrations_js_data') );
        // Save integration data
        add_action( 'wpcf7_after_save', array($this, 'save_integrations_data') );
        // load integration files
        if ( $this->is_active( 'telegram' ) ) {
            include_once EACF7_INCLUDES . '/integrations/Telegram.php';
        }
        if ( $this->is_active( 'mailchimp' ) ) {
            include_once EACF7_INCLUDES . '/integrations/Mailchimp.php';
        }
        if ( $this->is_active( 'webhooks' ) ) {
            include_once EACF7_INCLUDES . '/integrations/Webhook.php';
        }
        if ( $this->is_active( 'zapier' ) ) {
            include_once EACF7_INCLUDES . '/integrations/Zapier.php';
        }
        if ( $this->is_active( 'pabbly' ) ) {
            include_once EACF7_INCLUDES . '/integrations/Pabbly.php';
        }
    }

    public function add_localize_data( $data ) {
        if ( eacf7_is_editor_page() ) {
            $data['integrationsData'] = $this->get_integrations_data();
        }
        return $data;
    }

    public function print_integrations_js_data( $form_html ) {
        $form_id = eacf7_get_current_form_id();
        $data = $this->get_integrations_data( $form_id );
        if ( empty( $data ) ) {
            return $form_html;
        }
        // Encode PHP array/object to JSON for safe JS usage
        $json_data = wp_json_encode( $data );
        $js = <<<HTML
<script>
var eacf7IntegrationsData_{$form_id} = {$json_data};
</script>
HTML;
        return $form_html . $js;
    }

    public function get_integrations_data( $form_id = false ) {
        if ( !$form_id ) {
            $form_id = eacf7_get_current_form_id();
        }
        $data = get_post_meta( $form_id, 'eacf7_integrations_data', true );
        return ( !empty( $data ) ? $data : [] );
    }

    /**
     * Save Integrations data
     */
    public function save_integrations_data( $post ) {
        $post_id = $post->id();
        if ( empty( $_POST['eacf7_integrations_data'] ) ) {
            return;
        }
        $data = sanitize_text_field( stripslashes( $_POST['eacf7_integrations_data'] ) );
        $data = json_decode( $data, true );
        update_post_meta( $post_id, 'eacf7_integrations_data', $data );
    }

    public function is_active( $key ) {
        $integrations = eacf7_get_settings( 'integrations', [] );
        return in_array( $key, $integrations );
    }

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}

Integrations::instance();