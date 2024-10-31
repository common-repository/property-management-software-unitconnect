<?php
/**
 * Export/Import file.
 */

function ucpm_add_custom_import_button() {
	global $current_screen;
	// Not our post type, exit earlier
	// You can remove this if condition if you don't have any specific post type to restrict to. 
	if ('listing' != $current_screen->post_type) {
		return;
	}

	?>
		<script type="text/javascript">
			jQuery(document).ready( function($) {
				jQuery('.wp-header-end').before("<a class='add-new-h2' href='<?php echo admin_url('admin-ajax.php') . '?' . http_build_query($_GET); ?>&action=ucpm_pdf_export'>Export PDF</a>");
				jQuery('.wp-header-end').before("<a class='add-new-h2' href='<?php echo admin_url('admin-ajax.php') . '?' . http_build_query($_GET); ?>&action=ucpm_csv_export'>Export CSV</a>");
				jQuery('.wp-header-end').before('<a class="add-new-h2" id="ucpm-import-btn" href="#"><?php esc_html_e('Import CSV', 'ucpm'); ?></a>');
			});
		</script>
		<form id="ucpm-import">
			<input type="file" name="csv-file" accept=".csv">
		</form>
	<?php
}
add_action('admin_head-edit.php', 'ucpm_add_custom_import_button');


function ucpm_get_all_fields_names() {
	$field_ids1 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_description' )->prop( 'fields' ), 'name' );
	$field_ids2 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_features' )->prop( 'fields' ), 'name' );
	$field_ids3 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_images' )->prop( 'fields' ), 'name' );
	$field_ids4 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_sale' )->prop( 'fields' ), 'name' );
	$field_ids5 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_lease' )->prop( 'fields' ), 'name' );
	$field_ids6 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_documents' )->prop( 'fields' ), 'name' );
	$field_ids7 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_status' )->prop( 'fields' ), 'name' );
	$field_ids8 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_address' )->prop( 'fields' ), 'name' );
	
	$keys = array_merge($field_ids1, $field_ids2, $field_ids3, $field_ids4, $field_ids5, $field_ids6, $field_ids7, $field_ids8);
	$list = array(
		'item_id' => esc_html__('Item ID', 'ucpm'),
		'title' => esc_html__('Title', 'ucpm'),
	);

	unset($keys['_ucpm_listing_property_documents']);
	unset($keys['']);
	
	foreach( $keys as $key ) {
		$list[] = str_replace('_ucpm_listing_', '', $key);
	}


	return $list;
}

function ucpm_get_all_fields_keys() {
	$field_ids1 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_description' )->prop( 'fields' ), 'id' );
	$field_ids2 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_features' )->prop( 'fields' ), 'id' );
	$field_ids3 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_images' )->prop( 'fields' ), 'id' );
	$field_ids4 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_sale' )->prop( 'fields' ), 'id' );
	$field_ids5 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_lease' )->prop( 'fields' ), 'id' );
	$field_ids6 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_documents' )->prop( 'fields' ), 'id' );
	$field_ids7 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_status' )->prop( 'fields' ), 'id' );
	$field_ids8 = wp_list_pluck( cmb2_get_metabox( '_ucpm_listing_address' )->prop( 'fields' ), 'id' );
	

	$keys = array_merge($field_ids1, $field_ids2, $field_ids3, $field_ids4, $field_ids5, $field_ids6, $field_ids7, $field_ids8);
	$list = array();


	foreach( $keys as $key ) {
		if ( ! empty( $key ) && $key != '' ) {
			$list[] = str_replace('_ucpm_listing_', '', $key);
		}
	}


	return $list;
}

function ucpm_get_posts_array() {
	$args = array(
		'post_type' => 'listing',
		'posts_per_page' => -1
	);

	if ( isset( $_GET['listing-type'] ) && ! empty( $_GET['listing-type'] ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'listing-type',
				'field'    => 'slug',
				'terms'    => array( sanitize_text_field( $_GET['listing-type'] ) ),
			),
		);
	}

	if ( isset( $_GET['purpose'] ) && ! empty( $_GET['purpose'] ) ) {
		$args['meta_query'] = array(
			array(
				'key'     => '_ucpm_listing_show_' . sanitize_text_field( $_GET['purpose'] ),
				'value'   => 'on',
			),
		);
	}

	if ( isset( $_GET['m'] ) && ! empty( $_GET['m'] ) && is_numeric( $_GET['m'] ) ) {

		$args['date_query'] = array(
			array(
				'year'  => sanitize_text_field( intval(substr($_GET['m'], 0, 4)) ),
				'month' => sanitize_text_field( intval(substr($_GET['m'], 4, 2)) ),
			),
		);
	}

	$the_query = new WP_Query( $args );
	$switchers = array('display_property', 'show_sale', 'show_lease', 'lease_items');

	$keys = ucpm_get_all_fields_keys();
	$list = array();

	// The Loop
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();

			$list_item = array(
				'item_id' => get_the_ID(),
				'title' => get_the_title(),
			);

			foreach( $keys as $key ) {
				if ( in_array( $key, $switchers ) ) {
					$value = ucpm_meta( $key );
					if ( $key != 'lease_items') {
						$list_item[ $key ] = $value == 'on' ? 'yes' : 'no';
					} else {
						foreach ($value as $el_key => $el) {
							if ( $value[ $el_key ]['_ucpm_listing_lease_available'] == 'on') {
								$value[ $el_key ]['_ucpm_listing_lease_available'] = 'yes';
							} else {
								$value[ $el_key ]['_ucpm_listing_lease_available'] = 'no';
							}
						}

						$list_item[ $key ] = $value;
					}
				} else {
					if ( $key == 'status' ) {
						$status = ucpm_meta( $key );
						if ( ! empty( $status ) ) {
							$list_item[ $key ] = $status;
						} else {
							$list_item[ $key ] = '';
						}
					} else {
						$list_item[ $key ] = ucpm_meta( $key );
					}
				}
			}

			$list[] = $list_item; 
			
		}
		wp_reset_postdata();
	}
	
	return $list;
}

function ucpm_csv_export() {
	$items = ucpm_get_posts_array();

	// prepare to export
	foreach ($items as $key => $item) {
		unset($items[$key]['property_documents']);
		unset($items[$key]['']);

		$items[$key]['image_gallery'] = implode(', ', $items[$key]['image_gallery']);

		foreach ($item['lease_items'] as $key2 => $row) {
			$items[$key]['lease_items'][$key2] = implode('|', $row);
		}

		$items[$key]['lease_items'] = json_encode( $items[$key]['lease_items'] );
	}

	foreach ($items as $key => $item) {
		$items[$key] = array_map( 'strip_tags', $items[$key] );
	}

	$filename = date('mdY') . ' - export.csv';
	$output = fopen("php://output",'w') or die("Can't open php://output");
	header("Content-Type:application/csv"); 
	header("Content-Disposition:attachment;filename=" . $filename); 

	fputcsv($output, ucpm_get_all_fields_names());

	foreach($items as $item) {
		fputcsv($output, $item);
	}

	fclose($output) or die("Can't close php://output");

	die();
}
add_action( 'wp_ajax_ucpm_csv_export', 'ucpm_csv_export' );

require dirname(__DIR__) . '/vendor/autoload.php';
use Dompdf\Dompdf;

function ucpm_pdf_export() {
	$items = ucpm_get_posts_array();
	$html = '
	<style>	
		table {
			table-layout: fixed;
			width: 100%;
		}
		td {
			vertical-align: top;
		}
		tr {
			border-collapse:separate; 
			border-spacing: 0 20px;
		}
		hr {
			margin: 20px 0;
		}
		.page_break { page-break-after: always; }
	</style>
	';
	
	if ( ! empty( $items ) ) {
		$total_items = count( $items );
		foreach($items as $key => $item) {
			$last_item = ( $key + 1 ) == $total_items;
			$html = $html . ucpm_pdf_export_item_html( $item, $last_item );
		}
	}

	$dompdf = new Dompdf();
	$dompdf->loadHtml($html);
	$dompdf->set_option('isRemoteEnabled', TRUE);
	$dompdf->setPaper('A4', 'landscape');

	// Render the HTML as PDF
	$dompdf->render();

	// Output the generated PDF to Browser
	$dompdf->stream();

	die();
}
add_action( 'wp_ajax_ucpm_pdf_export', 'ucpm_pdf_export' );

function ucpm_pdf_export_item_html( $item, $last_item ) {
	$images = $item['image_gallery'];
	$img = '';
	if ( ! empty( $images ) ) {
		$image  = array_keys( $images );
		$img    = wp_get_attachment_image_src( $image[0], 'full' );
	}
	ob_start();
	?>
	<h2><?php echo esc_html( $item['title'] ); ?></h2>

	<table>
		<tr>
			<td>
				<?php if ( ! empty( $img[0] ) ) : ?>
					<img src="<?php echo esc_url( $img[0] ); ?>" width="500" />
				<?php endif ?>
			</td>
			<td><?php echo wp_kses_post( $item['content'] ); ?></td>
		</tr>

		<tr>
			<td>
				<?php echo wp_kses_post( $item['displayed_address'] ); ?><br/>
				<?php echo esc_html( $item['city'] . ', ' . $item['state'] . ' ' . $item['zip'] ); ?>
			</td>

			<td>
				<b><?php esc_html_e('Building Size:', 'ucpm'); ?></b> <?php echo esc_html( number_format( $item['$building_size'] ) ); ?>
				<b><?php esc_html_e('Land Size:', 'ucpm'); ?></b> <?php echo esc_html( number_format( $item['land_size'] ) ); ?>
				<b><?php esc_html_e('# Units:', 'ucpm'); ?></b> <?php echo esc_html( $item['units'] ); ?>
				<b><?php esc_html_e('#Build:', 'ucpm'); ?></b> <?php echo esc_html( $item['floors'] ); ?>
				<b><?php esc_html_e('Property Type:', 'ucpm'); ?></b> <?php echo esc_html( $item['type'] ); ?>
			</td>
		</tr>

		<?php if ( $item['show_sale'] === 'on' ) : ?>
			<tr>
				<td>
					<h3><?php esc_html_e('Sale Details', 'ucpm'); ?></h3>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php esc_html_e('Price: $', 'ucpm'); ?></b> <?php echo esc_html(number_format($item['asking_price'])); ?><br/>
					<b><?php esc_html_e('NOI: $', 'ucpm'); ?></b> <?php echo esc_html(number_format($item['noi'])); ?><br/>
					<b><?php esc_html_e('CAP Rate:', 'ucpm'); ?></b> <?php echo esc_html($item['cap_rate']); ?>%<br/>
					<b><?php esc_html_e('Status:', 'ucpm'); ?></b> <?php echo esc_html(str_replace( '-', ' ', $item['status'] )); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php echo wp_kses_post( $item['sale_description'] ); ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( $item['show_lease'] === 'on' ) : ?>
			<tr>
				<td>
					<h3><?php esc_html_e('Lease Details', 'ucpm'); ?></h3>
				</td>
			</tr>

			<?php foreach ( $item['lease_items'] as $el ) : ?>
				<tr>
					<td>
						<b><?php esc_html_e('Space:', 'ucpm'); ?></b> <?php echo esc_html( $el['_ucpm_listing_lease_space'] ); ?><br/>
						<b><?php esc_html_e('Size:', 'ucpm'); ?></b> <?php echo esc_html(number_format( $el['_ucpm_listing_lease_size'] )); ?> <?php esc_html_e('SQFT', 'ucpm'); ?><br/>
						<b><?php esc_html_e('Description:', 'ucpm'); ?></b> <?php echo esc_html( $el['_ucpm_listing_lease_desc'] ); ?><br/>
						<b><?php esc_html_e('Asking:', 'ucpm'); ?></b> <?php echo esc_html(number_format( $el['_ucpm_listing_lease_asking'] )); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td colspan="2">
					<?php echo wp_kses_post( $item['lease_description'] ); ?>
				</td>
			</tr>
		<?php endif; ?>
	</table>

	<?php if ( ! $last_item ): ?>
		<hr class="page_break">
	<?php endif ?>

	<?php
	return ob_get_clean();
}

function ucpm_import_csv() {
	$tmpName = $_FILES['file']['tmp_name'];
	$prefix = '_ucpm_listing_';

	if ( ( $handle = fopen( $tmpName, 'r') ) !== FALSE ) {

		fgetcsv($handle); // skip titles row

		while(($data = fgetcsv($handle, 10000, ',')) !== FALSE ) {
			$post_id = trim($data[0]);

			if ( empty( $data[0] ) || $post_id == '' ) {
				$post_data = array(
					'post_title'    => sanitize_text_field( $data[1] ),
					'post_type'  	=> 'listing',
					'post_status'   => 'publish'
				);

				$post_id = wp_insert_post( $post_data );
			} else {
				$my_post = array();
				$my_post['ID'] = $post_id;
				$my_post['post_title'] = sanitize_text_field( $data[1] );

				wp_update_post( wp_slash($my_post) );
			}

			if ( is_numeric( $post_id ) ) {

				$display_property = trim(strtolower($data[2]));
				$display_property = $display_property == 'yes' ? 'on' : '';
				update_post_meta( $post_id, $prefix . 'display_property', $display_property);

				update_post_meta( $post_id, $prefix . 'tagline', trim($data[3]));
				update_post_meta( $post_id, 'content', trim($data[4]));
				update_post_meta( $post_id, $prefix . 'building_size', trim($data[5]));
				update_post_meta( $post_id, $prefix . 'land_size', trim($data[6]));

				// Listing type
				if ( ! empty( $data[7] ) ) {
					$term = get_term_by( 'name', trim($data[7]), 'listing-type');
					if ( ! empty( $term ) ) {
						wp_set_post_terms( $post_id, $term->term_id, 'listing-type' );
					} else {
						$term = wp_insert_term( trim($data[7]), 'listing-type' );
						wp_set_post_terms( $post_id, $term->term_id, 'listing-type' );
					}
				} else {
					wp_delete_object_term_relationships($post_id, 'listing-type');
				}

				update_post_meta( $post_id, $prefix . 'floors', trim($data[8]));
				update_post_meta( $post_id, $prefix . 'units', trim($data[9]));

				$show_sale = trim(strtolower($data[11]));
				$show_sale = $show_sale == 'yes' ? 'on' : '';
				update_post_meta( $post_id, $prefix . 'show_sale', $show_sale);

				update_post_meta( $post_id, $prefix . 'asking_price', trim($data[12]));
				update_post_meta( $post_id, $prefix . 'noi', trim($data[13]));
				update_post_meta( $post_id, $prefix . 'cap_rate', trim($data[14]));

				// Listing status
				if ( ! empty( $data[15] ) ) {
					$term = get_term_by( 'name', trim($data[15]), 'listing-status');
					if ( ! empty( $term ) ) {
						wp_set_post_terms( $post_id, $term->term_id, 'listing-status');
					} else {
						$term = wp_insert_term( trim($data[15]), 'listing-status' );
						wp_set_post_terms( $post_id, $term->term_id, 'listing-status');
					}
				} else {
					wp_delete_object_term_relationships($post_id, 'listing-status');
				}

				update_post_meta( $post_id, $prefix . 'sale_description', trim($data[16]));

				$show_lease = trim(strtolower($data[17]));
				$show_lease = $show_lease == 'yes' ? 'on' : '';
				update_post_meta( $post_id, $prefix . 'show_lease', $show_lease);

				$lease_items = ! empty( $data[18] ) ? json_decode( trim($data[18]), true ) : '';
				if ( ! empty( $lease_items ) && is_array( $lease_items ) ) {
					$list = array();
					foreach ($lease_items as $item) {
						$el = explode('|', $item);

						if ( isset( $el[4] ) ) {
							$el[4] = $el[4] == 'yes' ? 'on' : '';

							$list[] = array(
								'_ucpm_listing_lease_space' => $el[0],
								'_ucpm_listing_lease_desc' => $el[1],
								'_ucpm_listing_lease_size' => $el[2],
								'_ucpm_listing_lease_asking' => $el[3],
								'_ucpm_listing_lease_available' => $el[4]
							);
						}
					}

					if ( ! empty( $list ) ) {
						update_post_meta( $post_id, $prefix . 'lease_items', $list);
					}
				}

				update_post_meta( $post_id, $prefix . 'lease_description', trim($data[19]));
				update_post_meta( $post_id, $prefix . 'displayed_address', trim($data[20]));
				update_post_meta( $post_id, $prefix . 'city', trim($data[21]));
				update_post_meta( $post_id, $prefix . 'state', trim($data[22]));
				update_post_meta( $post_id, $prefix . 'county', trim($data[23]));
				update_post_meta( $post_id, $prefix . 'zip', trim($data[24]));
				update_post_meta( $post_id, $prefix . 'lat', trim($data[25]));
				update_post_meta( $post_id, $prefix . 'lng', trim($data[26]));
			}

			echo esc_html($post_id) . ' - ';
	    }
    }
}
add_action( 'wp_ajax_ucpm_import_csv', 'ucpm_import_csv' );
