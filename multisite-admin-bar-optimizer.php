<?php
/**
*  Create the list of network sites in the WordPress admin bar in a more performant way.
*
*  Based on the article https://wpartisan.me/tutorials/multisite-speed-improvements-admin-bar
*/

add_action( 'add_admin_bar_menus', 'large_network_remove_wp_admin_bar_my_sites_menu', 10, 0 );

/**
 * De-register the native WP Admin Bar My Sites function.
 *
 * Loading all sites menu for large multisite is inefficient and bad news. This de-registers the native WP function so it can be replaced with a more efficient one.
 *
 * @return null
 */
function large_network_remove_wp_admin_bar_my_sites_menu() {
    remove_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );
}

add_action( 'admin_bar_menu', 'large_network_replacement_my_sites_menu', 20, 1 );


/**
 * Add the "My Sites/[Site Name]" menu and all submenus.
 *
 * Essentially the same as the WP native one but doesn't use switch_to_blog();
 *
 *
 * @param WP_Admin_Bar $wp_admin_bar
 * @return null
 */
function large_network_replacement_my_sites_menu( $wp_admin_bar ) {
    // Don't show for logged out users or single site mode.
    if ( ! is_user_logged_in() || ! is_multisite() )
        return;

    // Show only when the user has at least one site, or they're a super admin.
    if ( count( $wp_admin_bar->user->blogs ) < 1 && ! is_super_admin() ) return; if ( $wp_admin_bar->user->active_blog ) {
        $my_sites_url = get_admin_url( $wp_admin_bar->user->active_blog->blog_id, 'my-sites.php' );
    } else {
        $my_sites_url = admin_url( 'my-sites.php' );
    }

    $wp_admin_bar->add_menu( array(
        'id'    => 'my-sites',
        'title' => __( 'My Sites' ),
        'href'  => $my_sites_url,
    ) );

    if ( is_super_admin() ) {
        $wp_admin_bar->add_group( array(
            'parent' => 'my-sites',
            'id'     => 'my-sites-super-admin',
        ) );

        $wp_admin_bar->add_menu( array(
            'parent' => 'my-sites-super-admin',
            'id'     => 'network-admin',
            'title'  => __('Network Admin'),
            'href'   => network_admin_url(),
        ) );

        $wp_admin_bar->add_menu( array(
            'parent' => 'network-admin',
            'id'     => 'network-admin-d',
            'title'  => __( 'Dashboard' ),
            'href'   => network_admin_url(),
        ) );
        $wp_admin_bar->add_menu( array(
            'parent' => 'network-admin',
            'id'     => 'network-admin-s',
            'title'  => __( 'Sites' ),
            'href'   => network_admin_url( 'sites.php' ),
        ) );
        $wp_admin_bar->add_menu( array(
            'parent' => 'network-admin',
            'id'     => 'network-admin-u',
            'title'  => __( 'Users' ),
            'href'   => network_admin_url( 'users.php' ),
        ) );
        $wp_admin_bar->add_menu( array(
            'parent' => 'network-admin',
            'id'     => 'network-admin-t',
            'title'  => __( 'Themes' ),
            'href'   => network_admin_url( 'themes.php' ),
        ) );
        $wp_admin_bar->add_menu( array(
            'parent' => 'network-admin',
            'id'     => 'network-admin-p',
            'title'  => __( 'Plugins' ),
            'href'   => network_admin_url( 'plugins.php' ),
        ) );
        $wp_admin_bar->add_menu( array(
            'parent' => 'network-admin',
            'id'     => 'network-admin-o',
            'title'  => __( 'Settings' ),
            'href'   => network_admin_url( 'settings.php' ),
        ) );
    }

    // Add site links
    $wp_admin_bar->add_group( array(
        'parent' => 'my-sites',
        'id'     => 'my-sites-list',
        'meta'   => array(
            'class' => is_super_admin() ? 'ab-sub-secondary' : '',
        ),
    ) );

    foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {

        $blavatar = '
<div class="blavatar"></div>

';

        $blogname = $blog->blogname;

        if ( ! $blogname ) {
            $blogname = preg_replace( '#^(https?://)?(www.)?#', '', $blog->siteurl );
        }

        $menu_id  = 'blog-' . $blog->userblog_id;

        $admin_url = $blog->siteurl . '/wp-admin';

        $wp_admin_bar->add_menu( array(
            'parent'    => 'my-sites-list',
            'id'        => $menu_id,
            'title'     => $blavatar . $blogname,
            'href'      => $admin_url,
        ) );

        $wp_admin_bar->add_menu( array(
            'parent' => $menu_id,
            'id'     => $menu_id . '-d',
            'title'  => __( 'Dashboard' ),
            'href'   => $admin_url,
        ) );

        $wp_admin_bar->add_menu( array(
            'parent' => $menu_id,
            'id'     => $menu_id . '-v',
            'title'  => __( 'Visit Site' ),
            'href'   => $blog->siteurl,
        ) );

    }
}