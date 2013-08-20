<?php
/**
 * @package Admin
 */

if ( !defined( 'RHMD_VERSION' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    die;
}

// check to see if they have options first
$rhmd_options	= get_option('rhmd_settings');

?>

<div class="wrap">
    <?php settings_errors(); ?>
    <div class="rhschema_admin_wrapper">
        <?php echo '<form action="' . admin_url( 'options.php' ) . '" method="post" id="rhmeta-conf">'; ?>
            <?php settings_fields( 'rhmd_options_group' ); ?>
            <div class="rhschema_input_area">
                <h2>Enable & Configure Google+ Interactive Posts</h2>
                <br class="clear">
                <div class="rhschema_admin_box rhschema_box_status">
                    <input class="checkbox double" type="checkbox" id="rhmd_settings[gplus_enable]" name="rhmd_settings[gplus_enable]"  value="1"<?php checked( isset($rhmd_options['gplus_enable'])); ?>>
                    <label for="rhmd_settings[gplus_enable]"><?php _e('Enable Google+ Interactive Posts', 'rh_metadata'); ?></label>
                    <p class="desc"><?php _e('Adds the functionality to enable a Call to Action (CTA) on pages you post to Google+', 'rh_metadata'); ?></p>
                    <h4>Global Google+ API Settings</h4>
                    <div class="rhmd-field">
                        <label for="rhmd_settings[gplus_api_clientid_global]"><?php _e('Google+ API Client ID *', 'rh_metadata'); ?></label>
                        <div class="field-subject">
                            <input class="" type="text" size="75" id="rhmd_settings[gplus_api_clientid_global]" name="rhmd_settings[gplus_api_clientid_global]" value="<?php echo $rhmd_options['gplus_api_clientid_global'] ?>">
                            <p class="gplus-desc"><?php _e('Master Google+ API key used for creating all interactive posts on this site.', 'rh_metadata'); ?></p>
                        </div>
                    </div>
                    <div class="rhmd-field">
                        <label for="rhmd_settings[gpi_cta_global]"><?php _e('Default Call to Action Label *', 'rh_metadata'); ?></label>
                        <div class="field-subject">
                            <?php global $gplusInteractive; ?>
                            <?php echo $html = $gplusInteractive->generateSelect('rhmd_settings[gpi_cta_global]', $gplusInteractive->ctalabels, $rhmd_options['gpi_cta_global']); ?>
                            <!--<input class="" type="text" size="75" id="gpi_cta_global" name="rhmd_settings[gpi_cta_global]" value="<?php /*echo $rhmd_options['gpi_cta_global'] */?>">-->
                            <p class="gplus-desc"><?php _e('The CTA label that you pick from the list of Google’s available options. Label options listed ');?><a target="_blank" href="https://developers.google.com/+/features/call-to-action-labels">here</a>. </p>
                        </div>
                    </div>
                    <div class="rhmd-field">
                        <label for="rhmd_settings[gpi_cta_url_global]"><?php _e('Default Call to Action URL *', 'rh_metadata'); ?></label>
                        <div class="field-subject">
                            <input class="" type="text" size="75" id="gpi_cta_url_global" name="rhmd_settings[gpi_cta_url_global]" value="<?php echo $rhmd_options['gpi_cta_url_global'] ?>">
                            <p class="gplus-desc"><?php _e('The page that the CTA directs visitors to. Can be a static or dynamic URL.');?></p>
                        </div>
                    </div>
                    <div class="rhmd-field">
                        <label for="rhmd_settings[gpi_btntxt_global]"><?php _e('Default Button Text *', 'rh_metadata'); ?></label>
                        <div class="field-subject">
                            <input class="" type="text" size="75" id="rhmd_settings[gpi_btntxt_global]" name="rhmd_settings[gpi_btntxt_global]" value="<?php echo $rhmd_options['gpi_btntxt_global'] ?>">
                            <p class="gplus-desc"><?php _e('Default "Share to Google+" button text.'); ?></p>
                        </div>
                    </div>
                    <div class="rhmd-field">
                        <label for="rhmd_settings[gpi_prefill_global]"><?php _e('Default Prefill Text', 'rh_metadata'); ?></label>
                        <div class="field-subject">
                            <input class="" type="text" size="75" id="rhmd_settings[gpi_prefill_global]" name="rhmd_settings[gpi_prefill_global]" value="<?php echo $rhmd_options['gpi_prefill_global'] ?>">
                            <p class="gplus-desc"><?php _e('Default post text that will populate whenever someone clicks the share to Google+ button.'); ?></p>
                        </div>
                    </div>
                </div>
                <br class="clear">
                <div class="submit">
                    <input id="rhmeta_add" type="submit" class="button-primary" name="submit" value="<?php _e( "Save Settings", 'rh_metadata' ); ?>"/><span style="display:inline;" id="rhmeta_btn_result"></span>
                </div>

            </div>
        </form>
        <?php include_once('sidebar.php'); ?>
    </div>
</div>