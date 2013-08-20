<?php

if ( !defined( 'RHMD_VERSION' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    die;
}

class RHContentEdit
{


    public function __construct()
    {
        add_action('admin_footer', array($this, 'getAddSchemaPopUpForm') );
        add_action( 'admin_enqueue_scripts', array($this, 'rhEnqueueScripts') );
    }

    /**
     * add in custom JS for administration
     */
    public function rhEnqueueScripts()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('thickbox',null,array('jquery'));

        wp_register_script('rhschema_admin_js', RHMETADATA_URL . 'assets/js/admin.js');
        wp_enqueue_script('rhschema_admin_js');

        wp_localize_script('rhschema_admin_js', 'RH', array(
                'nonce' => wp_create_nonce( 'rh_ajax_nonce' )
            )
        );
    }

    public function getAddSchemaPopUpForm()
    {
        global $gplusInteractive;
        $gplusInteractive->addScriptToFooter();
        // check to see if they have options first
        $rhmd_options	= get_option('rhmd_settings');
        global $post;
        if (is_null($post) || $post->ID == null)
            return;

        $permalink= get_post_meta($post->ID, 'gpi_content_url', true);
        if (!isset($permalink) || $permalink == '')
        {
            global $post;
            $permalink = get_permalink( $post->ID );
        }

        $gpiCTALabel	= get_post_meta($post->ID, 'gpi_cta', true);
        if (!isset($gpiCTALabel) || $gpiCTALabel == '')
        {
            $gpiCTALabel = $rhmd_options['gpi_cta_global'];
        }

        $gpiCTAUrl	= get_post_meta($post->ID, 'gpi_cta_url', true);
        if (!isset($gpiCTAUrl) || $gpiCTAUrl == '')
        {
            $gpiCTAUrl = $rhmd_options['gpi_cta_url_global'];
        }

        $gpiPrefillText	= get_post_meta($post->ID, 'gpi_prefill', true);
        if (!isset($gpiPrefillText) || $gpiPrefillText == '')
        {
            $gpiPrefillText = $rhmd_options['gpi_prefill_global'];
        }

        $gpiButtonText	= get_post_meta($post->ID, 'gpi_btntxt', true);
        if (!isset($gpiButtonText) || $gpiButtonText == '')
        {
            $gpiButtonText = $rhmd_options['gpi_btntxt_global'];
        }

        // use nonce for security
        wp_nonce_field( RHMETADATA_BASENAME, 'rhmd_gpi_nonce' );
        ?>

        <div id="rhmd-dialog-modal" title="Custom Structured Markup" style="display:none;">
            <div id="rhmd-dialog-tabs">
                <ul class="rhmd-nav-tabs nav">
                    <li class="nav-one"><a id="rh_sm_tab1" href="#rhmd-gplus" class="current">Google+ Interactive</a></li>
                    <li class="nav-two"><a id="rh_sm_tab2" href="#rhmd-schema">Schema Markup</a></li>
                </ul>

                <section id="rhmd-gplus" class="list-wrap rh-tab-content rh-active">
                    <ul id="gpi_featured">
                        <input type="hidden" id="post_id" value="<?php echo $post->ID?>" />
                        <div>
                            <strong><label for="rhmd_settings[gpi_content_url]"><?php _e('Content URL', 'rh_metadata'); ?></label></strong>
                            <input class="" type="text" size="75" id="gpi_content_url" name="rhmd_settings[gpi_content_url]" value="<?php echo $permalink ?>">
                        </div>
                        <p class="gplus-desc"><?php _e('The primary page that you want to point to. This is the link you’d use for a standard Google+ post. The meta description and on-page images from this page are what get pulled in for the standard Google+ rich snippet.');?></p>
                        <br class="clear">
                        <div>
                            <strong><label for="rhmd_settings[gpi_cta]"><?php _e('Call to Action Label', 'rh_metadata'); ?></label></strong>
                            <!--<input class="" type="text" size="75" id="gpi_cta" name="rhmd_settings[gpi_cta]" value="<?php /*echo $gpiCTALabel */?>">-->
                            <?php echo $html = $gplusInteractive->generateSelect('dataCallToActionLabel', $gplusInteractive->ctalabels, $gpiCTALabel); ?>

                        </div>
                        <p class="gplus-desc"><?php _e('The CTA label that you pick from the list of Google’s available options. Label options listed ');?><a target="_blank" href="https://developers.google.com/+/features/call-to-action-labels">here</a></p>
                        <br class="clear">
                        <div>
                            <strong><label for="rhmd_settings[gpi_cta_url]"><?php _e('Call to Action URL', 'rh_metadata'); ?></label></strong>
                            <input class="" type="text" size="75" id="gpi_cta_url" name="rhmd_settings[gpi_cta_url]" value="<?php echo $gpiCTAUrl ?>">
                        </div>
                        <p class="gplus-desc"><?php _e('The page that the CTA directs visitors to. Can be a static or dynamic URL.');?></p>
                        <br class="clear">

                        <div>
                            <strong><label for="rhmd_settings[gpi_btn_text]"><?php _e('Button Text', 'rh_metadata'); ?></label></strong>
                            <input class="" type="text" size="75" id="gpi_btn_text" name="rhmd_settings[gpi_btn_text]" value="<?php echo $gpiButtonText ?>">
                        </div>
                        <p class="gplus-desc"><?php _e('(Optional) Change the text that shows up on the actual button.');?></p>
                        <br class="clear">
                        <div>
                            <strong><label for="rhmd_settings[gpi_prefill]"><?php _e('Prefill Text', 'rh_metadata'); ?></label></strong>
                            <input class="" type="text" size="75" id="gpi_prefill" name="rhmd_settings[gpi_prefill]" value="<?php echo $gpiPrefillText ?>">
                        </div>
                        <p class="gplus-desc"><?php _e('(Optional) This is the message that will be pre-composed when you hit “Share”. Not really 100% necessary since you’ll be managing this on your own, but you could embed the output into your own pages and leverage that prefill text.');?></p>

                        <?php submit_button( 'Save Settings', 'primary') ?>
                        <br class="clear">
                        <hr />
                        <br class="clear">
                        <h3>Click Button to Share on Google+</h3>
                        <br class="clear">
                        <button id="myBtn"
                                class="g-interactivepost"
                                data-contenturl="<?php echo $permalink; ?>"
                                data-clientid="<?php echo $rhmd_options['gplus_api_clientid_global']; ?>.apps.googleusercontent.com"
                                data-cookiepolicy="single_host_origin"
                                data-prefilltext="<?php echo $gpiPrefillText; ?>"
                                data-calltoactionlabel="<?php echo $gpiCTALabel; ?>"
                                data-calltoactionurl="<?php echo $gpiCTAUrl; ?>">
                            <span class="icon">&nbsp;</span>
                            <?php echo $gpiButtonText; ?>
                        </button>
                        <p class="gplus-desc"><?php _e('Click this button to share this interactive post immediately on Google+.');?></p>
                        <div id="hiddenCode">
                            <h3>Copy Shortcode</h3>
                            <p><?php _e('If you want to embed the above within a post, copy the shortcode and paste it in within the page content.');?></p>
                            <code>[gpi id="<?php echo $post->ID;?>"]</code>
                        </div>
                    </ul> <!-- END featured -->
                </section> <!-- END List Wrap -->
                <section id="rhmd-schema" class="list-wrap rh-tab-content rh-hide">
                    <p>Coming Soon.</p>
                </section>
            </div>
        </div>

        <?php
    }

    protected function generateSelect($name = '', $options = array(), $default = '') {
        $html = '<select id="gpi_cta" name="'.$name.'">';
        foreach ($options as $value => $option) {
            if ($value == $default) {
                $html .= '<option value='.$value.' selected="selected">'.$option.'</option>';
            } else {
                $html .= '<option value='.$value.'>'.$option.'</option>';
            }
        }

        $html .= '</select>';
        return $html;
    }
}

new RHContentEdit();

