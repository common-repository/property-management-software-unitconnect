<?php
/**
 * Single sale tagline
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/sale.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$show_sale = ucpm_meta( 'show_sale' );
$asking_price = ucpm_meta( 'asking_price' );
$noi = ucpm_meta( 'noi' );
$cap_rate = ucpm_meta( 'cap_rate' );
$status = ucpm_meta( 'status' );
$sale_description = ucpm_meta( 'sale_description' );

if ( isset( $show_sale ) && $show_sale === 'on' ) : ?>
    <h2 class="widget-title"><span><?php esc_html_e( 'Sale Details', 'ucpm' ); ?></span></h2>

    <div class="sale-list">
        <?php if ( ! empty( $asking_price ) ) : ?>
            <div class="sale-list-item"><div><b><?php esc_html_e('Price: ', 'ucpm'); ?></b> $<?php echo esc_html( number_format($asking_price) ); ?></div></div>
        <?php endif; ?>

        <?php if ( ! empty( $noi ) ) : ?>
            <div class="sale-list-item"><div><b><?php esc_html_e('NOI:', 'ucpm'); ?></b> $<?php echo esc_html( number_format($noi) ); ?></div></div>
        <?php endif; ?>

        <?php if ( ! empty( $cap_rate ) ) : ?>
            <div class="sale-list-item"><div><b><?php esc_html_e('CAP Rate:', 'ucpm'); ?></b> <?php echo esc_html( $cap_rate ); ?>%</div></div>
        <?php endif; ?>

        <?php if ( ! empty( $status ) ) : ?>
            <div class="sale-list-item sale-list-item--status"><div><b><?php esc_html_e('Status:', 'ucpm'); ?></b> <?php echo esc_html( str_replace( '-', ' ', $status ) ); ?></div></div>
        <?php endif; ?>
    </div>

    <?php if ( ! empty( $sale_description ) ) : ?>
        <div class="sale-desc"><?php echo wp_kses_post( $sale_description ); ?></div>
    <?php endif; ?>
<?php endif;
