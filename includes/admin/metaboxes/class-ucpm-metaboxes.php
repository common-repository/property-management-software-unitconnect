<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('ucpm_Metaboxes')) :
    /**
     * CMB2 Theme Options
     * @version 0.1.0
     */
    class ucpm_Metaboxes
    {

        /**
         * Post type
         * @var string
         */
        public $type = 'listing';

        /**
         * Metabox prefix
         * @var string
         */
        public $prefix = '_ucpm_listing_';

        public $listing_label = '';

        /**
         * Holds an instance of the object
         *
         * @var Myprefix_Admin
         **/
        public static $instance = null;

        public function __construct()
        {
            $this->listing_label = esc_html__('Listing', 'ucpm');
        }

        /**
         * Returns the running object
         *
         * @return Myprefix_Admin
         **/
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();

                self::$instance->description();
                self::$instance->features();
                self::$instance->images();
                self::$instance->documents();
                self::$instance->sale();
                self::$instance->lease();

                self::$instance->address();
                self::$instance->contacts();
                self::$instance->status();
            }
            return self::$instance;
        }

        /*==================== Listing Description ====================*/

        public function description()
        {

            $box = new_cmb2_box(array(
                'id' => $this->prefix . 'description',
                'title' => sprintf(__("%s Description", 'ucpm'), $this->listing_label),
                'object_types' => array($this->type),
                'priority' => 'high'
            ));

            $fields = array();

            $fields[10] = array(
                'name'             => esc_html__( 'Display Property', 'ucpm' ),
                'id'               => $this->prefix . 'display_property',
                'desc'             => esc_html__('','ucpm'),
                'type'	           => 'switch',
                'default'          => true,
            );

            $fields[20] = array(
                'name' => esc_html__('Name', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'tagline',
                'type' => 'text',
            );

            $fields[30] = array(
                'name' => esc_html__('Description', 'ucpm'),
                'desc' => '',
                'id' => 'content',
                'type' => 'wysiwyg',
                'options' => array(
                    'wpautop' => true, // use wpautop?
                    'media_buttons' => false, // show insert/upload button(s)
                    'textarea_rows' => get_option('default_post_edit_rows', 3), // rows="..."
                    'teeny' => true, // output the minimal editor config used in Press This
                    'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
                    'quicktags' => false // load Quicktags, can be used to pass settings directly to Quicktags using an array()
                ),
            );

            // filter the fields
            $fields = apply_filters('ucpm_metabox_description', $fields);

            // sort numerically
            ksort($fields);

            // loop through ordered fields and add them to the metabox
            if ($fields) {
                foreach ($fields as $key => $value) {
                    $fields[$key] = $box->add_field($value);
                }
            }

        }

        /*==================== Listing Features ====================*/

        public function features()
        {

            $box = new_cmb2_box(array(
                'id' => $this->prefix . 'features',
                'title' => sprintf(esc_html__("Features", 'ucpm')),
                'object_types' => array($this->type),
                'priority' => 'high'
            ));

            $fields = array();

            $fields[10] = array(
                'name' => esc_html__('Property Size', 'ucpm'),
                'id' => $this->prefix . 'building_size',
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                    'min' => '0',
                    'step' => '1',
                ),
            );
            $fields[11] = array(
                'name' => esc_html__('Land Size', 'ucpm'),
                'id' => $this->prefix . 'land_size',
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                    'min' => '0',
                    'step' => '1',
                ),
            );

            $fields[12] = array(
                'name' => esc_html__('Type', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'type',
                'taxonomy' => 'listing-type', //Enter Taxonomy Slug
                'type' => 'taxonomy_select',
                'show_option_none' => false,
            );

            $fields[20] = array(
                'name' => esc_html__('# Floors', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'floors',
                'type' => 'text',
            );

            $fields[21] = array(
                'name' => esc_html__('# Units', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'units',
                'type' => 'text',
            );

            // filter the fields
            $fields = apply_filters('ucpm_metabox_features', $fields);

            // sort numerically
            ksort($fields);

            // loop through ordered fields and add them to the metabox
            if ($fields) {
                foreach ($fields as $key => $value) {
                    $fields[$key] = $box->add_field($value);
                }
            }

            // setup the columns
            if (!is_admin()) {
                return;
            }
            $cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid($box);

            // define number of rows
            $rows = apply_filters('ucpm_metabox_features_rows', 3);

            // loop through number of rows
            for ($i = 1; $i < $rows; $i++) {

                // add each row
                $row[$i] = $cmb2Grid->addRow();

                // reset the array for each row
                $array = array();

                // this allows up to 4 columns in each row
                if (isset($fields[$i * 10])) {
                    $array[] = $fields[$i * 10];
                }
                if (isset($fields[$i * 10 + 1])) {
                    $array[] = $fields[$i * 10 + 1];
                }
                if (isset($fields[$i * 10 + 2])) {
                    $array[] = $fields[$i * 10 + 2];
                }

                // add the fields as columns
                $row[$i]->addColumns(
                    apply_filters("ucpm_metabox_features_row_{$i}_columns", $array)
                );

            }

        }

        /*==================== Gallery ====================*/
        public function images()
        {

            $box = new_cmb2_box(array(
                'id' => $this->prefix . 'images',
                'title' => esc_html__("Image Gallery", 'ucpm'),
                'object_types' => array($this->type),
                'priority' => 'high'
            ));

            $fields = array();

            $fields[10] = array(
                'name' => esc_html__('Image Gallery', 'ucpm'),
                'desc' => esc_html__('The first image will be used as the main feature image. Drag and drop to re-order.', 'ucpm'),
                'id' => $this->prefix . 'image_gallery',
                'type' => 'file_list',
                'preview_size' => array(150, 100), // Default: array( 50, 50 )
                'text' => array(
                    'add_upload_files_text' => esc_html__('Add Images', 'ucpm'),
                ),
            );

            // filter the fields
            $fields = apply_filters('ucpm_metabox_images', $fields);

            // sort numerically
            ksort($fields);

            // loop through ordered fields and add them to the metabox
            if ($fields) {
                foreach ($fields as $key => $value) {
                    $fields[$key] = $box->add_field($value);
                }
            }
        }

        /*==================== Sale ====================*/
        public function sale()
        {
            $box = new_cmb2_box(array(
                'id' => $this->prefix . 'sale',
                'title' => sprintf(esc_html__("Sale", 'ucpm')),
                'object_types' => array($this->type),
                'priority' => 'high'
            ));

            $fields = array();

            $fields[10] = array(
                'name'             => esc_html__( 'Show Sale', 'ucpm' ),
                'id'               => $this->prefix . 'show_sale',
                'desc'             => esc_html__('','ucpm'),
                'type'	           => 'switch',
                'default'          => true,
            );

            $fields[20] = array(
                'name' => esc_html__('Asking Price', 'ucpm'),
                'id' => $this->prefix . 'asking_price',
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                    'min' => '0',
                    'step' => '1',
                ),
            );
            $fields[21] = array(
                'name' => esc_html__('NOI', 'ucpm'),
                'id' => $this->prefix . 'noi',
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                    'min' => '0',
                    'step' => '1',
                ),
            );

            $fields[30] = array(
                'name' => esc_html__('CAP Rate', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'cap_rate',
                'type' => 'text',
            );

            $fields[31] = array(
                'name' => esc_html__('Status', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'status',
                'taxonomy' => 'listing-status', //Enter Taxonomy Slug
                'type' => 'taxonomy_select',
                'show_option_none' => false,
            );

            $fields[40] = array(
                'name' => esc_html__('Description', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'sale_description',
                'type' => 'wysiwyg',
                'options' => array(
                    'wpautop' => true, // use wpautop?
                    'media_buttons' => false, // show insert/upload button(s)
                    'textarea_rows' => get_option('default_post_edit_rows', 3), // rows="..."
                    'teeny' => true, // output the minimal editor config used in Press This
                    'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
                    'quicktags' => false // load Quicktags, can be used to pass settings directly to Quicktags using an array()
                ),
            );


            // filter the fields
            $fields = apply_filters('ucpm_metabox_sale', $fields);

            // sort numerically
            ksort($fields);

            // loop through ordered fields and add them to the metabox
            if ($fields) {
                foreach ($fields as $key => $value) {
                    $fields[$key] = $box->add_field($value);
                }
            }

            // setup the columns
            if (!is_admin()) {
                return;
            }
            $cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid($box);

            // define number of rows
            $rows = apply_filters('ucpm_metabox_sale_rows', 4);

            // loop through number of rows
            for ($i = 1; $i < $rows; $i++) {

                // add each row
                $row[$i] = $cmb2Grid->addRow();

                // reset the array for each row
                $array = array();

                // this allows up to 4 columns in each row
                if (isset($fields[$i * 10])) {
                    $array[] = $fields[$i * 10];
                }
                if (isset($fields[$i * 10 + 1])) {
                    $array[] = $fields[$i * 10 + 1];
                }
                if (isset($fields[$i * 10 + 2])) {
                    $array[] = $fields[$i * 10 + 2];
                }
                if (isset($fields[$i * 10 + 3])) {
                    $array[] = $fields[$i * 10 + 3];
                }

                // add the fields as columns
                $row[$i]->addColumns(
                    apply_filters("ucpm_metabox_sale_row_{$i}_columns", $array)
                );

            }
        }

        /*==================== Lease ====================*/
        public function lease()
        {
            $box = new_cmb2_box(array(
                'id' => $this->prefix . 'lease',
                'title' => sprintf(__("Lease", 'ucpm')),
                'object_types' => array($this->type),
                'priority' => 'high'
            ));

            $fields = array();

            $fields[10] = array(
                'name'             => esc_html__( 'Show Lease', 'ucpm' ),
                'id'               => $this->prefix . 'show_lease',
                'desc'             => esc_html__('','ucpm'),
                'type'	           => 'switch',
                'default'          => true,
            );

             $fields[20] = array(
                'id' => $this->prefix . 'lease_items',
                'type' => 'group',
                'name'  => esc_html__( 'Lease items', 'ucpm' ),
                'description' => '',
                'options' => array(
                    'group_title' => esc_html__('Lease Items', 'ucpm'),
                    'add_button' => esc_html__('Add', 'ucpm'),
                    'remove_button' => esc_html__('Remove', 'ucpm'),
                    'sortable' => true, // beta
                ),
            );

            $fields[21] = array(
                'name' => esc_html__('Space', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'lease_space',
                'type' => 'text',
            );

            $fields[22] = array(
                'name' => esc_html__('Description', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'lease_desc',
                'type' => 'text',
            );

            $fields[23] = array(
                'name' => esc_html__('Size', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'lease_size',
                'type' => 'text',
            );

            $fields[24] = array(
                'name' => esc_html__('Asking', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'lease_asking',
                'type' => 'text',
            );

            $fields[25] = array(
                'name' => 'Available',
                'id'   => $this->prefix . 'lease_available',
                'type' => 'checkbox',
            );

            $fields[30] = array(
                'name' => esc_html__('Description', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'lease_description',
                'type' => 'wysiwyg',
                'options' => array(
                    'wpautop' => true, // use wpautop?
                    'media_buttons' => false, // show insert/upload button(s)
                    'textarea_rows' => get_option('default_post_edit_rows', 3), // rows="..."
                    'teeny' => true, // output the minimal editor config used in Press This
                    'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
                    'quicktags' => false // load Quicktags, can be used to pass settings directly to Quicktags using an array()
                ),
            );


            // filter the fields
            $fields = apply_filters('ucpm_metabox_lease', $fields);

            // sort numerically
            ksort($fields);

            // loop through ordered fields and add them to the metabox
            if ($fields) {
                foreach ($fields as $key => $value) {
                    if ( $key === 10 || $key === 20 || $key === 30 ) {
                        $fields[$key] = $box->add_field($value);
                    } else {
                        $fields[$key] = $box->add_group_field($this->prefix . 'lease_items', $value);
                    }
                }
            }

            // setup the columns
            if (!is_admin()) {
                return;
            }
            $cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid($box);

            // define number of rows
            $rows = apply_filters('ucpm_metabox_lease_rows', 3);

            // loop through number of rows
            for ($i = 1; $i < $rows; $i++) {

                // add each row
                $row[$i] = $cmb2Grid->addRow();

                // reset the array for each row
                $array = array();

                // this allows up to 4 columns in each row
                if (isset($fields[$i * 10])) {
                    $array[] = $fields[$i * 10];
                }

                // add the fields as columns
                $row[$i]->addColumns(
                    apply_filters("ucpm_metabox_lease_row_{$i}_columns", $array)
                );

            }
        }

        /*==================== Documents ====================*/
        public function documents()
        {

            $box = new_cmb2_box(array(
                'id' => $this->prefix . 'documents',
                'title' => esc_html__("Documents", 'ucpm'),
                'object_types' => array($this->type),
                'priority' => 'high'
            ));

            $fields = array();

            $fields[10] = array(
                'name' => esc_html__('Documents', 'ucpm'),
                'id' => $this->prefix . 'property_documents',
                'type' => 'file_list',
                'preview_size' => array(150, 100), // Default: array( 50, 50 )
                'text' => array(
                    'add_upload_files_text' => esc_html__('Add Document', 'ucpm'),
                ),
            );

            // filter the fields
            $fields = apply_filters('ucpm_metabox_documents', $fields);

            // sort numerically
            ksort($fields);

            // loop through ordered fields and add them to the metabox
            if ($fields) {
                foreach ($fields as $key => $value) {
                    $fields[$key] = $box->add_field($value);
                }
            }
        }

        /*==================== Listing Status ====================*/

        public function status()
        {

            $box = new_cmb2_box(array(
                'id' => $this->prefix . 'status',
                'title' => sprintf(__("Inquiries", 'ucpm'), $this->listing_label),
                'object_types' => array($this->type),
                'context' => 'side',
            ));

            $fields = array();

            $fields[10] = array(
                'name' => '',
                'desc' => '',
                'id' => '',
                'type' => 'title',
                'after_row' => 'ucpm_admin_status_area',
            );

            // filter the fields
            $fields = apply_filters('ucpm_metabox_status', $fields);

            // sort numerically
            ksort($fields);

            // loop through ordered fields and add them to the metabox
            if ($fields) {
                foreach ($fields as $key => $value) {
                    $fields[$key] = $box->add_field($value);
                }
            }

        }


        /*==================== Listing Address ====================*/

        public function address()
        {

            $box = new_cmb2_box(array(
                'id' => $this->prefix . 'address',
                'title' => sprintf(__("Address", 'ucpm')),
                'object_types' => array($this->type),
                'context' => 'side',
            ));

            $fields = array();

            $fields[10] = array(
                'name' => esc_html__('Address', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'displayed_address',
                'type' => 'text',
                'attributes' => array(
                    'data-geo' => 'formatted_address',
                ),
            );

            $fields[15] = array(
                'name' => esc_html__('City', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'city',
                'type' => 'text',
                'attributes' => array(
                    'data-geo' => 'locality',
                ),
            );
            $fields[20] = array(
                'name' => esc_html__('State', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'state',
                'type' => 'text',
                'attributes' => array(
                    'data-geo' => 'administrative_area_level_1',
                ),
            );
            $fields[25] = array(
                'name' => esc_html__('County', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'county',
                'type' => 'text',
                'attributes' => array(
                    'data-geo' => 'administrative_area_level_1',
                ),
            );
            $fields[30] = array(
                'name' => esc_html__('Zip', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'zip',
                'type' => 'text',
                'attributes' => array(
                    'data-geo' => 'postal_code',
                ),
            );

            $fields[50] = array(
                'name' => esc_html__('Latitude', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'lat',
                'type' => 'text',
                'attributes' => array(
                    'data-geo' => 'lat',
                ),
            );
            $fields[55] = array(
                'name' => esc_html__('Longitude', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'lng',
                'type' => 'text',
                'attributes' => array(
                    'data-geo' => 'lng',
                ),
            );

            // filter the fields
            $fields = apply_filters('ucpm_metabox_address', $fields);

            // sort numerically
            ksort($fields);

            // loop through ordered fields and add them to the metabox
            if ($fields) {
                foreach ($fields as $key => $value) {
                    $fields[$key] = $box->add_field($value);
                }
            }

        }

        /*==================== Listing Contacts ====================*/

        public function contacts()
        {
            $box = new_cmb2_box(array(
                'id' => $this->prefix . 'contacts',
                'title' => sprintf(__("Contacts", 'ucpm')),
                'object_types' => array($this->type),
                'context' => 'side',
            ));

            $fields = array();

            $fields[10] = array(
                'name'             => esc_html__( 'Show Contacts', 'ucpm' ),
                'id'               => $this->prefix . 'show_contacts',
                'desc'             => esc_html__('','ucpm'),
                'type'	           => 'switch',
                'default'          => true,
            );

            $fields[20] = array(
                'name' => esc_html__('Contact 1', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'contact_1',
                'type' => 'select',
                'show_option_none' => true,
                'options_cb' => 'ucpm_listing_users',
            );

            $fields[25] = array(
                'name' => esc_html__('Contact 2', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'contact_2',
                'type' => 'select',
                'show_option_none' => true,
                'options_cb' => 'ucpm_listing_users',
            );

            $fields[30] = array(
                'name' => esc_html__('Contact 3', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'contact_3',
                'type' => 'select',
                'show_option_none' => true,
                'options_cb' => 'ucpm_listing_users',
            );

            $fields[35] = array(
                'name' => esc_html__('Contact 4', 'ucpm'),
                'desc' => '',
                'id' => $this->prefix . 'contact_4',
                'type' => 'select',
                'show_option_none' => true,
                'options_cb' => 'ucpm_listing_users',
            );


            // filter the fields
            $fields = apply_filters('ucpm_metabox_contacts', $fields);

            // sort numerically
            ksort($fields);

            // loop through ordered fields and add them to the metabox
            if ($fields) {
                foreach ($fields as $key => $value) {
                    $fields[$key] = $box->add_field($value);
                }
            }
        }

    }

endif;