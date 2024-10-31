<?php
/**
 * Single listing documents
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/documents.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$documents = ucpm_meta( 'property_documents' );

if( empty( $documents ) )
    return;
?>

<h2 class="widget-title"><span><?php esc_html_e( 'Documents', 'ucpm' ); ?></span></h2>
<div class="ucpm-documents">
    <?php foreach ( $documents as $key => $document ) : ?>
        <a href="<?php echo esc_url( $document ); ?>" class="ucpm-documents-item" target="_blank"><?php echo basename ( get_attached_file( $key ) ) ?></a>
    <?php endforeach; ?>
</div>