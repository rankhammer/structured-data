<?php

if ( !defined( 'RHMD_VERSION' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    die;
}

function saveGoogleInteractive()
{
    $nonce = sanitize_text_field($_POST['nonce']);
    if ( !wp_verify_nonce( $nonce, 'rh_ajax_nonce' ) )
        die();

    // OK, we're authenticated: we need to find and save the data
    $rhmd_options	= get_option('rhmd_settings');
    $post_id = absint($_POST['post_id']);

    $url	= isset($_POST['gpi_content_url']) ? $_POST['gpi_content_url'] : '';
    $cta	= isset($_POST['gpi_cta']) ? $_POST['gpi_cta'] : '';
    $ctaUrl	= isset($_POST['gpi_cta_url']) ? $_POST['gpi_cta_url'] : '';
    $txt	= isset($_POST['gpi_prefill']) ? $_POST['gpi_prefill'] : '';
    $btnTxt	= isset($_POST['gpi_btn_text']) ? $_POST['gpi_btn_text'] : '';

    update_post_meta($post_id, 'gpi_content_url', $url);
    update_post_meta($post_id, 'gpi_cta', $cta);
    update_post_meta($post_id, 'gpi_cta_url', $ctaUrl);
    update_post_meta($post_id, 'gpi_prefill', $txt);
    update_post_meta($post_id, 'gpi_btn_text', $btnTxt);

    $data = array('client_id' => $rhmd_options['gplus_api_clientid_global'], 'url' => $url, 'cta' => $cta, 'ctaurl' => $ctaUrl, 'txt' => $txt, 'btntxt' => $btnTxt);
    $response = json_encode( array( 'success' => true,'data' => $data ) );

    header( "Content-Type: application/json" );
    echo $response;
    exit;
}

add_action("wp_ajax_save_gpi", "saveGoogleInteractive" );
