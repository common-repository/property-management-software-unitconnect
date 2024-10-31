<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

add_action('cmb2_admin_init', 'ucpm_options_page');

function ucpm_options_page() {

	$property_label = esc_html__('Property', 'ucpm');
	$properties_label = esc_html__('Properties', 'ucpm');
	// the options key fields will be saved under
	$opt_key = 'ucpm_options';

	// the show_on parameter for configuring the CMB2 box, this is critical!
	$show_on = array('key' => 'ucpm-options-page', 'value' => array($opt_key));

	// an array to hold our boxes
	$boxes = array();

	// an array to hold some tabs
	$tabs = array();

	/*
	 * Tabs - an array of configuration arrays.
	 */
	$tabs[] = array(
		'id' => 'general',
		'title' => esc_html__('General', 'ucpm'),
		'desc' => '',
		'boxes' => array(
			'ucpm_display_settings',
			'google_maps',
			'search',
		),
	);

	$tabs[] = array(
		'id' => 'properties',
		'title' => sprintf(__('%s', 'ucpm'), $properties_label),
		'desc' => '',
		'boxes' => array(
			'property_setup',
			'property_attributes',
		),
	);

	$tabs[] = array(
		'id' => 'contact',
		'title' => 'Contact Form',
		'desc' => '',
		'boxes' => array(
			'contact_form',
			'contact_form_email',
			'contact_form_messages',
		),
	);

	$tabs[] = array(
		'id' => 'advanced',
		'title' => 'Advanced',
		'desc' => '',
		'boxes' => array(
			'template_html',
			'uninstall',
		),
	);
	
	// display-setttings
	$cmb = new_cmb2_box(array(
		'id' => 'ucpm_display_settings',
		'title' => esc_html__('Display Settings', 'ucpm'),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => esc_html__('Default Display Mode', 'ucpm'),
		'desc' => '',
		'id' => 'ucpm_default_display_mode',
		'type' => 'select',
		'default' => 'grid-view',
		'options' => array(
			'grid-view' => esc_html__('Grid Mode', 'ucpm'),
			'list-view' => esc_html__('List Mode', 'ucpm'),
		),
	));
	$cmb->add_field(array(
		'name' => esc_html__('Show plugin author copyright', 'ucpm'),
		'desc' => '',
		'id' => 'ucpm_default_show_copy',
		'type' => 'select',
		'default' => 'grid-view',
		'options' => array(
			'no' => esc_html__('No', 'ucpm'),
			'yes' => esc_html__('Yes', 'ucpm'),
		),
	));
	$cmb->add_field(array(
		'name' => esc_html__('Grid Columns', 'ucpm'),
		'desc' => esc_html__('The number of columns to display on the archive page, when viewing properties in grid mode.', 'ucpm'),
		'id' => 'ucpm_grid_columns',
		'type' => 'select',
		'default' => '3',
		'options' => array(
			'2' => esc_html__('2 columns', 'ucpm'),
			'3' => esc_html__('3 columns', 'ucpm'),
			'4' => esc_html__('4 columns', 'ucpm'),
		),
	));
	$cmb->add_field( array(
		'name' => esc_html__('Properties per Page', 'ucpm'),
		'desc' => esc_html__('The max number of properties/agencies to show in archive page.', 'ucpm') . '<br>' . esc_html__('Could show less than this if not enough properties/agencies are found.', 'ucpm'),
		'id'   => 'archive_property_number',
		'type' => 'text',
		'default' => '10',
		'attributes' => array(
			'type' => 'number',
			'min'	=> 1
		),
	) );
	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// maps
	$cmb = new_cmb2_box(array(
		'id' => 'google_maps',
		'title' => esc_html__('Google Maps', 'ucpm'),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => esc_html__('API Key', 'ucpm'),
		'before_row' => sprintf(__('A Google Maps API Key is required to be able to show the maps. It\'s free and you can get yours %s.', 'ucpm'), '<strong><a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">here</a></strong>').'<br />'. esc_html__('You can add a configurable map to pinpoint your properties on the front end using [ucpm_map] shortcode.', 'ucpm'),
		'id' => 'maps_api_key',
		'type' => 'text',
	));
	$cmb->add_field(array(
		'name' => esc_html__('Map Zoom', 'ucpm'),
		'desc' => '',
		'id' => 'map_zoom',
		'type' => 'text',
		'default' => '12',
		'attributes' => array(
			'type' => 'number',
		),
	));
	$cmb->add_field(array(
		'name' => esc_html__('Map Height', 'ucpm'),
		'desc' => '',
		'id' => 'map_height',
		'type' => 'text',
		'default' => '200',
		'attributes' => array(
			'type' => 'number',
		),
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// maps
	$cmb = new_cmb2_box(array(
		'id' => 'search',
		'title' => esc_html__('Search', 'ucpm'),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => esc_html__('Distance Measurement', 'ucpm'),
		'before_row' => esc_html__('These settings relate to the [ucpm_search] shortcode.', 'ucpm'),
		'desc' => esc_html__('Choose miles or kilometers for the radius.', 'ucpm'),
		'id' => 'distance_measurement',
		'type' => 'select',
		'options' => array(
			'miles' => esc_html__('Miles', 'ucpm'),
			'kilometers' => esc_html__('Kilometers', 'ucpm'),
		),
	));

	$cmb->add_field(array(
		'name' => esc_html__('Radius', 'ucpm'),
		'desc' => esc_html__('Show properties that are within this distance (mi or km as selected above).', 'ucpm'),
		'id' => 'search_radius',
		'type' => 'text',
		'default' => '20',
		'atributes' => array(
			'type' => 'number',
			'placeholder' => '20',
		),
	));
	$cmb->add_field(array(
		'name' => esc_html__('Country', 'ucpm'),
		'desc' => sprintf(__('Country name or two letter %s country code.', 'ucpm'), '<a target="_blank" href="https://en.wikipedia.org/wiki/ISO_3166-1">ISO 3166-1</a>'),
		'id' => 'search_country',
		'type' => 'text',
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;


	/* ==================== Properties Options ==================== */

	// properties setup
	$cmb = new_cmb2_box(array(
		'id' => 'property_setup',
		'title' => esc_html__('Properties Setup', 'ucpm'),
		'show_on' => $show_on,
	));

	$cmb->add_field(array(
		'name' => esc_html__('Properties Page', 'ucpm'),
		'desc' => esc_html__('The main page to display your properties.', 'ucpm'),
		'id' => 'archives_page',
		'type' => 'select',
		'options_cb' => 'ucpm_get_pages',
	));

    $cmb->add_field(array(
        'name' => esc_html__('For Sale Page', 'ucpm'),
        'desc' => esc_html__('The main page to show properties available for Sale.', 'ucpm'),
        'id' => 'archives_sale_page',
        'type' => 'select',
        'options_cb' => 'ucpm_get_pages',
    ));

    $cmb->add_field(array(
        'name' => esc_html__('For Lease Page', 'ucpm'),
        'desc' => esc_html__('The main page to show properties available for Lease.', 'ucpm'),
        'id' => 'archives_lease_page',
        'type' => 'select',
        'options_cb' => 'ucpm_get_pages',
    ));

    $cmb->add_field(array(
        'name' => esc_html__('Single Property URL', 'ucpm'),
        'desc' => esc_html__('The single property URL (or slug).', 'ucpm'),
        'id' => 'single_url',
        'type' => 'text',
        'default' => 'property',
    ));

	$cmb->add_field(array(
		'name' => esc_html__('Force properties Page Title', 'ucpm'),
		'desc' => esc_html__('If your page title is not displaying correctly, you can force the page title here.', 'ucpm') . '<br>' . esc_html__('(Some themes may be using incorrect template tags to display the archive title. This forces the title within the page)', 'ucpm'),
		'id' => 'archives_page_title',
		'type' => 'select',
		'default' => 'no',
		'options' => array(
			'no' => esc_html__('No', 'ucpm'),
			'yes' => esc_html__('Yes', 'ucpm'),
		),
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	/* ==================== Contact Form ==================== */

	// contact form
	$cmb = new_cmb2_box(array(
		'id' => 'contact_form',
		'title' => esc_html__('Contact Form Settings', 'ucpm'),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => esc_html__('Email From', 'ucpm'),
		'desc' => esc_html__('The "from" address for all inquiry emails that are sent to email addresses.', 'ucpm'),
		'id' => 'email_from',
		'type' => 'text_email',
		'default' => get_bloginfo('admin_email'),
	));
	$cmb->add_field(array(
		'name' => esc_html__('Name', 'ucpm'),
		'desc' => esc_html__('The "from" name for all inquiry emails that are sent to email addresses.', 'ucpm'),
		'id' => 'email_from_name',
		'type' => 'text',
		'default' => get_bloginfo('name'),
	));
	$cmb->add_field(array(
		'name' => esc_html__('CC', 'ucpm'),
		'desc' => esc_html__('Extra email addresses that are CC\'d on every inquiry (comma separated).', 'ucpm'),
		'id' => 'contact_form_cc',
		'type' => 'text',
		'attributes' => array(
			'placeholder' => 'somebody@somewhere.com',
		),
	));
	$cmb->add_field(array(
		'name' => esc_html__('BCC', 'ucpm'),
		'desc' => esc_html__('Extra email addresses that are BCC\'d on every inquiry (comma separated).', 'ucpm'),
		'id' => 'contact_form_bcc',
		'type' => 'text',
		'attributes' => array(
			'placeholder' => 'somebody@somewhere.com',
		),
	));
    $cmb->add_field(array(
        'name' => esc_html__('Google reCAPTCHA Key', 'ucpm'),
        'id' => 'google_recaptcha_ley',
        'type' => 'text',
    ));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// contact form email
	$cmb = new_cmb2_box(array(
		'id' => 'contact_form_email',
		'title' => esc_html__('Contact Form Email', 'ucpm'),
		'show_on' => $show_on,
		'desc' => '',
	));

	$cmb->add_field(array(
		'name' => esc_html__('Email Type', 'ucpm'),
		'desc' => '',
		'id' => 'contact_form_email_type',
		'type' => 'select',
		'options' => array(
			'html_email' => esc_html__('HTML', 'ucpm'),
			'text_email' => esc_html__('Plain Text', 'ucpm'),
		),
		'default' => 'html_email',
	));
	$cmb->add_field(array(
		'name' => esc_html__('Email Subject', 'ucpm'),
		'desc' => '',
		'id' => 'contact_form_subject',
		'type' => 'text',
		'default' => esc_html__('New inquiry on property #{property_id}', 'ucpm'),
	));
	$cmb->add_field(array(
		'name' => esc_html__('Email Message', 'ucpm'),
		'desc' => __('Content of the email that is sent to the email addresses above. ' .
				'Available tags are:<br>' .
				'{property_title}<br>' .
				'{property_id}<br>' .
				'{inquiry_name}<br>' .
				'{inquiry_email}<br>' .
				'{inquiry_phone}<br>' .
				'{inquiry_message}<br>'
				, 'ucpm'),
		'default' => esc_html__('Hi,', 'ucpm') . "\r\n" .
		__('There has been a new inquiry on <strong>{property_title}</strong>', 'ucpm') . "\r\n" .
		'<hr>' . "\r\n" .
		__('Name: {inquiry_name}', 'ucpm') . "\r\n" .
		__('Email: {inquiry_email}', 'ucpm') . "\r\n" .
		__('Phone: {inquiry_phone}', 'ucpm') . "\r\n" .
		__('Message: {inquiry_message}', 'ucpm') . "\r\n" .
		'<hr>',
		'id' => 'contact_form_message',
		'type' => 'textarea',
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// contact form messages
	$cmb = new_cmb2_box(array(
		'id' => 'contact_form_messages',
		'title' => esc_html__('Contact Form Messages', 'ucpm'),
		'show_on' => $show_on,
		'desc' => '',
	));

	$cmb->add_field(array(
		'name' => esc_html__('Consent Field Label', 'ucpm'),
		'desc' => esc_html__('Add Consent Field.', 'ucpm'),
		'id' => 'contact_form_consent_label',
		'type' => 'text',
		'default' => ''
	));

	$cmb->add_field(array(
		'name' => esc_html__('Consent Description', 'ucpm'),
		'desc' => esc_html__('Add Consent Description.', 'ucpm'),
		'id' => 'contact_form_consent_desc',
		'type' => 'wysiwyg',
		'options' => array( 'teeny' => true, 'quicktags' => false, 'media_buttons' => false, 'textarea_rows' => 5 ),
		'default' => ''
	));

	$cmb->add_field(array(
		'name' => esc_html__('Success Message', 'ucpm'),
		'desc' => esc_html__('The message that is displayed to users upon successfully sending a message.', 'ucpm'),
		'id' => 'contact_form_success',
		'type' => 'text',
		'default' => esc_html__('Thank you, we will be in touch with you soon.', 'ucpm'),
	));
	$cmb->add_field(array(
		'name' => esc_html__('Error Message', 'ucpm'),
		'desc' => esc_html__('The message that is displayed if there is an error sending the message.', 'ucpm'),
		'id' => 'contact_form_error',
		'type' => 'text',
		'default' => esc_html__('There was an error. Please try again.', 'ucpm'),
	));
	$cmb->add_field(array(
		'name' => esc_html__('Include Error Code', 'ucpm'),
		'desc' => esc_html__('Should the error code be shown with the error. Can be helpful for troubleshooting.', 'ucpm'),
		'id' => 'contact_form_include_error',
		'type' => 'select',
		'options' => array(
			'yes' => esc_html__('Yes', 'ucpm'),
			'no' => esc_html__('No', 'ucpm'),
		),
		'default' => 'yes',
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	
	/* ==================== Advanced Options ==================== */

	// template html
	$cmb = new_cmb2_box(array(
		'id' => 'template_html',
		'title' => esc_html__('Template HTML', 'ucpm'),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => esc_html__('Theme Compatibility', 'ucpm'),
		'desc' => esc_html__('If enabled, add [ucpm_archive_properties] and [ucpm_archive_agencies] shortcode on there respective pages and remove it if disabled.', 'ucpm'),
		'id' => 'ucpm_theme_compatibility',
		'type' => 'select',
		'default' => 'enable',
		'options' => array(
			'enable' => esc_html__('Enabled', 'ucpm'),
			'disable' => esc_html__('Disabled', 'ucpm'),
		),
	));
	$cmb->add_field(array(
		'name' => esc_html__('Opening HTML Tag(s)', 'ucpm'),
		'desc' => esc_html__('Used for theme compatability, this option will override the opening HTML for all properties pages.', 'ucpm') . '<br>' . esc_html__('This can help you to match the HTML with your current theme.', 'ucpm'),
		'id' => 'opening_html',
		'type' => 'textarea',
		'attributes' => array(
			'placeholder' => '<div class=&quot;container&quot;><div class=&quot;main-content&quot;>',
			'rows' => 2,
		),
		'before_row' => '<p class="cmb2-metabox-description"></p>',
	));
	$cmb->add_field(array(
		'name' => esc_html__('Closing HTML Tag(s)', 'ucpm'),
		'desc' => esc_html__('Used for theme compatability, this option will override the closing HTML for all properties pages.', 'ucpm') . '<br>' .
		__('This can help you to match the HTML with your current theme.', 'ucpm'),
		'id' => 'closing_html',
		'type' => 'textarea',
		'attributes' => array(
			'placeholder' => '</div></div>',
			'rows' => 2,
		),
	));
	$cmb->add_field(array(
		'name' => esc_html__('Hide In-content sidebar page', 'ucpm'),
		'desc' => esc_html__('Used for removing in-content sidebar on single-property page.', 'ucpm'),
		'id' => 'ucpm_hide_in_content_sidebar',
		'type' => 'select',
		'default' => 'no',
		'options' => array(
			'yes' => esc_html__('Yes', 'ucpm'),
			'no' => esc_html__('No', 'ucpm'),
		),
	));
	
	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// uninstall
	$cmb = new_cmb2_box(array(
		'id' => 'uninstall',
		'title' => esc_html__('Uninstall', 'ucpm'),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => esc_html__('Delete Data', 'ucpm'),
		'desc' => esc_html__('Should all plugin data be deleted upon uninstalling this plugin?', 'ucpm'),
		'id' => 'delete_data',
		'type' => 'select',
		'default' => 'no',
		'options' => array(
			'yes' => esc_html__('Yes', 'ucpm'),
			'no' => esc_html__('No', 'ucpm'),
		),
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// box 3, in sidebar of our two-column layout
	$cmb = new_cmb2_box(array(
		'id' => 'side_metabox',
		'title' => esc_html__('Save Options', 'ucpm'),
		'show_on' => $show_on,
		'context' => 'side',
	));
	$cmb->add_field(array(
		'name' => esc_html__('Publish?', 'ucpm'),
		'desc' => esc_html__('Save Changes', 'ucpm'),
		'id' => 'ucpm_save_button',
		'type' => 'ucpm_options_save_button',
		'show_names' => false,
	));
	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// Arguments array. See the arguments page for more detail
	$args = array(
		'key' => $opt_key,
		'title' => esc_html__('UCPM Settings', 'ucpm'),
		'topmenu' => 'edit.php',
		'postslug' => 'listing',
		'boxes' => $boxes,
		'tabs' => $tabs,
		'cols' => 2,
		'savetxt' => '',
	);

	new Cmb2_Metatabs_Options(apply_filters('ucpm_admin_options', $args, $cmb));
}

/**
 * Hook in and add a metabox to add fields to the user profile pages
 */
function ucpm_add_user_fields() {
    $prefix = 'ucpm_user_';

    /**
     * Metabox for the user profile screen
     */
    $cmb_user = new_cmb2_box( array(
        'id'               => $prefix . 'edit',
        'title'            => esc_html__( 'User Profile Fields', 'ucpm' ), // Doesn't output for user boxes
        'object_types'     => array( 'user' ), // Tells CMB2 to use user_meta vs post_meta
        'show_names'       => true,
        'new_user_section' => 'add-new-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
    ) );

    $cmb_user->add_field( array(
        'name'    => esc_html__( 'Avatar', 'cmb2' ),
        'desc'    => esc_html__( 'field description (optional)', 'ucpm' ),
        'id'      => $prefix . 'avatar',
        'type'    => 'file',
    ) );

    $cmb_user->add_field( array(
        'name'    => 'Office Tel',
        'desc'    => esc_html__( 'field tel (optional)', 'ucpm' ),
        'id'      => $prefix . 'tel',
        'type'    => 'text',
    ) );

    $cmb_user->add_field( array(
        'name'    => 'Mobile Tel',
        'desc'    => esc_html__( 'field mobile tel (optional)', 'ucpm' ),
        'id'      => $prefix . 'tel_mobile',
        'type'    => 'text',
    ) );

}
add_action( 'cmb2_admin_init', 'ucpm_add_user_fields' );