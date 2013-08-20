<?php

/**
 * Repurposed methods. Credit goes to Joost de Valk @ yoast.com
 */

if ( !defined( 'RHMD_VERSION' ) )
{
    header( 'HTTP/1.0 403 Forbidden' );
    die;
}

class RHMD_Front
{
    /**
     * Determine whether the current page is the homepage and shows posts.
     *
     * @return bool
     */
    function is_home_posts_page()
    {
        return ( is_home() );
    }

    /**
     * Determine whether the current page is a static homepage.
     *
     * @return bool
     */
    function is_home_static_page()
    {
        return ( is_front_page() );
    }

    /**
     * Determine whether this is the posts page, regardless of whether it's the frontpage or not.
     *
     * @return bool
     */
    function is_posts_page()
    {
        return ( is_home() );
    }

    /**
     * Used for static home and posts pages as well as singular titles.
     *
     * @param object|null $object if filled, object to get the title for
     * @return string
     */
    function get_content_title( $object = null )
    {
        if ( is_null( $object ) ) {
            global $wp_query;
            $object = $wp_query->get_queried_object();
        }

        $title = $this->rhmd_get_value( 'title', $object->ID );

        if ( !empty( $title ) )
        {
            return wpseo_replace_vars( $title, (array) $object );
        }

        return $this->get_title_from_options( 'title-' . $object->post_type, $object );
    }


    /**
     * Get the default title for the current page.
     *
     * @param string $sep         the separator used between variables
     * @param string $seplocation Whether the separator should be left or right.
     * @param string $title       possible title that's already set
     * @return string
     */
    function get_default_title( $sep, $seplocation, $title = '' ) {
        if ( 'right' == $seplocation )
            $regex = '/\s*' . preg_quote( trim( $sep ), '/' ) . '\s*/';
        else
            $regex = '/^\s*' . preg_quote( trim( $sep ), '/' ) . '\s*/';
        $title = preg_replace( $regex, '', $title );

        if ( empty( $title ) ) {
            $title = get_bloginfo( 'name' );
            $title = $this->add_paging_to_title( $sep, $seplocation, $title );
            $title = $this->add_to_title( $sep, $seplocation, $title, get_bloginfo( 'description' ) );
            return $title;
        }

        $title = $this->add_paging_to_title( $sep, $seplocation, $title );
        $title = $this->add_to_title( $sep, $seplocation, $title, get_bloginfo( 'name' ) );
        return $title;
    }

    /**
     * This function adds paging details to the title.
     *
     * @param string $sep         separator used in the title
     * @param string $seplocation Whether the separator should be left or right.
     * @param string $title       the title to append the paging info to
     * @return string
     */
    function add_paging_to_title( $sep, $seplocation, $title ) {
        global $wp_query;

        if ( !empty( $wp_query->query_vars['paged'] ) && $wp_query->query_vars['paged'] > 1 )
            return $this->add_to_title( $sep, $seplocation, $title, $wp_query->query_vars['paged'] . '/' . $wp_query->max_num_pages );

        return $title;
    }

    /**
     * Add part to title, while ensuring that the $seplocation variable is respected.
     *
     * @param string $sep         separator used in the title
     * @param string $seplocation Whether the separator should be left or right.
     * @param string $title       the title to append the title_part to
     * @param string $title_part  the part to append to the title
     * @return string
     */
    function add_to_title( $sep, $seplocation, $title, $title_part ) {
        if ( 'right' == $seplocation )
            return $title . $sep . $title_part;
        return $title_part . $sep . $title;
    }

    /**
     * Main title function.
     *
     * @param string $title       Title that might have already been set.
     * @param string $sepinput    Separator determined in theme.
     * @param string $seplocation Whether the separator should be left or right.
     * @return string
     */
    function title( $title, $sepinput = '-', $seplocation = '' ) {
        global $sep;

        $sep = $sepinput;

        if ( is_feed() )
            return $title;

        // This needs to be kept track of in order to generate
        // default titles for singular pages.
        $original_title = $title;

        // This conditional ensures that sites that use of wp_title(''); as the plugin
        // used to suggest will still work properly with these changes.
        if ( '' == trim( $sep ) && '' == $seplocation ) {
            $sep         = '-';
            $seplocation = 'right';
        } // In the event that $seplocation is left empty, the direction will be
        // determined by whether the site is in rtl mode or not. This is based
        // upon my findings that rtl sites tend to reverse the flow of the site titles.
        else if ( '' == $seplocation )
            $seplocation = ( is_rtl() ) ? 'left' : 'right';

        $sep = ' ' . trim( $sep ) . ' ';

        // This flag is used to determine if any additional
        // processing should be done to the title after the
        // main section of title generation completes.
        $modified_title = true;

        // This variable holds the page-specific title part
        // that is used to generate default titles.
        $title_part = '';

        if ( $this->is_home_static_page() ) {
            $title = $this->get_content_title();
        //} else if ( $this->is_home_posts_page() ) {
            //$title = $this->get_title_from_options( 'title-home' );
        } else if ( $this->is_posts_page() ) {
            $title = $this->get_content_title( get_post( get_option( 'page_for_posts' ) ) );
        } else if ( is_singular() ) {
            $title = $this->get_content_title();

            if ( empty( $title ) )
                $title_part = $original_title;
        //} else if ( is_search() ) {
            //$title = $this->get_title_from_options( 'title-search' );

            if ( empty( $title ) )
                $title_part = sprintf( __( 'Search for "%s"', 'wordpress-seo' ), esc_html( get_search_query() ) );
        } else if ( is_category() || is_tag() || is_tax() ) {
            $title = $this->get_taxonomy_title();

            if ( empty( $title ) ) {
                if ( is_category() )
                    $title_part = single_cat_title( '', false );
                else if ( is_tag() )
                    $title_part = single_tag_title( '', false );
                else if ( function_exists( 'single_term_title' ) ) {
                    $title_part = single_term_title( '', false );
                } else {
                    global $wp_query;
                    $term       = $wp_query->get_queried_object();
                    $title_part = $term->name;
                }
            }
        } else if ( is_author() ) {
            $title = $this->get_author_title();

            if ( empty( $title ) )
                $title_part = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
        } else {
            // In case the page type is unknown, leave the title alone.
            $modified_title = false;

            // If you would like to generate a default title instead,
            // the following code could be used instead of the line above:
            // $title_part = $title;
        }

        if ( ( $modified_title && empty( $title ) ) || !empty( $title_part ) )
            $title = $this->get_default_title( $sep, $seplocation, $title_part );

        return esc_html( strip_tags( stripslashes( apply_filters( 'mdog_title', $title ) ) ) );
    }

    /**
     * This function normally outputs the canonical but is also used in other places to retrieve the canonical URL
     * for the current page.
     *
     * @param bool $echo    Whether or not to output the canonical element.
     * @param bool $unpaged Whether or not to return the canonical with or without pagination added to the URL.
     * @return string $canonical
     */
    function canonical( $echo = true, $unpaged = false )
    {
        $canonical = false;

        // Set decent canonicals for homepage, singulars and taxonomy pages
        if ( is_singular() )
        {
            $obj       = get_queried_object();
            $canonical = get_permalink( $obj->ID );

            // Fix paginated pages canonical, but only if the page is truly paginated.
            if ( get_query_var( 'page' ) > 1 )
            {
                global $wp_rewrite;
                $numpages = substr_count( $obj->post_content, '<!--nextpage-->' ) + 1;
                if ( $numpages && get_query_var( 'page' ) < $numpages ) {
                    if ( !$wp_rewrite->using_permalinks() )
                    {
                        $canonical = add_query_arg( 'page', get_query_var( 'page' ), $canonical );
                    } else {
                        $canonical = user_trailingslashit( trailingslashit( $canonical ) . get_query_var( 'page' ) );
                    }
                }
            }
        } else {
            if ( is_search() )
            {
                $canonical = get_search_link();
            } else if ( is_front_page() ) {
                $canonical = home_url( '/' );
            } else if ( $this->is_posts_page() ) {
                $canonical = get_permalink( get_option( 'page_for_posts' ) );
            } else if ( is_tax() || is_tag() || is_category() ) {
                $term      = get_queried_object();
                $canonical = wpseo_get_term_meta( $term, $term->taxonomy, 'canonical' );
                if ( !$canonical )
                    $canonical = get_term_link( $term, $term->taxonomy );
            } else if ( function_exists( 'get_post_type_archive_link' ) && is_post_type_archive() ) {
                $canonical = get_post_type_archive_link( get_query_var( 'post_type' ) );
            } else if ( is_author() ) {
                $canonical = get_author_posts_url( get_query_var( 'author' ), get_query_var( 'author_name' ) );
            } else if ( is_archive() ) {
                if ( is_date() )
                {
                    if ( is_day() )
                    {
                        $canonical = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
                    } else if ( is_month() ) {
                        $canonical = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
                    } else if ( is_year() ) {
                        $canonical = get_year_link( get_query_var( 'year' ) );
                    }
                }
            }

            if ( $canonical && $unpaged )
                return $canonical;

            if ( $canonical && get_query_var( 'paged' ) > 1 )
            {
                global $wp_rewrite;
                if ( !$wp_rewrite->using_permalinks() )
                {
                    $canonical = add_query_arg( 'paged', get_query_var( 'paged' ), $canonical );
                } else {
                    $canonical = user_trailingslashit( trailingslashit( $canonical ) . trailingslashit( $wp_rewrite->pagination_base ) . get_query_var( 'paged' ) );
                }
            }
        }
        if ( $canonical && !is_wp_error( $canonical ) )
        {
            if ( $echo )
                echo '<link rel="canonical" href="' . esc_url( $canonical, null, 'other' ) . '" />' . "\n";
            else
                return $canonical;
        }
    }

    /**
     * Get the value from the post custom values
     *
     * @param string $val    name of the value to get
     * @param int    $postid post ID of the post to get the value for
     * @return bool|mixed
     */
    function rhmd_get_value( $val, $postid = 0 )
    {
        $postid = absint( $postid );
        if ( $postid === 0 )
        {
            global $post;
            if ( isset( $post ) && isset( $post->post_status ) && $post->post_status != 'auto-draft')
                $postid = $post->ID;
            else
                return false;
        }
        $custom = get_post_custom( $postid );
        if ( !empty( $custom['_rhmd_' . $val][0] ) )      //_yoast_wpseo_
            return maybe_unserialize( $custom['_rmhd_' . $val][0] );
        else
            return false;
    }
}
