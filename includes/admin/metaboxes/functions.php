<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Returns the listing statuses as set in the options.
 *
 * @return array		
 */
function ucpm_listing_statuses() {
	$option = get_option('ucpm_options');
	$statuses = isset($option['listing_status']) ? $option['listing_status'] : '';
	$array = array();
	if ($statuses) {
		foreach ($statuses as $status) {
			$status_slug = strtolower(str_replace(' ', '-', $status));
			$array[$status_slug] = $status;
		}
	}
	return $array;
}

/**
 * Returns the listing users.
 *
 * @return array
 */
function ucpm_listing_users() {
    $get_users = get_users();

    $users = array();

    if ($get_users) {
        foreach ($get_users as $user) {
            $users[$user->ID] = ! empty( $user->display_name ) ? $user->display_name : $user->user_nicename;
        }
    }

    return $users;
}

/**
 * Returns array of all pages.
 * For use in dropdowns.
 */
function ucpm_get_pages() {

	$args = array(
		'sort_order' => 'asc',
		'sort_column' => 'post_title',
		'hierarchical' => 1,
		'exclude' => '',
		'include' => '',
		'meta_key' => '',
		'meta_value' => '',
		'authors' => '',
		'child_of' => 0,
		'parent' => -1,
		'exclude_tree' => '',
		'number' => '',
		'offset' => 0,
		'post_type' => 'page',
		'post_status' => 'publish'
	);

	$pages = get_pages($args);
	$array = array();
	if ($pages) {
		foreach ($pages as $page) {
			$array[$page->ID] = $page->post_title;
		}
	}

	return $array;
}

/**
 * Output the map on the admin edit listing
 * @param  object $field_args Current field args
 * @param  object $field      Current field object
 */
function ucpm_admin_map($field_args, $field) {
	?>

	<div class="cmb-th"></div>
	<div class="cmb-td">
		<button id="ucpm-find" type="button" class="button button-small"><?php esc_html_e('Find', 'ucpm'); ?></button>
		<button id="ucpm-reset" type="button" class="button button-small"><?php esc_html_e('Reset', 'ucpm'); ?></button>
	</div>

	<div class="cmb-th"></div>
	<div class="cmb-td">
		<div class="ucpm-admin-map" style="height:220px"></div>
		<p class="cmb2-metabox-description map-desc"><?php esc_html_e('Modify the marker\'s position by dragging it.', 'ucpm'); ?></p>
	</div>

	<?php
}

/**
 * Output the archive button
 * @param  object $field_args Current field args
 * @param  object $field      Current field object
 */
function ucpm_admin_status_area($field_args, $field) {

	$post_id = $field->object_id;
	$inquiries = ucpm_meta('inquiries', $field->object_id);
	$count = !empty($inquiries) ? count($inquiries) : 0;
	$latest = is_array($inquiries) ? end($inquiries) : null;

	// listing inquiries section
	echo '<div class="listing-inquiries">';
	echo '<span class="dashicons dashicons-admin-comments"></span> <a target="_blank" href="' . esc_url(admin_url('edit.php?post_type=listing-inquiry&listings=' . $post_id)) . '"><span>' . sprintf(_n('%s Inquiry', '%s Inquiries', $count, 'ucpm'), $count) . '</a></span>';

	if ($latest) {
		echo '<p class="cmb2-metabox-description most-recent">' . esc_html__('Most Recent:', 'ucpm') . ' ' . sprintf(_x('%s ago', '%s = human-readable time difference', 'ucpm'), human_time_diff(get_the_date('U', $latest), current_time('timestamp'))) . '</p>';
	}
	echo '</div>';

	if ('archive' !== get_post_status($post_id)) {
		// archive button
		$button = ' <button id="archive-listing" type="button" class="button button-small">' . esc_html__('Archive This Listing', 'ucpm') . '</button>';

		echo $button;
	} else {
		echo '<div class="archived-text warning">' . esc_html__('This listing is archived.', 'ucpm') . '<br>' . esc_html__('It is no longer visible on the front end.', 'ucpm') . '<br>' . esc_html__('Hit the Publish button to un-archive it.', 'ucpm') . '</div>';
	}
	?>

	<script type="text/javascript" >

		jQuery(document).ready(function ($) {

			$("#archive-listing").click(function () {
				var btn = $(this);
				var data = {
					'action': 'ucpm_ajax_archive_listing',
					'post_id': <?php echo (int) $post_id; ?>,
					'nonce': '<?php echo wp_create_nonce('ucpm-archive-' . $post_id); ?>',
				};

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				$.post(ajaxurl, data, function (response) {

					var obj = $.parseJSON(response);

					$(btn).hide();
					$(btn).after('<div class="archived-text ' + obj.result + '">' + obj.string + '</div>');

					// change the select input to be archived (in case listing is updated after our actions)
					$('#post-status-display').text('<?php esc_html_e('Archived', 'ucpm') ?>');

				});

			});

		});
	</script>

	<?php
}

// Ajax Handler for archiving a listings
add_action('wp_ajax_ucpm_ajax_archive_listing', 'ucpm_ajax_archive_listing');

function ucpm_ajax_archive_listing() {
	// Get the Post ID
	$post_id = (int) $_REQUEST['post_id'];
	$response = false;

	// Proceed, again we are checking for permissions
	if (wp_verify_nonce($_REQUEST['nonce'], 'ucpm-archive-' . $post_id)) {

		$updated = wp_update_post(array(
			'ID' => $post_id,
			'post_status' => 'archive'
		));

		if (is_wp_error($updated)) {
			$response = false;
		} else {
			$response = true;
		}
	}

	if ($response == true) {
		$return = array(
			'string' => esc_html__('This listing is now archived. It is no longer visible on the front end.', 'ucpm'),
			'result' => 'warning'
		);
	} else {
		$return = array(
			'string' => esc_html__('There was an error archiving this listing', 'ucpm'),
			'result' => 'error'
		);
	}

	// Whatever the outcome, send the Response back
	echo json_encode($return);

	// Always exit when doing Ajax
	exit();
}