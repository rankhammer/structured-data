<?php
/**
 * @package Admin
 */

if ( !defined( 'RHMD_VERSION' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    die;
}

class RHMetaData_Admin
{

    public function __construct()
    {
        add_action(
            'admin_enqueue_scripts',
            array($this, 'admin_scripts_and_styles')
        );

        if( $this->grant_access() )
        {
            add_action('admin_init', array($this, 'options_init'));
            add_action('admin_menu', array($this, 'register_settings_page'));
            add_filter('media_buttons_context',array( $this, 'rhschema_media_button'), 35);
        }
    }

    /**
     * Register the menu item and its sub menu's.
     *
     * @global array $submenu used to change the label on the first item.
     */
    public function register_settings_page()
    {
        add_menu_page(
            __('Structured Data Config', 'rh-schema'),
            __('Markup', 'rh-schema'),
            'manage_options',
            'rh-schema',
            array($this, 'config_page'),
            RHMETADATA_URL . 'assets/images/rankhammer-icon.png'
        );
        global $submenu;
        if( isset($submenu['rh-schema']) )
        {
            $submenu['rh-schema'][0][0] = __('Dashboard', 'rh-schema');
        }
    }

    /**
     * Registers the settings we want to store for this plugin
     */
    public function options_init()
    {
        register_setting( 'rhmd_options_group', 'rhmd_settings', array($this, 'update_option') );

    }

    /**
     * Validate & save admin data
     * @param $data
     */
    public function update_option($data)
    {
        //We will only require fields if the feature is actually enabled.
        $gplus_enable = abs($data['gplus_enable']);

        //Make sure the required fields are filled in
        if($gplus_enable)
        {
            if (!isset($data['gplus_api_clientid_global']) || $data['gplus_api_clientid_global'] == ''){
                add_settings_error( 'rhmd_options_group', 'xx', 'G+: Missing API Client ID', 'error');
            } else {
                if (strpos($data['gplus_api_clientid_global'], '.apps.googleusercontent.com')) {
                    $data['gplus_api_clientid_global'] = str_replace('.apps.googleusercontent.com', '', $data['gplus_api_clientid_global']);
                }
            }

            if (!isset($data['gpi_cta_global']) || $data['gpi_cta_global'] == '') {
                add_settings_error( 'rhmd_options_group', 'xx', 'G+: Missing CTA', 'error');
            }

            if (!isset($data['gpi_cta_url_global']) || $data['gpi_cta_url_global'] == '') {
                add_settings_error( 'rhmd_options_group', 'xx', 'G+: Missing CTA URL', 'error');
            }

            if (!isset($data['gpi_btntxt_global']) || $data['gpi_btntxt_global'] == '') {
                add_settings_error( 'rhmd_options_group', 'xx', 'G+: Missing Button Text', 'error');
            }

        }
        return $data;
    }

    public function rhschema_media_button($context)
    {
        // don't show on dashboard
        $current_screen = get_current_screen();
        if ( 'dashboard' == $current_screen->base )
        {
            return;
        }

        // don't display button for users who don't have access
        if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
        {
            return;
        }

        //TODO: Check if ver. < 3.5 b/c the UI changed
        // do a version check for the new 3.5 UI
        $version	= get_bloginfo('version');
        $img = '<span class="rhschema_icon"></span>';
        $title = 'Custom Meta Data';

        //append the icon
        $context .= "<a title='Markup' href='#TB_inline?width=400&height=755&inlineId=rhmd-dialog-modal' class='thickbox button'>{$img} Add Markup</a>";

        return $context;
    }


    /**
     * Loads the form for the Dashboard page.
     */
    public function config_page()
    {
        if( isset($_GET['page']) && 'rh-schema' == $_GET['page'] )
        {
            include(RHMETADATA_PATH . '/admin/dashboard.php');
            include(RHMETADATA_PATH . '/admin/ajax.php');
        }
    }

    /**
     * Check whether the current user is allowed to access the configuration.
     *
     * @return boolean
     */
    public function grant_access()
    {
        if( !function_exists('is_multisite') || !is_multisite() )
        {
            return true;
        }

        $options = get_site_option('rhmd_site');
        if( !is_array($options) || !isset($options['access']) )
        {
            return true;
        }

        if( $options['access'] == 'superadmin' && !is_super_admin() )
        {
            return false;
        }
        return true;
    }

    /**
     * Load the necessary CSS & JS files for administration
     */
    public function admin_scripts_and_styles()
    {
        if( !wp_script_is('rhschema_admin_css', 'registered') )
        {
            wp_register_style(
                'rhschema_admin_css',
                RHMETADATA_URL . 'assets/css/admin.css',
                false,
                '1.0.0'
            );
            wp_enqueue_style('rhschema_admin_css');
        }
        wp_enqueue_script('share_twitter', 'http'.(is_ssl()?'s':'').'://platform.twitter.com/widgets.js','','');
        wp_enqueue_script('share_google', 'http'.(is_ssl()?'s':'').'://apis.google.com/js/plusone.js','','');
    }

}

global $media_schema;
$media_schema = new RHMetaData_Admin();