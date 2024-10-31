<?php
/**
 * Ordering
 *
 * This template can be overridden by copying it to yourtheme/listings/loop/orderby.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$contextual_query = ucpm_get_contextual_query();
if ( 1 === $contextual_query->found_posts ) {
	return;
}

// get unique cities
$cities = array();

foreach ( $contextual_query->posts as $item ) {
    $city = get_post_meta( $item->ID, '_ucpm_listing_city', true );
    $cities[] = $city;
}

$cities = array_unique( $cities );

// orderny
$orderby = isset( $_GET['ucpm-orderby'] ) ? sanitize_text_field( $_GET['ucpm-orderby'] ) : 'date';
$orderby_options = apply_filters( 'ucpm_listings_orderby', array(
	'date'			=> esc_html__( 'Newest', 'ucpm' ),
	'date-old'		=> esc_html__( 'Oldest', 'ucpm' ),
) );
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

?>
<form class="ucpm-ordering" method="get">

    <div class="ucpm-search-wrap">
        <svg version="1.1" class="ucpm-search-btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
        viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
            <g>
                <g>
                    <path d="M225.474,0C101.151,0,0,101.151,0,225.474c0,124.33,101.151,225.474,225.474,225.474
                    c124.33,0,225.474-101.144,225.474-225.474C450.948,101.151,349.804,0,225.474,0z M225.474,409.323
                    c-101.373,0-183.848-82.475-183.848-183.848S124.101,41.626,225.474,41.626s183.848,82.475,183.848,183.848
                    S326.847,409.323,225.474,409.323z"/>
                </g>
            </g>
            <g>
                <g>
                    <path d="M505.902,476.472L386.574,357.144c-8.131-8.131-21.299-8.131-29.43,0c-8.131,8.124-8.131,21.306,0,29.43l119.328,119.328
                    c4.065,4.065,9.387,6.098,14.715,6.098c5.321,0,10.649-2.033,14.715-6.098C514.033,497.778,514.033,484.596,505.902,476.472z"/>
                </g>
            </g>
        </svg>

        <input type="text" name="s" placeholder="Search by text" class="ucpm-search-field">
    </div>

    <div class="ucpm-city-wrap">
        <svg class="ucpm-city-btn" enable-background="new 0 0 512.016 512.016" height="512" viewBox="0 0 512.016 512.016" width="512" xmlns="http://www.w3.org/2000/svg"><g><path d="m232.714 510.417 60-30c5.082-2.541 8.292-7.735 8.292-13.417v-92.438l120.597-146.052c2.229-2.7 3.502-6.239 3.428-9.861v-59.696c0-8.284-6.716-15-15-15h-308.049c-8.284 0-15 6.716-15 15v60c0 3.471 1.295 6.998 3.518 9.643l120.507 145.954v122.45c-.001 11.131 11.739 18.4 21.707 13.417zm162.317-336.463v30c-4.249 0-108.943.002-278.049-.004v-29.996zm-261.204 60c146.726 0 243.545.005 244.373.005l-103.76 125.66c-2.22 2.688-3.434 6.065-3.434 9.551v88.56l-30 15v-103.57c0-3.486-1.214-6.862-3.433-9.55z"/><path d="m331.376 98.952v-83.952c0-8.284-6.716-15-15-15s-15 6.716-15 15v83.952c0 8.284 6.716 15 15 15s15-6.716 15-15z"/><path d="m211.376 98.952v-83.952c0-8.284-6.716-15-15-15s-15 6.716-15 15v83.952c0 8.284 6.716 15 15 15s15-6.716 15-15z"/><path d="m256.376 0c-8.284 0-15 6.716-15 15v23.952c0 8.284 6.716 15 15 15s15-6.716 15-15v-23.952c0-8.284-6.716-15-15-15z"/><circle cx="256.376" cy="98.952" r="15"/></g></svg>
        <select class="ucpm-city-select" name="city">
            <option selected value=""><?php esc_html_e('Choose City', 'ucpm'); ?></option>
            <?php foreach ( $cities as $city ) : ?>
                <option value="<?php echo esc_attr( $city ); ?>"><?php echo esc_attr( $city ); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

	<div class="ucpm-select-wrap">
        <svg version="1.1" class="ucpm-select-btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
             viewBox="0 0 301.219 301.219" style="enable-background:new 0 0 301.219 301.219;" xml:space="preserve">
            <g>
                <path d="M159.365,23.736v-10c0-5.523-4.477-10-10-10H10c-5.523,0-10,4.477-10,10v10c0,5.523,4.477,10,10,10h139.365
                    C154.888,33.736,159.365,29.259,159.365,23.736z"/>
                <path d="M130.586,66.736H10c-5.523,0-10,4.477-10,10v10c0,5.523,4.477,10,10,10h120.586c5.523,0,10-4.477,10-10v-10
                    C140.586,71.213,136.109,66.736,130.586,66.736z"/>
                <path d="M111.805,129.736H10c-5.523,0-10,4.477-10,10v10c0,5.523,4.477,10,10,10h101.805c5.523,0,10-4.477,10-10v-10
                    C121.805,134.213,117.328,129.736,111.805,129.736z"/>
                <path d="M93.025,199.736H10c-5.523,0-10,4.477-10,10v10c0,5.523,4.477,10,10,10h83.025c5.522,0,10-4.477,10-10v-10
                    C103.025,204.213,98.548,199.736,93.025,199.736z"/>
                <path d="M74.244,262.736H10c-5.523,0-10,4.477-10,10v10c0,5.523,4.477,10,10,10h64.244c5.522,0,10-4.477,10-10v-10
                    C84.244,267.213,79.767,262.736,74.244,262.736z"/>
                <path d="M298.29,216.877l-7.071-7.071c-1.875-1.875-4.419-2.929-7.071-2.929c-2.652,0-5.196,1.054-7.072,2.929l-34.393,34.393
                    V18.736c0-5.523-4.477-10-10-10h-10c-5.523,0-10,4.477-10,10v225.462l-34.393-34.393c-1.876-1.875-4.419-2.929-7.071-2.929
                    c-2.652,0-5.196,1.054-7.071,2.929l-7.072,7.071c-3.904,3.905-3.904,10.237,0,14.142l63.536,63.536
                    c1.953,1.953,4.512,2.929,7.071,2.929c2.559,0,5.119-0.976,7.071-2.929l63.536-63.536
                    C302.195,227.113,302.195,220.781,298.29,216.877z"/>
            </g>
        </svg>
		<select name="ucpm-orderby" class="properties-orderby">
			<?php foreach ( $orderby_options as $id => $name ) : ?>
				<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<?php
	// Keep query string vars intact
	foreach ( $_GET as $key => $val ) {

		if ( 'ucpm-orderby' === $key || 'submit' === $key ) {
			continue;
		}
		if ( is_array( $val ) ) {
			foreach( $val as $innerVal ) {
				echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
			}
		} else {
			echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
		}

	}
	?>
	<input type="hidden" name="paged" value="<?php echo esc_attr( $paged ); ?>" />
</form>