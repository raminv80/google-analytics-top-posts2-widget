=== Plugin Name ===
Google Analytics Top Content Widget

Contributors: Jtsternberg , jtsternberg
Plugin Name:  Google Analytics Top Content Widget 2
Plugin URI: http://j.ustin.co/yWTtmy
Tags: google analytics, google, top posts, top content, display rank, page rank, page views, widget, sidebar, sidebar widget, Google Analytics Dashboard, shortcode, site stats, statistics, stats
Author: Ramin Vakilian
Author URI: http://about.me/jtsternberg
Donate link: http://j.ustin.co/rYL89n
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 1.0
Version: 1.4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enhanced version of Google Analytics Top Content Widget written by Jtsternberg to include filtering of top content based on viewers' country or a selected country. Widget and shortcode to display top content according to Google Analytics. ("Google Analytics Dashboard" plugin required)

== Description ==

Adds a widget that allows you to display top pages/posts in your sidebar based on google analytics data.

Requires a Google Analytics account, and the plugin, ["Google Analytics Dashboard"](http://wordpress.org/extend/plugins/google-analytics-dashboard/) (which will be auto-installed by this plugin, thanks to [@jthomasgriffin](http://twitter.com/jthomasgriffin)'s awesome [TGM Plugin Activation Class](http://j.ustin.co/yZPKXw)).

Also includes a shortcode to display the top content in your posts and pages.

= Shortcode with options supported: =

`[google_top_content pageviews=5 number=10 showhome=no time=2628000 timeval=2]`

= Shortcode attributes definitions: =

* Pageviews: Show pages with at least __ number of page views
* Number: Number of pages to show in the list
* Showhome: Will remove home page from list: (usually "yoursite.com" is the highest viewed page)
* Time: Selects how far back you would like analytics to pull from. needs to be in seconds. (1 hour - 3600, 1 day - 86400, 1 month - 2628000, 1 year - 31536000).
* Time Value: time=2628000 timeval=2 like in the example above would be 2 months.


All of the widget options are exactly that.. optional. If you don't include them it will pick some defaults.

= Plugin Features: =

* Plugin uses WordPress transients to cache the Google results so you're not running the update from Google every time. cache updates every 24 hours.
* Developer Friendly. Many filters built in to allow you to filter the results to dispay how you want.  One example of that would be to remove your Site's title from the results. (now unnecessary, as the widget/shortcode has the option built in)
** [Example using a filter to add view counts after the title](http://wordpress.org/support/topic/top-viewed-content-couple-of-tweeks-needed?replies=9#post-3816989) -
`add_filter( 'gtc_pages_filter', 'gtc_add_viewcount_title' );
function gtc_add_viewcount_title( $pages ) {

	if ( !$pages )
		return false;
	// loop through the pages
	foreach ( $pages as $key => $page ) {
		// and add the page count to the title value
		$pages[$key]['children']['value'] = $pages[$key]['children']['value'] . ' ['. $pages[$key]['children']['children']['ga:pageviews'] .' Views]';
	}
	return $pages;
}`


== Installation ==

1. Upload the `google-analytics-top-posts2-widget` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Plugin will prompt you to install the "Google Analytics Dashboard" plugin. Install and activate it.
4. Use the "Google Analytics Dashboard" plugin's settings page to login to your google analytics account.
5. On the widgets page, drag the "Google Analytics Top Posts" widget to the desired sidebar.
6. Update the widget settings and save.

== Frequently Asked Questions ==


If you run into a problem or have a question, contact me [@raminv80 on twitter]. I'll add them here.


== Screenshots ==

1. Widget options.
2. Widget display (in an ordered list).

== Changelog ==

= 1.0 =
Launch