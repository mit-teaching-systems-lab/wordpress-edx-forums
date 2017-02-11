<?php
/*
Plugin Name: WP Content Filter
Plugin URI: http://wordpress.org/plugins/wp-content-filter/
Description: Filter out profanity, swearing, abusive comments and any other keywords from your site.
Version: 2.47
Author: David Gwyer
Author URI: http://www.wpgothemes.com
*/

/*  Copyright 2009 David Gwyer (email : david@wpgothemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// @todo
//
// 1. Add code as suggested here, to make sure Ajax generated content is filtered: http://wordpress.org/support/topic/filter-on-ajax-calls?replies=1
//    Make sure this works. I'm not sure from first impressions how it allows Ajax content to be filtered. Will need to look into it more and do some tests.
// 1. Filter browser title.
// 2. Somehow flag/identify/segregate the posts and comments that have the offending content. That way the admin could deal with the offender, and modify the post or comment content as they see fit. As it stands now, the only way to find the bad content is to visually see it on the site. This will have to be via a manual scan of the db as trying to capture it via the front end won't work as it only records content captured on viewed pages. Other content that would be filtered (if viewed) wouldn't be recorded in the list of captured items.
// 3. Put all CSS in a separate file, and enqueue only on the Plugin options page. Or (maybe better way) just hide/collapse the textarea from view on the Plugin settings page and show if clicked on an expand button to hide the profanity keywords by default.
// 4. Allow what kind of filtering you want to use for a certain list of words. Some words to be strict filtered and some other list of words to be non-strict filtered. i.e. Strict: a,b,c Non-strict: aa,bb,cc.
// 5. Hooks to filter BuddyPress content:
// Forum titles, topics and replies
// add_filter('bp_get_the_topic_title', 'pccf_filter');
// add_filter('bp_get_the_topic_post_content', 'pccf_filter');
//
// Activity title & content
// add_filter('bp_get_activity_action', 'pccf_filter');
// add_filter('bp_get_activity_content_body', 'pccf_filter');
// 6. See if we can filter custom post type content, comments, titles etc.
// 7. Maybe offer an alternative to edit keywords via a text file instead of the textarea in theme options. If I implement this then have a checkbox or radio buttons to select one or the other. Hide the keywords textarea if text file preferred and show a text box for a user to enter a path to a text file. Should probably only allow same domain access and validate text file can be found when saving options. If text file can't be found then show warning message.
// 8. Add the ability to Remove just some words e.g Fudge can be changed to just "F*dge" only the u is Removed. Also, maybe you can allow us Specify the Banned Keywords and What to Replace them with?

// pccf_ prefix is derived from [p]ress [c]oders [c]ontent [f]ilter
register_activation_hook( __FILE__, 'pccf_add_defaults' );
register_uninstall_hook( __FILE__, 'pccf_delete_plugin_options' );
add_action( 'admin_init', 'pccf_init' );
add_action( 'admin_menu', 'pccf_add_options_page' );
add_action( 'plugins_loaded', 'pccf_contfilt' );
add_filter( 'plugin_action_links', 'pccf_plugin_action_links', 10, 2 );

// Setup Plugin default options
global $pccf_defaults;
$pccf_defaults = array(
	"chk_post_title"         => "1",
	"chk_post_content"       => "1",
	"chk_comments"           => "1",
	"txtar_keywords"         => "Saturn, Jupiter, Pluto",
	"txt_exclude"            => "",
	"rdo_word"               => "all",
	"drp_filter_char"        => "star",
	"rdo_case"               => "insen",
	"chk_default_options_db" => "",
	"rdo_strict_filtering"   => "strict_on"
);

// ***************************************
// *** START - Create Admin Options    ***
// ***************************************

// delete options table entries ONLY when plugin deactivated AND deleted
function pccf_delete_plugin_options() {
	delete_option( 'pccf_options' );
}

// Define default option settings
function pccf_add_defaults() {

	global $pccf_defaults;
	$tmp = get_option( 'pccf_options', $pccf_defaults );

	if ( ( ( isset( $tmp['chk_default_options_db'] ) && $tmp['chk_default_options_db'] == '1' ) ) || ( ! is_array( $tmp ) ) ) {
		update_option( 'pccf_options', $pccf_defaults );
	}
}

// Init plugin options to white list our options
function pccf_init() {
	// put the below into a function and add checks all sections (especially radio buttons) have a valid choice (i.e. no section is blank)
	// this is primarily to check newly added options have correct initial values
	global $pccf_defaults;
	$tmp = get_option( 'pccf_options', $pccf_defaults );

	if ( ! $tmp['rdo_strict_filtering'] ) { // check strict filtering option has a starting value
		$tmp["rdo_strict_filtering"] = "strict_off";
		update_option( 'pccf_options', $tmp );
	}
	register_setting( 'pccf_plugin_options', 'pccf_options', 'pccf_validate_options' );
}

// Add menu page
function pccf_add_options_page() {
	add_options_page( 'WP Content Filter Options Page', 'WP Content Filter', 'manage_options', __FILE__, 'pccf_render_form' );
}

// Draw the menu page itself
function pccf_render_form() {
	?>
	<div class="wrap">
		<h2>WP Content Filter Options</h2>

		<form method="post" action="options.php">
			<?php settings_fields( 'pccf_plugin_options' ); ?>
			<?php
			global $pccf_defaults;
			$options = get_option( 'pccf_options', $pccf_defaults );
			?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Keywords to Remove</th>
					<td>
						<textarea name="pccf_options[txtar_keywords]" rows="7" cols="50" type='textarea'><?php echo $options['txtar_keywords']; ?></textarea>

						<p class="description">Separate keywords with commas.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Exclude Pages</th>
					<td>
						<input type="text" class="regular-text code" name="pccf_options[txt_exclude]" value="<?php echo $options['txt_exclude']; ?>" />

						<p class="description">Enter a comma separate list of page ID's that will be excluded from the content filter.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Strict Filtering</th>
					<td>
						<label><input name="pccf_options[rdo_strict_filtering]" type="radio" value="strict_off" <?php checked( 'strict_off', $options['rdo_strict_filtering'] ); ?> /> Strict Filtering OFF
							<span style="font-family:lucida console;color:#888;margin-left:119px;">[e.g. 'ass' becomes 'p***able']</span></label><br />
						<label><input name="pccf_options[rdo_strict_filtering]" type="radio" value="strict_on" <?php checked( 'strict_on', $options['rdo_strict_filtering'] ); ?> /> Strict Filtering ON (recommended)
							<span style="font-family:lucida console;color:#888;margin-left:32px;">[e.g. 'ass' becomes 'passable']</span></label>

						<p class="description">Note: When strict filtering is ON, embedded keywords are no longer filtered.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Filter main content</th>
					<td>
						<label><input name="pccf_options[chk_post_content]" type="checkbox" value="1" <?php if ( isset( $options['chk_post_content'] ) ) {
								checked( '1', $options['chk_post_content'] );
							} ?> /> Post Content/Excerpt<?php if ( class_exists( 'bbPress' ) ) {
								echo " (including bbPress content)";
							} ?></label><br />
						<label><input name="pccf_options[chk_post_title]" type="checkbox" value="1" <?php if ( isset( $options['chk_post_title'] ) ) {
								checked( '1', $options['chk_post_title'] );
							} ?> /> Post Titles<?php if ( class_exists( 'bbPress' ) ) {
								echo " (including bbPress titles)";
							} ?></label><br />
						<label><input name="pccf_options[chk_comments]" type="checkbox" value="1" <?php if ( isset( $options['chk_comments'] ) ) {
								checked( '1', $options['chk_comments'] );
							} ?> /> Comments <span class="description">(filters comment author names too)</span></label><br />
						<label><input name="pccf_options[chk_tags]" type="checkbox" value="1" <?php if ( isset( $options['chk_tags'] ) ) {
								checked( '1', $options['chk_tags'] );
							} ?> /> Tags</label><br />
						<label><input name="pccf_options[chk_tag_cloud]" type="checkbox" value="1" <?php if ( isset( $options['chk_tag_cloud'] ) ) {
								checked( '1', $options['chk_tag_cloud'] );
							} ?> /> Tag Cloud</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Word Rendering</th>
					<td>
						<label><input name="pccf_options[rdo_word]" type="radio" value="first" <?php checked( 'first', $options['rdo_word'] ); ?> /> First letter retained
							<span style="font-family:lucida console;color:#888;margin-left:39px;">[e.g. 'dog' becomes 'd**']</span></label><br />
						<label><input name="pccf_options[rdo_word]" type="radio" value="all" <?php checked( 'all', $options['rdo_word'] ); ?> /> All letters removed
							<span style="font-family:lucida console;color:#888;margin-left:40px;">[e.g. 'dog' becomes '***']</span></label><br />
						<label><input name="pccf_options[rdo_word]" type="radio" value="firstlast" <?php checked( 'firstlast', $options['rdo_word'] ); ?> /> First/last letter retained
							<span style="font-family:lucida console;color:#888;margin-left:16px;">[e.g. 'dog' becomes 'd*g']</span></label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Filter Character</th>
					<td>
						<select name='pccf_options[drp_filter_char]'>
							<option value='star' <?php selected( 'star', $options['drp_filter_char'] ); ?>>[*] Asterisk</option>
							<option value='dollar' <?php selected( 'dollar', $options['drp_filter_char'] ); ?>>[$] Dollar</option>
							<option value='question' <?php selected( 'question', $options['drp_filter_char'] ); ?>>[?] Question</option>
							<option value='exclamation' <?php selected( 'exclamation', $options['drp_filter_char'] ); ?>>[!] Exclamation</option>
							<option value='hyphen' <?php selected( 'hyphen', $options['drp_filter_char'] ); ?>>[-] Hyphen</option>
							<option value='hash' <?php selected( 'hash', $options['drp_filter_char'] ); ?>>[#] Hash</option>
							<option value='tilde' <?php selected( 'tilde', $options['drp_filter_char'] ); ?>>[~] Tilde</option>
							<option value='blank' <?php selected( 'blank', $options['drp_filter_char'] ); ?>>[ ] Blank</option>
						</select>

						<p class="description">'Blank' completely removes the filtered keywords from view.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Case Matching</th>
					<td>
						<label><input name="pccf_options[rdo_case]" type="radio" value="sen" <?php checked( 'sen', $options['rdo_case'] ); ?> /> Case Sensitive</label><br />
						<label><input name="pccf_options[rdo_case]" type="radio" value="insen" <?php checked( 'insen', $options['rdo_case'] ); ?> /> Case Insensitive (recommended)</label>

						<p class="description">Note: 'Case Insensitive' matching type is better as it captures more words!</p>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2">
						<div style="margin-top:10px;"></div>
					</td>
				</tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td>
						<label><input name="pccf_options[chk_default_options_db]" type="checkbox" value="1" <?php if ( isset( $options['chk_default_options_db'] ) ) {
								checked( '1', $options['chk_default_options_db'] );
							} ?> /> Restore defaults upon plugin deactivation/reactivation</label>

						<p class="description">Only check this if you want to reset plugin settings upon reactivation.</p>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
			</p>
		</form>

		<?php

		$discount_date = "14th August 2014";
		if( strtotime($discount_date) > strtotime('now') ) {
			echo '<p style="background: #eee;border: 1px dashed #ccc;font-size: 13px;margin: 0 0 10px 0;padding: 5px 0 5px 8px;">For a limited time only! <strong>Get 50% OFF</strong> the price of our brand new mobile ready, fully responsive <a href="http://www.wpgothemes.com/themes/minn/" target="_blank"><strong>Minn WordPress theme</strong></a>. Simply enter the following code at checkout: <code>MINN50OFF</code></p>';
		} else {
			echo '<p style="background: #eee;border: 1px dashed #ccc;font-size: 13px;margin: 0 0 10px 0;padding: 5px 0 5px 8px;">As a user of our free plugins here\'s a bonus just for you! <strong>Get 30% OFF</strong> the price of our brand new mobile ready, fully responsive <a href="http://www.wpgothemes.com/themes/minn/" target="_blank"><strong>Minn WordPress theme</strong></a>. Simply enter the following code at checkout: <code>WPGO30OFF</code></p>';
		}

		?>

		<div style="clear:both;">
			<p>
				<a href="http://www.twitter.com/dgwyer" title="Follow me Twitter!" target="_blank"><img src="<?php echo plugins_url(); ?>/wp-content-filter/images/twitter.png" /></a>&nbsp;&nbsp;
				<input class="button" style="vertical-align:12px;" type="button" value="Visit Our NEW Site!" onClick="window.open('http://www.wpgothemes.com')">
				<input class="button" style="vertical-align:12px;" type="button" value="Minn, Our Latest Theme" onClick="window.open('http://www.wpgothemes.com/themes/minn')">
			</p>
		</div>

	</div>
<?php
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function pccf_validate_options( $input ) {
	// strip html from textboxes
	$input['txtar_keywords'] = wp_filter_nohtml_kses( $input['txtar_keywords'] );
	$input['txt_exclude']    = wp_filter_nohtml_kses( $input['txt_exclude'] );

	return $input;
}

// ***************************************
// *** END - Create Admin Options    ***
// ***************************************

// ---------------------------------------------------------------------------------

// ***************************************
// *** START - Plugin Core Functions   ***
// ***************************************

function pccf_contfilt() {

	if ( is_admin() ) {
		return;
	} /* Only filter front end content. */

	global $pccf_defaults;
	$tmp = get_option( 'pccf_options', $pccf_defaults );

	if ( isset( $tmp['chk_post_content'] ) ) {
		if ( $tmp['chk_post_content'] == '1' ) {
			add_filter( 'the_content', 'pccf_filter' );
			add_filter( 'get_the_excerpt', 'pccf_filter' );
		}

		/* bbPress specific filtering (only if bbPress is present). */
		if ( class_exists( 'bbPress' ) ) {
			add_filter( 'bbp_get_topic_content', 'pccf_filter' );
			add_filter( 'bbp_get_reply_content', 'pccf_filter' );
		}
	}
	if ( isset( $tmp['chk_post_title'] ) ) {
		if ( $tmp['chk_post_title'] == '1' ) {
			add_filter( 'the_title', 'pccf_filter' );
		}
	}
	if ( isset( $tmp['chk_comments'] ) ) {
		if ( $tmp['chk_comments'] == '1' ) {
			add_filter( 'comment_text', 'pccf_filter' );
		}
	}
	if ( isset( $tmp['chk_comments'] ) ) {
		if ( $tmp['chk_comments'] == '1' ) {
			add_filter( 'get_comment_author', 'pccf_filter' );
		}
	}
	if ( isset( $tmp['chk_tags'] ) ) {
		if ( $tmp['chk_tags'] == '1' ) {
			add_filter( 'term_links-post_tag', 'pccf_filter' );
		}
	}
	if ( isset( $tmp['chk_cloud'] ) ) {
		if ( $tmp['chk_cloud'] == '1' ) {
			add_filter( 'wp_tag_cloud', 'pccf_filter' );
		}
	}
}

function pccf_filter( $text ) {

	global $post;

	// get comma separated list of page ID's to exclude
	global $pccf_defaults;
	$tmp = get_option( 'pccf_options', $pccf_defaults );

	$exclude_id_list  = $tmp['txt_exclude'];
	$exclude_id_array = explode( ', ', $exclude_id_list );

	// if current page ID is in exclude list then don't filter
	if ( isset( $post ) && in_array( $post->ID, $exclude_id_array ) ) {
		return $text;
	}

	$wildcard_filter_type = $tmp['rdo_word'];
	$wildcard_char        = $tmp['drp_filter_char'];

	if ( $wildcard_char == 'star' ) {
		$wildcard = '*';
	} else {
		if ( $wildcard_char == 'dollar' ) {
			$wildcard = '$';
		} else {
			if ( $wildcard_char == 'question' ) {
				$wildcard = '?';
			} else {
				if ( $wildcard_char == 'exclamation' ) {
					$wildcard = '!';
				} else {
					if ( $wildcard_char == 'hyphen' ) {
						$wildcard = '-';
					} else {
						if ( $wildcard_char == 'hash' ) {
							$wildcard = '#';
						} else {
							if ( $wildcard_char == 'tilde' ) {
								$wildcard = '~';
							} else {
								$wildcard = '';
							}
						}
					}
				}
			}
		}
	}

	$filter_type      = $tmp['rdo_case'];
	$db_search_string = $tmp['txtar_keywords'];
	$search           = explode( ",", $db_search_string );
	$search           = array_unique( $search ); // get rid of duplicates in the keywords textbox

	if ( $tmp['rdo_strict_filtering'] == 'strict_off' ) {
		// If strict filtering is OFF - use the standard str_ireplace, and str_ireplace functions
		foreach ( $search as $sub_search ) {
			$sub_search = trim( $sub_search ); // remove whitespace chars from start/end of string
			if ( strlen( $sub_search ) > 2 ) {
				if ( $wildcard_filter_type == 'first' ) {
					$tmp_search = substr( $sub_search, 0, 1 ) . str_repeat( $wildcard, strlen( substr( $sub_search, 1 ) ) );
				} else {
					if ( $wildcard_filter_type == 'all' ) {
						$tmp_search = str_repeat( $wildcard, strlen( substr( $sub_search, 0 ) ) );
					} else {
						$tmp_search = substr( $sub_search, 0, 1 ) . str_repeat( $wildcard, strlen( substr( $sub_search, 2 ) ) ) . substr( $sub_search, - 1, 1 );
					}
				}
				if ( $filter_type == "insen" ) {
					$text = str_ireplace( $sub_search, $tmp_search, $text );
				} else {
					$text = str_replace( $sub_search, $tmp_search, $text );
				}
			}
		}
	} else {
		// If strict filtering is ON - use regular expressions for more powerful seach and replace
		foreach ( $search as $sub_search ) {
			$sub_search = trim( $sub_search ); // remove whitespace chars from start/end of string
			if ( strlen( $sub_search ) > 2 ) {
				if ( $wildcard_filter_type == 'first' ) {
					$tmp_search = substr( $sub_search, 0, 1 ) . str_repeat( $wildcard, strlen( substr( $sub_search, 1 ) ) );
				} else {
					if ( $wildcard_filter_type == 'all' ) {
						$tmp_search = str_repeat( $wildcard, strlen( substr( $sub_search, 0 ) ) );
					} else {
						$tmp_search = substr( $sub_search, 0, 1 ) . str_repeat( $wildcard, strlen( substr( $sub_search, 2 ) ) ) . substr( $sub_search, - 1, 1 );
					}
				}
				if ( $filter_type == "insen" ) {
					$text = str_replace_word_i( $sub_search, $tmp_search, $text );
				} else {
					$text = str_replace_word( $sub_search, $tmp_search, $text );
				}
			}
		}
	}

	return $text;
}

// case insensitive
function str_replace_word_i( $needle, $replacement, $haystack ) {
	$needle   = str_replace( '/', '\\/', preg_quote( $needle ) ); // allow '/' in keywords
	$pattern  = "/\b$needle\b/i";
	$haystack = preg_replace( $pattern, $replacement, $haystack );

	return $haystack;
}

// case sensitive
function str_replace_word( $needle, $replacement, $haystack ) {
	$needle   = str_replace( '/', '\\/', preg_quote( $needle ) ); // allow '/' in keywords
	$pattern  = "/\b$needle\b/";
	$haystack = preg_replace( $pattern, $replacement, $haystack );

	return $haystack;
}

// Display a Settings link on the main Plugins page
function pccf_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$posk_links = '<a href="' . get_admin_url() . 'options-general.php?page=wp-content-filter/wp-content-filter.php">' . __( 'Settings' ) . '</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $posk_links );
	}

	return $links;
}

// ***************************************
// *** END - Plugin Core Functions     ***
// ***************************************