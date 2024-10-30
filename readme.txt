=== Plugin Name ===
Contributors: useStrict
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VLQU2MMXKB6S2
Tags: dynamic content, geo-location, geolocation, scheduled content, dynamic widgets, dynamic snippets, snippets
Requires at least: 3.5
Tested up to: 3.8
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Control which snippets of content get displayed to your visitors based on Scheduling or Geo-Location.

== Description ==

Cronblocks for Wordpress is a port of http://cronblocks.com. It allows the site owner to create snippets of content that are displayed according to the visitor's Geo-Location or a given Date and Time.

Assign multiple Snippets to a Snippet Group to control which ones are displayed by the shortcode. Handle overlapping Snippets by setting Priorities. 

Usage Examples:

1. Sell the same page area to multiple advertisers based on time of day (allows you to multiply your gains and set different rates for the same space like 'Prime Time')
1. Choose which affiliate links to display according to your visitor's country (e.g. display amazon.ca, amazon.de, amazon.co.uk, amazon.com affiliate links to visitors from Canada, Germany, the UK, and everywhere else, respectively)

== Installation ==

1. Upload cronblocks.zip to your blog's wp-content/plugins directory;
1. Activate the plugin in your Plugin Admin interface;
1. Create Snippets and assign them to Snippet Groups;
1. Place the shortcode in a post/page/widget where you want your snippet to appear; 

== Frequently Asked Questions ==

= Why can I only select Country (and not Region or City) for Geo-Location? =

- This plugin uses Maxmind's GeoIP database to identify your visitor's Geo-Location. To identify a Region or City, we'd need to use a different database which, zipped, takes up 18MB - just way too large for a WordPress Plugin. 

= Can I choose multiple conditions for a given snippet? =

- No, current functionality only allows a snippet to answer to a single scheduling rule OR to a single Geo-Location rule. A workaround is to create multiple snippets with the same content and different rules.   

= Can I embed snippets inside snippets? =

- Yes, you can. Just be careful not to create an infinite loop by assigning a group that contains the current snippet.

= What about snippets in widgets? =

- That works, too - we turn on do_shortcodes() in widgets if it's not already turned on.

= How do I control snippet positioning in a post or page? =

- Using WordPress' text editor, you can add whatever content or HTML that you want, including CSS. Wrap the shortcode in a div and use CSS to style your post however you prefer.

= I'd like to extend the functionality. Will you do that? =

- Contact us to tell us what you need. We do plan to build a Premium add-on plugin which will offer the following:
 * Enable multiple control conditions of each type, and mix/match 
 * Keep and show statistics of snippets being displayed
 * Optionally display snippets based on the visitor's local time    
 * Optionally reload the snippet group after a given amount of time (say a visitor is watching a video, reload the snippet group after 3 minutes)
 * Enable Region and City scope
 * Use Maxmind GeoIP2 database, which is more accurate than the GeoIP   
 * Allow overriding of snippet options via the shortcode attributes
 * Set up roles that can access the snippet administration    
 * Add an icon to the TinyMCE interface as a shortcut to inserting the shortcode        
 * Add a Snippet Group Widget

== Screenshots ==

1. Cronblocks Snippet Post Type
2. Cronblocks Snippet Groups (works like Category)
3. Cronblocks Geo-Location Snippet Controls
3. Cronblocks Weekly Scheduling Snippet Controls
4. Cronblocks Monthly Scheduling Snippet Controls

== Changelog ==
= 1.0.1 =
* Minor fixes and adjustments

= 1.0 =
* Initial release

== Upgrade Notice ==
No need to upgrade at this time
