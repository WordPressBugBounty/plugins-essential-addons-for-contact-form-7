<?php

namespace EACF7;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Email_Summaries
 */
class Email_Summaries {
    /**
     * @var null
     */
    protected static $instance = null;

    /**
     * Constructor.
     */
    public function __construct() {
        add_filter('cron_schedules', array($this, 'cron_schedules'));

        $enable_email_report = eacf7_get_settings('emailSummaries', false);
        $interval            = eacf7_get_settings('emailSummariesInterval', 'weekly');

        if (! $enable_email_report) {
            wp_clear_scheduled_hook('eacf7_email_summaries_monthly_report');
            wp_clear_scheduled_hook('eacf7_email_summaries_weekly_report');
            wp_clear_scheduled_hook('eacf7_email_summaries_daily_report');

            return;
        }

        if ('monthly' == $interval) {
            wp_clear_scheduled_hook('eacf7_email_summaries_daily_report');
            wp_clear_scheduled_hook('eacf7_email_summaries_weekly_report');
        } else if ('weekly' == $interval) {
            wp_clear_scheduled_hook('eacf7_email_summaries_daily_report');
            wp_clear_scheduled_hook('eacf7_email_summaries_monthly_report');
        } else {
            wp_clear_scheduled_hook('eacf7_email_summaries_weekly_report');
            wp_clear_scheduled_hook('eacf7_email_summaries_monthly_report');
        }

        add_action('admin_init', array($this, 'activate_email_reporting'));
        add_action("eacf7_email_summaries_{$interval}_report", array($this, 'send_report'));
    }

    /**
     * Modify the available cron schedules.
     *
     * Add a monthly schedule of 2,592,000 seconds, which is equivalent to 30 days.
     *
     * @param array $schedules The available cron schedules.
     *
     * @return array The modified array of available cron schedules.
     */
    public function cron_schedules($schedules) {
        $schedules['monthly'] = array(
            'interval' => 2592000,
            'display'  => __('Once Monthly', 'essential-addons-for-contact-form-7'),
        );

        return $schedules;
    }

    /**
     * Schedule an email reporting event based on the specified interval.
     *
     * This function retrieves the email summaries interval setting and calculates the
     * next scheduled time for the event to occur. It checks if the event is already
     * scheduled and, if not, schedules it to run at 9 AM on the appropriate day.
     *
     * The interval can be 'daily', 'weekly', or 'monthly', and the function computes
     * the next occurrence accordingly.
     */
    public function activate_email_reporting() {
        $interval = eacf7_get_settings('emailSummariesInterval', 'weekly');

        $datetime = strtotime("next monday 9AM", current_time('timestamp'));
        if ('daily' == $interval) {
            $datetime = strtotime("tomorrow 9AM", current_time('timestamp'));
        } elseif ('monthly' == $interval) {
            $datetime = strtotime("first day of next month 9AM", current_time('timestamp'));
        }

        if (! wp_next_scheduled("eacf7_email_summaries_{$interval}_report")) {
            wp_schedule_event($datetime, $interval, "eacf7_email_summaries_{$interval}_report");
        }
    }

    public function send_report() {
        $enable_email_report = eacf7_get_settings('emailSummaries', false);
        $interval            = eacf7_get_settings('emailSummariesInterval', 'weekly');

        if (! $enable_email_report) {
            wp_clear_scheduled_hook('eacf7_email_summaries_monthly_report');
            wp_clear_scheduled_hook('eacf7_email_summaries_weekly_report');
            wp_clear_scheduled_hook('eacf7_email_summaries_daily_report');

            return;
        }

        $length = 7;
        if ('monthly' == $interval) {
            $length = 30;
        } elseif ('daily' == $interval) {
            $length = 1;
        }

        $start_date = date('Y-m-d', strtotime("-{$length} days"));
        $end_date   = date('Y-m-d');

        $forms      = $this->get_top_forms($start_date, $end_date);
        $logs       = $this->get_logs($start_date, $end_date);

        $send_to = eacf7_get_settings('emailSummariesSendTo', 'admin');

        if( 'custom' == $send_to ) {
            $send_to = eacf7_get_settings('emailSummariesRecipient', '');
        }else{
            $send_to = get_option('admin_email');
        }

        $custom_subject = eacf7_get_settings('emailSummariesCustomSubject', false);
        $subject = eacf7_get_settings('emailSummariesSubject', '');

        if( $custom_subject && ! empty( $subject) ){
            $subject = $subject . ' ' . sprintf(_n(' %s day', ' %s days', $length, 'essential-addons-for-contact-form-7'), $length);
        }else{
            $subject = __('Essential Addons for Contact Form 7 Email Report of last ', 'essential-addons-for-contact-form-7') . sprintf(_n(' %s day', ' %s days', $length, 'essential-addons-for-contact-form-7'), $length);
        }

        ob_start();
        include_once EACF7_INCLUDES . '/views/email-report.php';
        $email_message = ob_get_clean();

        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($send_to, $subject, $email_message, $headers);
    }

    /**
     * Retrieve logs with entry counts for each day within the given date range.
     *
     * This function queries the database to count the number of entries
     * created on each day between the specified start and end dates. If there
     * are days with no entries, they are included in the results with a count of zero.
     *
     * @param string $start_date The start date in 'Y-m-d' format.
     * @param string $end_date   The end date in 'Y-m-d' format.
     * @return array An array of logs, each containing a 'date' and 'value' (entry count).
     */
    public function get_logs($start_date, $end_date) {
        global $wpdb;

        $table_name    = $wpdb->prefix . 'eacf7_entries';

        $start_date .= ' 00:00:00';
        $end_date   .= ' 23:59:59';

        // Prepare the query.
        $query = $wpdb->prepare(
            "SELECT 
                DATE_FORMAT(created_at, '%%d-%%m-%%Y') AS date,
                COUNT(*) AS value
            FROM {$table_name}
            WHERE created_at BETWEEN %s AND %s
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC",
            $start_date,
            $end_date
        );

        // phpcs:ignore
        $results = $wpdb->get_results($query, ARRAY_A);

        $logs = array();
        foreach ($results as $row) {
            $logs[] = array(
                'date'  => $row['date'],
                'value' => $row['value'] ?? 0,
            );
        }

        // Add dates with no data.
        $current_date = $start_date;
        while ($current_date <= $end_date) {
            if (! in_array($current_date, array_column($logs, 'date'))) {
                $logs[] = array(
                    'date'  => date('d-m-Y', strtotime($current_date)), // phpcs:ignore
                    'value' => 0,
                );
            }
            $current_date = date('d-m-Y', strtotime($current_date . ' +1 day'));
        }

        // Sort the chart data by date.
        usort(
            $logs,
            function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            }
        );

        // Return complete data with missing dates filled.
        return $logs;
    }

    /**
     * Get top forms sorted by submission count between given dates.
     *
     * @param string $start_date Start date in 'd-m-Y' format.
     * @param string $end_date   End date in 'd-m-Y' format.
     *
     * @return array
     */
    public function get_top_forms($start_date, $end_date) {
        global $wpdb;

        $table_name     = $wpdb->prefix . 'eacf7_entries';
        $start_date     .= ' 00:00:00';
        $end_date       .= ' 23:59:59';

        $query = $wpdb->prepare(
            "SELECT form_name AS title, COUNT(*) AS count
            FROM {$table_name}
            WHERE created_at BETWEEN %s AND %s
            GROUP BY form_name
            ORDER BY DATE(created_at) ASC",
            $start_date,
            $end_date
        );

        // phpcs:ignore
        $results = $wpdb->get_results($query, ARRAY_A);

        $forms = array();
        foreach ($results as $row) {
            $forms[] = array(
                'form'  => $row['title'],
                'count' => $row['count'] ?? 0,
            );
        }

        // Sort the chart data by date.
        usort($forms, function ($a, $b) {
            return $b['count'] - $a['count'];
        });

        // Return complete data with missing dates filled.
        return $forms;
    }

    /**
     * @return Email_Summaries|null
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

// Initialize the Email_Summaries class.
Email_Summaries::instance();
