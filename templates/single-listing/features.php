<?php
/**
 * Single listing features
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/features.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$building_size = ucpm_meta( 'building_size' );
$land_size = ucpm_meta( 'land_size' );
$floors = ucpm_meta( 'floors' );
$units = ucpm_meta( 'units' );
$type = ucpm_meta( 'type' );
?>

<div class="features sidebar-item">
    <h6><?php esc_html_e('Features:', 'ucpm'); ?></h6>

    <?php if ( ! empty( $building_size ) ) : ?>
        <span><b><?php esc_html_e('Building Size:', 'ucpm'); ?></b> <?php echo esc_html( number_format( $building_size ) ); ?></span>
    <?php endif; ?>

    <?php if ( ! empty( $land_size ) ) : ?>
        <span><b><?php esc_html_e('Land Size:', 'ucpm'); ?></b> <?php echo esc_html( number_format( $land_size ) ); ?></span>
    <?php endif; ?>

    <?php if ( ! empty( $units ) ) : ?>
        <span><b><?php esc_html_e('# Units:', 'ucpm'); ?></b> <?php echo esc_html( $units ); ?></span>
    <?php endif; ?>

    <?php if ( ! empty( $floors ) ) : ?>
        <span><b><?php esc_html_e('# Floors:', 'ucpm'); ?></b> <?php echo esc_html( $floors ); ?></span>
    <?php endif; ?>

    <?php if ( ! empty( $type ) ) : ?>
        <span><b><?php esc_html_e('Property Type:', 'ucpm'); ?></b> <?php echo esc_html( ucwords( str_replace( '-', ' ', $type ) ) ); ?></span>
    <?php endif; ?>
</div>
