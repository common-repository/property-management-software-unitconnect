<?php
/**
 * Single lease tagline
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/lease.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$show_lease = ucpm_meta( 'show_lease' );
$lease_items = ucpm_meta( 'lease_items' );
$lease_description = ucpm_meta( 'lease_description' );

if ( isset( $show_lease ) && $show_lease === 'on' ) : ?>
    <?php if ( ! empty( $lease_items ) || ! empty( $lease_description ) ) : ?>
        <h2 class="widget-title"><span><?php esc_html_e( 'Lease Details', 'ucpm' ); ?></span></h2>
    <?php endif; ?>

    <?php if ( ! empty( $lease_items ) ) : ?>
        <div class="lease-list">
            <?php foreach ( $lease_items as $item ) :
                if ( isset( $item['_ucpm_listing_lease_available'] ) && $item['_ucpm_listing_lease_available'] === 'on' ) : ?>
                    <div class="lease-list-item">
                        <div><b><?php esc_html_e('Space:', 'ucpm'); ?></b> <?php echo esc_html( $item['_ucpm_listing_lease_space'] ); ?></div>
                    </div>

                    <div class="lease-list-item">
                        <div><b><?php esc_html_e('Size:', 'ucpm'); ?></b> <?php echo esc_html( number_format( $item['_ucpm_listing_lease_size'] ) ); ?> <?php esc_html_e('SQFT', 'ucpm'); ?></div>
                    </div>

                    <div class="lease-list-item">
                        <div><b><?php esc_html_e('Description:', 'ucpm'); ?></b> <?php echo esc_html( $item['_ucpm_listing_lease_desc'] ); ?></div>
                    </div>

                    <div class="lease-list-item">
                        <div><b><?php esc_html_e('Asking:', 'ucpm'); ?></b> <?php echo esc_html( number_format( $item['_ucpm_listing_lease_asking'] ) ); ?></div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ( ! empty( $lease_description ) ) : ?>
        <div class="lease-desc"><?php echo wp_kses_post( $lease_description ); ?></div>
    <?php endif; ?>
<?php endif;
