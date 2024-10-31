<?php
/**
 * Loop title
 *
 * This template can be overridden by copying it to yourtheme/listings/loop/title.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php the_title( '<h3 class="title entry-title"><a href="' . get_the_permalink() . '" title="' . get_the_title() . '">', '</a></h3>' ); ?>