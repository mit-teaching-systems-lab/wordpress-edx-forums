=== bbPress WP Tweaks ===
Contributors: veppa
Tags: bbpress,forum,sidebar,login links,forum sidebar,widgets, forum widgets,bbpress sidebar,bbpress tweaks,tweaks,theme fixes,theme,template,template fixes,bbPress WP Tweaks
Requires at least: 3.3.1
Tested up to: 3.5.2
Stable tag: trunk

Tweaks to integrate bbPress 2.0 and later to your current wordpress theme by adding forum specific sidebar and login widget. 

== Description ==

bbPress WP Tweaks replaces regular sidebar with forum specific sidebar. When forum page loaded then forum specific sidebar will be displayed. If nothing in forum sidebar then regular sidebar will be shown. If no sidebar displayed then change default template from plugin settings. you can choose which forum wrapper template to use in plugin settings page.

Features:

* bbPress specific sidebar instead of main sidebar.
* default wrapper for forum pages
* bbPress login links widget

**bbPress specific sidebar** - you can use different sidebar on forum pages. Supports most widgets enabled templates that use main sidebar.

**default wrapper for forum pages** - bbPRess uses wrapper file in your theme in this order: 'bbpress.php',	'forum.php', 'page.php', 'single.php', 'index.php'. you can choose which teplate to check first. Most templates don't have sidebar in 'page.php', 'single.php' files. 'index.php' always has sidebar. If you cannot see forum sidebar then change this value to index.php in plugin settings (settins -> bbPress WP Tweaks ) page.

**bbPress login links widget** - if you want to display login and register links instead of login form in your sidebar then use this widget. By default bbPress will not show login links to visitors if they want to post in forum. Use this widget instead of login form in your bbPress sidebar.

= Demo =

Check out one of my sites' [bbPress forum page](http://www.veppa.com/blog/forums/).

= Plugin home page =

[bbPress wp tweaks plugin page](http://www.veppa.com/blog/bbpress-wp-tweaks/).

== Installation ==

###Updgrading From A Previous Version###

To upgrade from a previous version of this plugin, delete the entire folder and files from the previous version of the plugin and then follow the installation instructions below.

###Installing The Plugin###

Extract all files from the ZIP file, making sure to keep the file structure intact, and then upload it to `/wp-content/plugins/`.

This should result in the following file structure:

`- wp-content
    - plugins
        - bbpress-wp-tweaks
            | readme.txt
            | bbpress-wp-tweaks.php`

Then just visit your admin area and activate the plugin.

**See Also:** ["Installing Plugins" article on the WP Codex](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)

###Using The Plugin###

* Add bbPress login links and other bbPress related widgets to bbPress sidebar in appearence -> widgets
* Select forum wrapper file in plugin settings page (Settings -> bbPress WP Tweaks) 

== Frequently Asked Questions ==

= Does this plugin support other languages? =

Yes, it does. See the [WordPress Codex](http://codex.wordpress.org/Translating_WordPress) for details on how to make a translation file. Then just place the translation file, named `bbpress-wp-tweaks-[value in wp-config].mo`, into the plugin's folder.


== ChangeLog ==
**Version 1.3.1**

* Minor bugfixes

**Version 1.3**

* Added compatability to Wordpress version 3.5 and greater

**Version 1.2**

* Added compatability to bbPress version 2.1 and greater
* Displaying existing forum wrappers in bold in plugin settings.

**Version 1.1**

* Changed how main sidebar is detected. This adds compatability to more themes.

**Version 1.0.0**

* Initial release of my edition of the plugin.