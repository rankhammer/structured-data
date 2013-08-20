<?php

/*
Plugin Name: Structured Data
Plugin URI: http://www.rankhammer.com
Description: Enable & create Google+ Interactive Posts for your blog posts & pages.
Version: 1.0
Author: RankHammer
Author URI: http://www.rankhammer.com
License: GPL v2

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !defined( 'DB_NAME' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    die;
}

if ( !defined('RHMETADATA_URL') )
    define( 'RHMETADATA_URL', plugin_dir_url( __FILE__ ) );
if ( !defined('RHMETADATA_PATH') )
    define( 'RHMETADATA_PATH', plugin_dir_path( __FILE__ ) );
if ( !defined('RHMETADATA_BASENAME') )
    define( 'RHMETADATA_BASENAME', plugin_basename( __FILE__ ) );

define( 'RHMD_VERSION', '1.0.0' );

function rhmetadata_admin_init()
{
    require RHMETADATA_PATH . 'admin/class-admin.php';
    require RHMETADATA_PATH . 'admin/class-edit.php';
    require RHMETADATA_PATH . 'front/gplus-interactive.php';
}

function rhmetadata_frontend_init()
{
    require RHMETADATA_PATH . 'front/front.php';
    require RHMETADATA_PATH . 'front/gplus-interactive.php';
}

if ( is_admin() )
{
    if ( defined('DOING_AJAX') && DOING_AJAX )
    {
        require RHMETADATA_PATH.'admin/ajax.php';
    } else {
        add_action( 'plugins_loaded', 'rhmetadata_admin_init', 0 );
    }
    register_activation_hook( __FILE__, 'rhmetadata_activate' );
    register_deactivation_hook( __FILE__, 'rhmetadata_deactivate' );
} else {
    add_action( 'plugins_loaded', 'rhmetadata_frontend_init', 0 );
}

function rhmetadata_activate() {}

function rhmetadata_deactivate() {}


