<?php

/**
 * Archive Forum Content Part
 * This file was copied from bbpress
 * and it is used to create a single forum search box with the code from http://sevenspark.com/tutorials/how-to-search-a-single-forum-with-bbpress
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="bbpress-forums">

        <?php if ( bbp_allow_search() ) : ?>

                <div class="bbp-search-form">

                        <?php bbp_get_template_part( 'form', 'search' ); ?>

                </div>

        <?php endif; ?>

        <?php do_action( 'bbp_template_before_forums_index' ); ?>

        <?php if ( bbp_has_forums() ) : ?>

                <?php bbp_get_template_part( 'loop',     'forums'    ); ?>

        <?php else : ?>

                <?php bbp_get_template_part( 'feedback', 'no-forums' ); ?>

        <?php endif; ?>

        <?php do_action( 'bbp_template_after_forums_index' ); ?>

</div>
