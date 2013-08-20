<?php

if ( !defined('RHMD_VERSION') )
{
    header('HTTP/1.0 403 Forbidden');
    die;
}

class RHSchemaMeta
{
    public function __construct()
    {
        add_filter( 'language_attributes', array( &$this, 'wpa89133' ));
        //add_filter('the_content', array( &$this, 'addSchemaMeta' ));
        //add_filter('the_content', array( &$this, 'wrapWithSchema' ));
    }

    /**
     * Determine which semantic markup type we will apply
     *
     * @param $output
     * @return string
     */
    function wpa89133( $output )
    {
        $rhmd_options = get_option('rhmd_settings');

        //Schema.org tagging is not enabled; leave now
        if (!isset($rhmd_options['schema_enable']) || $rhmd_options['schema_enable'] == 0)
            return $output;

        $schemaType = 'WebPage';
        global $template;
        if (is_single())
        {
            $schemaType = 'BlogPosting';
        } else if (basename($template) == 'template-blog.php') {
            $schemaType = 'Blog';
        }

        $output .= ' itemscope="itemscope" itemtype="http://schema.org/'. $schemaType; //.'" itemref="wrapper"
        return $output;
    }

    /**
     * Add the body copy semantic markup
     *
     * @param $content
     * @return mixed
     */
    public function addSchemaMeta($content)
    {
        $this->schemaTitle();
        return $content;
    }

    protected function schemaTitle()
    {
        //TODO: Check for Blog or BlogPosting
        $y = single_post_title();
        $x = get_the_title();
    }

    /**
     *
     * Determine which semantic markup type we will apply to the body content, if at all
     *
     * @param $content
     * @internal param $output
     * @return string
     */
    function wrapWithSchema( $content )
    {
        $rhmd_options = get_option('rhmd_settings');

        //Schema.org tagging is not enabled globally
        if (!isset($rhmd_options['schema_enable']) || $rhmd_options['schema_enable'] == 0)
            return $content;

        // check for single post disable
        global $post;
        $disable_post	= get_post_meta($post->ID, '_rhmeta_md', true);
        if($disable_post == 'true' )
            return $content;

        //Figure out if it's the main blog page, or an individual post
        global $template;
        if (is_single())
        {
            $schemaType = 'BlogPosting';
        } else if (basename($template) == 'template-blog.php') {
            $schemaType = 'Blog';
        } else {
            //Regular page
            return $content;
        }
        $content = '<div itemscope itemtype="http://schema.org/'. $schemaType .'">'.$content.'</div>';
        return $content;
    }
}
new RHSchemaMeta();