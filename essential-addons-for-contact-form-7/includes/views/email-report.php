<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

?>

<div class="email-wrap">
    <table width="100%" border="0" align="center">
        <tbody>
        <tr>
            <td height="25" style="background:#1e62b9;padding:15px 5%;font-size:1px;border-collapse:collapse;margin:0">
                <p style="margin-top:0;margin-bottom:0;text-align: center">
                    <span style="color:#fff;font-size:20px;font-family:inherit;font-weight:bold;text-transform:uppercase;text-decoration:initial;line-height:40px;letter-spacing:normal"> <?php esc_html_e('Essential Addons for Contact Form 7 Statistics of last', 'essential-addons-for-contact-form-7'); ?> <?php printf( _n( '%s day', '%s days', $length, 'essential-addons-for-contact-form-7' ), $length ); ?> :</span>
                </p>
            </td>
        </tr>

        <tr>
            <td height="25"
                style="background:#1e62b9;padding:0;font-size:1px;border-collapse:collapse;margin:0;height:5px"></td>
        </tr>
        </tbody>
    </table>

    <!-- Top Contact Forms -->
    <table align="center">
        <tr>
            <td data-text="Titles" data-font="Primary" align="center" valign="middle"
                style="font-family: Poppins, sans-serif; color: #1e62b9; font-size: 37px; line-height: 47px; font-weight: 600; letter-spacing: 0px; padding: 40px 0;"
                contenteditable="true" data-gramm="false">
				<?php _e( 'Most Submitted Contact Form 7 Forms', 'essential-addons-for-contact-form-7' ); ?>
            </td>
        </tr>
    </table>

    <table cellpadding="0" cellspacing="0" style="width:100%;font-family:HelveticaNeue,Roboto,sans-serif;font-size:15px">
        <thead>
        <tr>
            <th style="padding:10px;border-bottom:1px solid #e5e5e5;border-right:1px solid #e5e5e5;text-align:left;font-weight:normal;color:#333;background:#f5f5f5">
                <b><?php _e( 'Form Title', 'essential-addons-for-contact-form-7' ); ?>:</b>
            </th>
            <th style="padding:10px;border-bottom:1px solid #e5e5e5;border-right:1px solid #e5e5e5;text-align:left;font-weight:normal;color:#333;background:#f5f5f5">
                <b><?php _e( 'Total Submission Count', 'essential-addons-for-contact-form-7' ); ?>:</b>
            </th>
        </tr>
        </thead>
        <tbody>
		<?php
            if ( ! empty( $forms ) ) {
                foreach ( $forms as $form ) { error_log( print_r($form, true) ); ?>
                    <tr>
                        <td style="padding:8px;background:#fff;border:1px solid #eee">
                            <?php echo esc_html( $form['form'] ); ?>
                        </td>
                        <td style="padding:8px;background:#fff;border:1px solid #eee">
                            <?php echo esc_html( $form['count'] ); ?>
                        </td>
                    </tr>
                <?php }
            }
        ?>
        </tbody>
    </table>

    <!-- Count by date -->
    <table align="center">
        <tr>
            <td data-text="Titles" data-font="Primary" align="center" valign="middle"
                style="font-family: Poppins, sans-serif; color: #1e62b9; font-size: 37px; line-height: 47px; font-weight: 600; letter-spacing: 0px; padding: 40px 0;"
                contenteditable="true" data-gramm="false">
				<?php _e( 'Count by Date', 'essential-addons-for-contact-form-7' ); ?>
            </td>
        </tr>
    </table>

    <table cellpadding="0" cellspacing="0"
           style="width:100%;font-family:HelveticaNeue,Roboto,sans-serif;font-size:15px">
        <thead>
        <tr>
            <th style="padding:10px;border-bottom:1px solid #e5e5e5;border-right:1px solid #e5e5e5;text-align:left;font-weight:normal;color:#333;background:#f5f5f5">
                <b><?php _e( 'Date', 'essential-addons-for-contact-form-7' ); ?>:</b>
            </th>
            <th style="padding:10px;border-bottom:1px solid #e5e5e5;border-right:1px solid #e5e5e5;text-align:left;font-weight:normal;color:#333;background:#f5f5f5">
                <b><?php _e( 'Total Count', 'essential-addons-for-contact-form-7' ); ?>:</b>
            </th>
        </tr>
        </thead>
        <tbody>
		<?php
		if ( ! empty( $logs ) ) {

			//group by date
			$grouped = array();
			foreach ( $logs as $log ) {
				$grouped[ $log['date'] ] = $log;
			}

			foreach ( $grouped as $date => $logs ) {
                ?>
                <tr>
                    <td style="padding:8px;background:#fff;border:1px solid #eee">
						<?php echo esc_html( $date ); ?>
                    </td>
                    <td style="padding:8px;background:#fff;border:1px solid #eee">
						<?php echo esc_html( $logs['value'] ); ?>
                    </td>
                </tr>
			<?php }
		} ?>
        </tbody>
    </table>


    <!-- Footer -->
    <div style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;box-sizing:border-box;font-size:14px;width:100%;clear:both;color:#999;margin:0;padding:20px">
        <table width="100%"
               style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;box-sizing:border-box;font-size:14px;margin:0">
            <tbody>
            <tr style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;box-sizing:border-box;font-size:14px;margin:0">
                <td align="center" valign="top"
                    style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;box-sizing:border-box;font-size:12px;vertical-align:top;color:#999;text-align:center;margin:0;padding:0 0 20px">
                    <p>
						<?php _e( 'This email has been generated from Essential Addons for Contact Form 7 at', 'essential-addons-for-contact-form-7' ); ?>
                        <a href="<?php echo site_url(); ?>"><?php bloginfo( 'name' ) ?></a>.
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>