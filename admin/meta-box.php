<?php

if ( !defined('RHMD_VERSION') )
{
    header('HTTP/1.0 403 Forbidden');
    die;
}

class RHMetaBox
{
    public function __construct()
    {
        add_action('do_meta_boxes',	array( $this, 'metaboxSchema'), 10, 2);

        /* Save post meta on the 'save_post' hook. */
        add_action( 'save_post', array( $this, 'saveMetabox' ), 10, 2 );
    }

    /**
     * display metabox
     * @param $page
     * @param $context
     */
    public function metaboxSchema( $page, $context )
    {
        // check to see if they have options first
        $rhmd_options	= get_option('rhmd_settings');

        // get custom post types
        $args = array(
            'public'   => true,
            '_builtin' => false
        );
        $output		= 'names';
        $operator	= 'and';

        $customs	= get_post_types($args,$output,$operator);
        $builtin	= array('post' => 'post', 'page' => 'page');

        $types		= $customs !== false ? array_merge($customs, $builtin) : $builtin;

        if ( in_array( $page,  $types ) && 'side' == $context )
            add_meta_box('metadata-box', __('Meta Data Options', 'rhmeta'), array(&$this, 'metadataBox'), $page, $context, 'high');
    }

    /**
     * Display checkboxes for disabling opengraph, twitter card, or schema.org metadata
     */
    public function metadataBox()
    {
        global $post;
        $disable_md	= get_post_meta($post->ID, '_rhmeta_md', true);

        // use nonce for security
        wp_nonce_field( RHMETADATA_BASENAME, 'schema_nonce' );

        echo '<p class="schema-post-option">';
        echo '<input type="checkbox" name="rh_disable_md" id="rh_disable_md" value="true" '.checked($disable_md, 'true', false).'>';
        echo '<label for="rh_disable_md">'.__('Disable schema.org on this page.', 'rhmeta').'</label>';
        echo '</p>';
    }

    /**
     * save the data
     */
    public function saveMetabox($post_id)
    {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        if ( isset($_POST['schema_nonce']) && !wp_verify_nonce( $_POST['schema_nonce'], RHMETADATA_BASENAME ) )
            return;

        if ( !current_user_can( 'edit_post', $post_id ) )
            return;

        // OK, we're authenticated: we need to find and save the data
        $dp_check	= isset($_POST['_rhmeta_md']) ? 'true' : 'false';
        update_post_meta($post_id, '_rhmeta_md', $dp_check);
    }
}
$rhMetaBox = new RHMetaBox();