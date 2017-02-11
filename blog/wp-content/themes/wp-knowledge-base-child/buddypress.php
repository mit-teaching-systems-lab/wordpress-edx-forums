<?php

/**
 * buddypress
 *
 * @package buddypress
 * @subpackage Theme
 */
get_header( 'buddypress' ); ?>
	<div id="primary" class="content-area col-md-8">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

				<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() )
					comments_template();
				?>

			<?php endwhile; // end of the loop. ?>


	</div><!-- #primary -->
<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer(); ?>

