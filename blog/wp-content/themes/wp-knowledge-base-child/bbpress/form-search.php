<?php

/**
 * Search
 *
 * @package bbPress
 * @subpackage Theme
 */
// Taken from http://sevenspark.com/tutorials/how-to-search-a-single-forum-with-bbpress and wp-admin/includes/class-wp-comments-list-table.php
// All forum specific search code was taken from http://sevenspark.com/tutorials/how-to-search-a-single-forum-with-bbpress
$forum_id = bbp_get_forum_id();
?>

<form role="search" method="get" id="bbp-search-form" action="<?php bbp_search_url(); ?>">
        <label class="screen-reader-text hidden" for="bbp_search"><?php _e( 'Search for:', 'bbpress' ); ?></label>
        <input type="hidden" name="action" value="bbp-search-request" />
        <div class="form-group">
                <div class="input-group">	
			<?php
			$myrealtitle = "";
			// Add the forum specific watermark only if this is a forum
			// Possibly helpful: https://bbpress.org/forums/topic/php-condition-to-detect-that-we-are-in-the-forum/
			if($forum_id){
				// Some pages add links to the title(e.g. groups, and group-forums), so we need to get rid of it (because it messes up the searchbox)
				$myposttitle=get_the_title();
				$matches = array();
				// http://php.net/manual/en/function.preg-match.php
				// https://regex101.com/
				$myrealtitle= $myposttitle;
				if(preg_match("/([^<]*)(.*)/",$myposttitle,$matches)){
					if(count($matches)>1){
						$myrealtitle = $matches[1];
						$myrealtitle = trim(trim($myrealtitle),"\"");
					}
					$matches=array();
					// http://stackoverflow.com/questions/718986/checking-if-the-string-is-empty
					if($myrealtitle==""){
						if(preg_match("/(<a.*>)([^<]*)(<\/a>)/",$myposttitle,$matches)){
							if(count($matches)>2){
								$myrealtitle = $matches[2];
							}
						}
					}
				}
				// http://php.net/trim
				$myrealtitle = "'".trim(trim($myrealtitle))."' ";
			}
			?>
                        <input class="form-control" tabindex="<?php bbp_tab_index(); ?>" type="text" value="<?php echo esc_attr( bbp_get_search_terms() ); ?>" name="bbp_search" id="bbp_search" placeholder="<?php printf( __( 'Search %s&hellip;', 'ipt_kb' ), $myrealtitle.bbp_get_forum_archive_title());?>" />
                        <?php if( $forum_id ): ?>
                        <input class="button" type="hidden" name="bbp_search_forum_id" value="<?php echo $forum_id; ?>" />
                        <?php endif; ?>
                        <span class="input-group-btn"><button type="submit" class="btn btn-default"><span class="ipt-icon-search"></span></button></span>
                </div>
        </div>
</form>
