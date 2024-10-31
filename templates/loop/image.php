<?php
/**
 * Loop single image
 *
 * This template can be overridden by copying it to yourtheme/listings/loop/image.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$image 	= ucpm_get_first_image();
$status = ucpm_get_status();

$show_sale = ucpm_meta('show_sale');
$show_lease = ucpm_meta('show_lease');

$status_text = '';

if ( $show_sale === 'on' && $show_lease !== 'on' ) {
    $status_text = 'For Sale';
} elseif ( $show_sale !== 'on' && $show_lease === 'on' ) {
    $status_text = 'For Lease';
} elseif ( $show_sale === 'on' && $show_lease === 'on' ) {
    $status_text = 'For Sale / For Lease';
}
?>

<div class="image">
	<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">

		<?php if ( $show_sale === 'on' && $status ) : ?>
				<span class="status status-listing <?php echo esc_attr( strtolower( str_replace( ' ', '-', $status['status']) ) ); ?>">
					<i class="ucpm-icon-house"></i>
					<?php echo esc_html( $status['status'] ); ?>
				</span>
		<?php endif; ?>

        <?php if ( ! empty( $status_text ) ) : ?>
            <span class="status sale"><?php echo esc_html( $status_text ); ?></span>
        <?php endif; ?>

		<img alt="<?php echo esc_attr( $image['alt'] ); ?>" src="<?php echo esc_url( $image['sml'] ); ?>" />
	</a>
</div>