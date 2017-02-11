=== BuddyMenu BuddyLinks ===
Contributors: leehodson
Tags: Links, BuddyPress Shortcodes, Link shortcodes, BuddyPress Menu Widget, BuddyPress Menu Shortcode
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=lee@wpservicemasters.com&currency_code=USD&amount=&item_name=Donation%20to%20JournalXtra&return=http://journalxtra.com/thank-you/&notify_url=&cbt=Thank%20you%20for%20your%20donation,%20it%20is%20greatly%20appreciated&page_style=
Requires at least: 3.0
Tested up to: 3.7
Stable tag: 2.2.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

BuddyPress menu widget and menu shortcode. Does three things really well: BuddyPress menu widget, BuddyPress menu shortcode and BuddyPress dynamic link shortcode.

== Description ==

BuddyPress BuddyLinks does three things really well:

1. BuddydMenu BuddyPress menu widget.
1. BuddyMenu BuddyPress menu shortcode.
1. BuddyLinks BuddyPress dynamic link shortcode.

Put a BuddyPress menu or dynamic user link in your sidebar, post, page, widget, footer or anywhere else. Needs BuddyPress to work. There is no need to network activate in WP Multisite. Does not display to logged out users.

BuddyPress menus are dynamic. Their URLs change for each logged in user. This makes it difficult for most community admins and webmasters to add links to areas in their BuddyPress sites. Unless you know how, you can't create a welcome page and send new users to their profile page with a link in the welcome page because the link will be different for each user.

For example, a link to a user's own activity stream would look like *http://example.com/members/[USERNAME]/activity/*.

The [USERNAME] bit of the URL is added dynamically by BuddyPress. This means you need to use pictures, videos, diagrams and lots of text to send people to different locations within a BP site or you can use the widget and shortcodes this BuddyMenu BuddyLinks provides.

See 'Other Notes' section usage guide.

= BuddyMenu Widget =

The BuddyMenu Widget displays the main BP menus items:

1. Activity
1. Forums
1. Friends
1. Messages
1. Profile
1. Edit Profile
1. Change Avatar
1. Settings
1. Menu
1. Custom Links

= BuddyMenu Widget's Features =

1. Choose which menu tabs to display.
1  Choose display order of menu tabs.
1. Custom menu items.
1. You can specify anchor text for each link.
1. You can choose to display the menu as a vertical list or as a horizontal bar.
1. The widget displays to those who are logged in only. Logged out users do not see the menu.
1. Beautiful and stylish menu icons next to each menu link.
1. Customize icons.
1. Choose whether to display icons or not.
1. Customize menu tab titles.

As easy to use as going to *Appearance > Widgets* and dragging the BuddyMenu widget into your sidebar or any other widget area.

= BuddyMenu Shortcode =

BuddyMenu Shortcode displays the BuddyPress menus tabs. Being a shortcode, it can be embedded in any page or post. If you use Ultimate TinyMCE you can even put the shortcode in a widget.

= The BuddyMenu Shortcode Features =

1. Configurable menu titles, just like the widget.
1. Vertical or horizontal alignment.
1. Menu items can be switched off.
1. Menu icons can be enabled or disabled.
1. Menu icons can be customized.

= BuddyLinks Shortcode =

This is for adding single links within page and post content. Ideal for linking to a user's profile, settings, personal activity stream or any other other BuddyPress page from within a post. For example, you've written a getting started guide for new members and you want to send them to upload an avatar; you can use the BuddyLinks shortcode to link to new users' avatar upload page. The dynamic slug is created by BuddyLinks.

BuddyLinks Features

1. In context BuddyPress linking.
1. Ease of use.
1. Link title and link anchor configuration.
1. Quick links to each of the 8 pages found in the BuddyMenus.
1. Configurable text for logged out visitors - tell them to login to follow the link or make the text non-clickable.
1. Icons can be added to links. Use shortcode attributes to use icons from the default icon set or use a URL to a custom icon.

== Installation ==

1. Use the WordPress *Add New Plugins* menu otherwise...
1. Download the zip file from WordPress.org.
1. Upload the *BuddyMenu* file to */wp-content/plugins/*.
1, Extract the zip file.
1. Activate the plugin through the WordPress *plugins* page.
1. See *Appearance > Widgets* to place the *BuddyMenu Widget* in a sidebar.

== Frequently Asked Questions ==

None yet.

== Screenshots ==

1. screenshot-1
2. screenshot-2
3. screenshot-3
4. screenshot-4

== Instructions ==

= BuddyMenu Widget =

1. Go to *Appearance > Settings*
1. Find the BuddyMenu widget
1. Drag it into a widget area
1. Configure the menus to display, their titles, their icons and whether to display the menu horizontally or vertically.

This menu is not visible to visitors who are not logged in.

= BuddyMenu Shortcode =

BuddyMenu Shortcode displays a vertical or horizontal menu. You can change the anchor text of each menu tab. The link title tooltip will always be the same as the anchor text. Any of the menu tabs can be disabled. This menu is not visible to visitors who are not logged in.

*New in version 2: Customize the menu icons*

Enter the shortcode in any page, post or widget area (if you are set up to use shortcodes in widget areas).

The shortcode for the menu is:

[buddymenu]

The basic options/attributes are:

1. `bmact=""` to change the activity tab
1. `bmfor=""` to change the forum tab
1. `bmfri=""` to change the friends tab
1. `bmmsg=""` to change the messages tab
1. `bmpro=""` to change the profile tab
1. `bmedpro=""` to change the edit profile tab
1. `bmchav=""` to change the change avatar tab
1. `bmset=""` to change the settings tab
1. `bmlay=""` to change the layout from horizontal to vertical

If no option is set, the full menu is displayed as a horizontal row. For example, using [buddymenu] will display the full menu where the shorcode is placed.

Setting any option to *-1* will disable that option's menu item.

Any text entered within the quotes of an option will become the link anchor text and the link title for that option's menu item.

For example,

To show the full menu horizontally:

[buddymenu]

To show the full menu as a vertical list:

[buddymenu bmlay="vertical"]

To show the menu without the activity tab:

[buddymenu bmact="-1"]

To show the menu with the message tab text changed to *Inbox*:

[buddymenu bmmsg="Inbox"]

To show a horizontal menu with the settings tab changed to *Configs*:

[buddymenu bmset="Configs" bmlay="horizontal"]

This menu is very useful in horizontal widget areas in multilingual environments.

The icon customization attributes are:

1. `bmacti=""` sets the activity icon.
1. `bmfori=""` sets the forum icon.
1. `bmfrii=""` sets the friends icon.
1. `bmmsgi=""` sets the messages icon.
1. `bmproi=""` sets the profile icon.
1. `bmedproi="" sets the edit profile icon.
1. `bmchavi="" sets the change avatar icon.
1. `bmseti=""` sets the settings icon.
1. `bmicons="0" Disables all icons.

Put the URL to any image into an link icon attribute to use that image as the icon for that link.

For example,

[buddymenu bmicons="0"] Disables all icons.

[buddymenu bmacti="http://example.com/icon.png"] Will replace the default activity icon with the image at http://example.com/icon.png.

= BuddyLinks =

The BuddyLinks shortcode provides a stub URL that is dynamically generated by BuddyPress. This URL looks like *example.com/members/[USERNAME]/*. You need to provide the path that follows the username (represented by the asterisk in this case).

This shortcode is good for putting links to your network users' pages in the content of your pages and posts.

None logged in users see the text "login to view this link" which links to the WordPress login page. This text link is configurable.

BuddyLinks has four main options/attributes:

1. `bllink=""` for setting the link
1. `bltitle=""` for setting the tooltip title
1. `bltext=""` for setting the anchor text that users click
1. `blicon=""` for configuring an icon for the link

If you provide no attributes, the shortcode returns a link to a user's profile page with the anchor text "your profile", no tool-tip title and no icon.

`blicon=""` can be used to display a custom icon or an icon from the plugin's default icon set. Accepted values are activity, friends, messages, profile, edit-profile, avatar and settings or any URL to an image.

For example,

To link to a logged in user's activity page:

[buddylink bmlink="activity" bltext="your activity page"]

To link to a logged in user's message inbox:

[buddylink bmlink="messages/inbox" bltext="view your inbox"]

To link to a logged in user's message inbox with an icon accompanying the link:

[buddylink bmlink="messages/inbox" bltext="view your inbox" blicon="messages"]

There are three options for controlling what non logged in users see:

1. `blolink=""` for setting the link
1. `blotitle=""` for setting the title
1. `blotext=""` for setting the anchor text

By default, a non logged in user will see a link to your site's login/registration page. To change that destination you could do something like this:

[buddylink bmlink="messages/inbox" bltext="view your inbox" blolink="http://example.com/reasons-to-register" blotext="reasons to register"]

= QuickLinks =

BuddyLinks has quicklinks built into it to facilitate quick placement of links to common BuddyPress areas.

Used with the [buddylink] shortcode, the options are:

1. `blq="act"` to link to 'activity'
1. `blq="for"` to link to 'forums'
1. `blq="fri"` to link to 'friends'
1. `blq="msg"` to link to 'messages'
1. `blq="pro"` to link to 'profile'
1. `blq="edpro"` to link to 'edit profile'
1. `blq="chav"` to link to 'change avatar'
1. `blq="set"` to link to 'settings'

Quicklinks can be used in conjuction with the blicon="" attribute.

For example,

To link to a user's activity page:

[buddylink blq="act"]

To link to a user's activity page and print a custom message to non logged in users:

[buddylink blq="act" blotext="You can't go here because you're not logged in!"]

QuickLinks do not accept custom title and link attributes except for those intended for non logged in users.

== FAQ ==

*I'm getting a space between the BuddyLink and my punctuation marks, why is this?*

This is a known bug with BuddyLinks. I am working on a fix. In the meantime, put the punctuation (fullstop, period, comma) in the bltext="" attribute. For example, `bltext="click here."` instead of `bltext="click here".`

== Contact ==

[General support]http://journalxtra.com/websiteadvice/wordpress/use-buddypress-dynamic-links-in-your-network-with-buddymenu-buddylinks/
and
[Commercial support]http://vizred.com/

== Supported Languages ==

* English

== Changelog ==

= 2.2.0 =

* Bug Fix: fixed login/logout menu URL
* Feature Addition: menu welcome message setting

= 2.1.1 =

* Widget - Corrected minor detail with visibility options: added an option to make widget items visible to no one.

= 2.1.0 =

* Widget - Changed visibility options to logged in, logged out and logged in/out.
* Widget - Added option to display logged in user's avatar above menu
* Widget - Added option to set menu icon as user's avatar
* Widget - Added more icons
* Widget - Added more menu links
* Widget - Added option to set non BuddyPress URLs as menu links

= 2.0.0 =

* Major upgrade - Almost complete rewrite
* Widget - Selectable link order
* Widget - Custom link icons
* Widget - Custom links
* Widget - Icon disablement feature
* Shortcodes - Icon inclusion
* Shortcodes - Custom icons
* Widget settings - New, neater look
* Removed hardcoding of icon URL location in CSS
* New icons


= 1.5.8 =

* Minor bug fix - corrected slug for forums URL

= 1.5.7 =

* First public release.

== Upgrade Notice ==

* <span style="color:red">Important:<span> BIG CHANGES TO THE WIDGET</span>. Please check widget settings to configure visibility.