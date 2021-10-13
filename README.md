# WordPress-Multisite-Admin-Bar-Optimizer

This code is copied, with only a few improvements, directly from the [WP Artisan](https://wpartisan.me/tutorials/multisite-speed-improvements-admin-bar) blog and all credit for this code and idea should be directed there.  It is copied here for easy access.  The original blog post is worth reading for details about why this performance optimization is necessary.  

# The Problem

On a large WordPress multisite installation (say over 50 subsites or so), the wp-admin experience can become slow because of the code needed to build the list of sites under "My Sites" in the top admin bar.  It does this by calling `switch_to_blog()` for every site, which is not a fast function, and as the number of sites grows, the multisite will get slower and slower.  On a multisite with 527 sites, building the list of sites in the admin bar was taking over 6 seconds and had to be done on each page load for a logged in user.  

# The Solution

Add the file below to the `wp-content/mu-plugins` directory (or the `wp-content/plugins directory if you want to be able to enable or disable it on a site-by-site basis) and the My Sites menu will be built with much more efficient functions.  You lose the "New Post" items from the menu, but the speed improvement is refreshing.  Read more in the [WP Artisan](https://wpartisan.me/tutorials/multisite-speed-improvements-admin-bar) blog for the full technical explanation. 

# Other Multisite Admin Bar Optimizations

* [My Sites Search](https://github.com/trepmal/my-sites-search) - Add a search bar to the My Sites menu for multisite. 
