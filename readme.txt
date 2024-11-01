=== WP-Infobox ===
Contributors: windyjonas
Donate link: http://flattr.com/thing/700831/plugins
Tags: infobox, content filter, info box, more info
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 0.8
 
Add an info box to individual posts

== Description ==

Add an info box to posts. Only displayed on single posts pages, You can include:

* Title
* Lead in, free text below title
* Bullet list, max number of items is configurable
* Copy, free text below list

Use the included css, or put your own wp-infobox.css in your theme directory.

Requires php5!

== Installation ==

1. Upload the wp-infobox directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
1. If you get this error message when the plugin is activated:
"Parse error: syntax error, unexpected T_STRING, expecting T_OLD_FUNCTION or T_FUNCTION or T_VAR or '}' in /wp-content/plugins/wp-infobox/wp-infobox.php on line 32"
You are probably running php4. WP-Infobox requires php5. You have to upgrade to php5, or contact your Web host and ask for instructions on how to run php5 code on your site.

The plugin requires PHP5 and above to work. While most Web hosts have already upgraded their system to support php5, there are still some of them that are using php4. For this issue, you have to contact your Web host and ask for instructions to run php5 code on your site.

== Screenshots ==
1. Example of an infobox in the wild
2. Meta box for wp-infobox

== Changelog ==

= 0.8 =
* tested on most recent version of WordPress

= 0.7 =
* Added possibility to customise css
* New setting "include css", possible to disable css include completely.
* Bug fix: html-code in infobox fields
* Bug fix: list-style-position now explicitly set to "inside"

= 0.5 =
* Added settings page for max number of items

= 0.4 =
* Added settings page for max number of items

= 0.1 =
* Initial version
