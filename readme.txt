=== Locationews ===
Contributors: anttiluokkanen
Donate link: https://www.paypal.me/anttiluokkanen
Plugin Name: Locationews
Plugin URI: https://www.locationews.com
Tags: location, local, map, news, publishing, journalism
Requires at least: 4.8
Tested up to: 4.9.8
Stable tag: 2.0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Version: 2.0.6

Publish location based articles in Locationews.

== Description ==

Locationews is a location based publishing channel that works both as a tool for journalists (as a plugin and template for the most widely used publishing platforms such as WordPress) and as an application that shows the content in a convenient map-based interface.

Go to locationews.com, register your free account and start publishing.

The plugin is made as simple as possible so that publishing on Locationews would be effortless for the publisher. Essentially you only need to install the plugin and enable it in one switch and you are ready to go.

Locationews plugin is WordPress multisite compatible.

This plugin is built on Cerado's [Structure](https://github.com/cedaro/structure).

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/locationews` directory or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Locationews screen to configure the plugin.

== Changelog ==

= 2.0.6 (2018-08-17) =
* Bugfix:	Fixed metabox javascript.

= 2.0.5 (2018-08-15) =
* Changed:	Validate and double check post meta values when setting or getting the values.

= 2.0.4 (2018-06-07) =
* Bugfix:   Set testing mode off. Allow to post to API.

= 2.0.3 (2018-06-02) =
* NEW:  Use possible Geotags (e.g. GEO:LAT=0.0, GEO:LON=0.0) for coordinates when map coordinates not set.
* NEW:  Added option to import plugin settings.
* Fixed:    Metabox behaviour when choosing article categories.
* Bugfix:   Couldn't read the required config files.

= 2.0.2. (2018-04-11) =
* Changed:	Check return type in wp_remote_get and wp_remote_post.
* Bugfix: Remove frontend JS.

= 2.0.1 (2018-03-20) =
* Changed:	Not to require any location to publish articles.

= 2.0.0 (2018-01-31) =
* NEW:  Completely redesigned code. Read minimum requirements.
* Changed:  Doesn't automatically publish articles with default location.
* Changed:  Minimum requirements changed. From now on this plugin requires at least PHP 5.6 or newer. Stay with the 1.1.15 if you have an older PHP version.

= 1.1.15 (2017-10-24) =
* NEW:      Added field 'Authors'
* NEW:      Added field for featured image's caption.

= 1.1.14 (2017-09-27) =
* Removed:  Article's publish time.
* Removed:  Update categories on admin init.

= 1.1.13 = 
* Removed:  jQuery tooltips. This time for good.

= 1.1.12 =
* Changed:  Took jQuery tooltips back again.
* Changed:  Different checks to see if Google Maps API is already loaded or not. The plugin should now work with multiple plugins using the same API.  
* Removed:  Unnecessary css + js files removed.

= 1.1.11 =
* Bugfix:   Fixed a conflict when multiple plugins uses Google Maps API.

= 1.1.10 =
* Bugfix:   Removed conflicting jQuery tooltips.

= 1.1.9 =
* Updated:  Changed the order of admin js files.

= 1.1.8 =
* NEW:      Added PHP version check on activation.
* Fixed:    On plugin activation check if valid options already exists.

= 1.1.7 =
* Fixed:    Removed content validation. Let the API remove unwanted tags.
* Updated:  API response messages and translations.

= 1.1.6 =
* Fixed:    Urlencode featured image url.

= 1.1.5 =
* NEW:      Defined default actions that occur in the front end.
* Changed:  Updated info texts with links to registration.

= 1.1.4 =
* Fixed:	Plugin css should not affect to other plugins anymore. Added namespacing to Bootstrap styles.

= 1.1.3 =
* NEW:      Tooltips & help texts
* NEW:      Map styles from Locationews Dashboard.
* NEW:      Possibility to search location address
* NEW:      Set article's default coordinates to null.
* Removed:  Locationews icon from post types list view.

= 1.1.2 =
* NEW:      Added support for multisite install.
* NEW:      Added test settings for test users.
* NEW:      Validate and strip not allowed tags from content

= 1.1.1 =
* NEW:      Added Locationews -category option to settings
* Fixed:    Tested with minimum requirements: PHP 5.3.29, WordPress 4.4

= 1.1.0 =
* NEW:      Use publicationId instead of userId
* NEW:      First version to use new API version 1.1.0
* Fixed:    Massive code rewrite and optimization

= 1.0.7 =
* Changed:  First test version. Latest version to use API version 1.0.3

= 1.0.6 =
* Fixed:    Bug fix. Locationews icon went back to default position when saving news. Reason: wrong meta key.

= 1.0.5 =
* NEW:      Allow shortcodes in content

= 1.0.4 =
* Changed:  Publish only published posts
* Changed:  Remove post when status is not publish
* Changed:  Remove post when post is moved to trash
* Fixed:    Bugfix in CategoryId

= 1.0.3 =
* Fixed:    Fixed bug in default location

== Upgrade Notice ==
Minimun requirements has changed in 2.0.0 release. Make sure you have at least PHP 5.6 installed or stay with 1.1.15.

== Requirements ==
* PHP >= 5.6 (Version 2.0.0 >) or PHP >= 5.3.29 (Version 1.1.15)
* WordPress >= 4.8 (Version 2.0.0 >) or WordPress >= 4.4 (Version 1.1.15)
* cURL support

==  Frequently Asked Questions ==
None yet.

== Screenshots ==

1. Locationews Plugin Settings
2. Locationews Meta Box
