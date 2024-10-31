=== Related Links Blender ===
Contributors: stephenblender
Donate link: http://blender.ca/payment
Tags: related links, relevant links, cross linking, visitor retention, related posts
Requires at least: 3.5.0
Tested up to: 4.8.2
Stable tag: 0.81
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Related Links Blender plugin provides a easy way to cross link posts. Target posts or external links with thumbs and SEO friendly markup.

== Description ==

The *Related Links Blender* plugin to makes it easy to cross link your posts and link to external web pages. Your visitors will be provided with hand picked topical reading options at the end of the current post.  Search engines will see more carefully SEO crafted content.

Related links are selected and defined on a post's editor page in a custom panel. These links are added for viewing at the end of a post's or page's content.

Construct a link using the tools in the Related Links Blender post edit panel. Use the FIND A POST tool to search through your existing posts to quickly create rich links, or manually enter a link to any webpage you want. Links are pre-compiled for efficient display. This is the preferred way to add links - tuned for visitor's interest and SEO. 

The Related Links Blender plugin has a settings page in which you can customise how the inserted links are constructed and styled.  This requires some basic HTML knowledge.

NOTES

1. This plugin is a beta release that is under development. Please report any issues!
2. This plugin is intended for advanced users and not documented for causual or beginner users
3. Want to send a donation?: http://blender.ca/payment


== Installation ==

The Related Links Blender plugin is available for download from the Wordpress plugin repository, INSTALL then ACTIVATE.

Or download and install from: 

http://www.blender.ca/wordpress-plugin-related-links/

1. Upload the *related-links-blender* folder to you WordPress installation's */wp-content/plugins/* directory
2. ACTIVATE the plugin through the 'Plugins' menu in WordPress

Once the plugin is active you will have to visit the settings page and choose the option to start adding links to pages.


== Frequently Asked Questions ==

**Can you link outside to other website?**
--YES! New feature in v04.

**Is there an easier way to choose targets?**
--YES! New feature in v05. FIND A POST button for searching for relevant post to link to

**Should I use this in my production website?**
--not yet, hopefully soon

**What is a post ID?**
--Every post in Wordpress has a unique ID.  It's not always easy to find, so I have included it in the options pane on a posts editor page. 


== Screenshots ==

1. A sample link added to a post. You can customize the construction and appearance of links in the settings.
2. The post editing panel where the magic happens. The post finder used to quickly track down the perfect post for linking.
3. The plugin's options panel where you style your links.
4. A sample of an alternate layout styled by one user.

== Changelog ==

= 0.81 =
Added option to specify external link to open in new window/tap
Bug fixes

= 0.8 =
Bug fix. (admin.js - panel setup made conditional on panel existance)
Tested WP 4.8.2
Tested PHP 7.1

= 0.76 =
Bug fix. (admin.js .rlb-hidden class not triggering. Thanks s-design!)

= 0.75 =
Bug fix. (admin.css .hidden stepping on on wordpress stylesheet. Thanks s-design!)

= 0.74 =
Bug fix. (admin.php error report line 221, fix edit line 540.)

= 0.73 =
User feature request: add related links to pages.  Insertion implemented, but pages not yet included in link searcher

= 0.72 =
User feature request: template function $relatedlinksblender->insert_the_links(); to insert links.  This necessarily ignores the settings option to 'add links to posts' which you will probably want to turn off.

= 0.71 =
bug fix

= 0.7 =
drag the links you have created into the order you want

= 0.6 =
clear form control for link editor control pane
scaling thumbs in link editor pane

= 0.5 =
big changes including the addition of a Post seaching tool to make linking easy


