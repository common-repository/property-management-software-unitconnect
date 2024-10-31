<?php
/**
 * @link http://webdevstudios.com/2015/03/30/use-cmb2-to-create-a-new-post-submission-form/ Original tutorial
 */

class ucpm_Contact_Form {

	public $content_type 	= '';

	public $success_msg 	= '';

	public $error_msg       = '';

	public $show_error      = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'cmb2_init', array( $this, 'register_contact_form' ) );
		add_shortcode( 'ucpm_contact_form', array( $this, 'contact_form_shortcode' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'set_content_type' ) );
		add_action( 'wp_ajax_ucpm_contact_form', array( $this, 'ucpm_contact_form_callback' ) );
		add_action( 'wp_ajax_nopriv_ucpm_contact_form', array( $this, 'ucpm_contact_form_callback' ) );
	}

	/**
	 * init
	 */
	public function init() {
		$this->content_type = ucpm_option( 'contact_form_email_type' );
		$this->success_msg  = ucpm_option( 'contact_form_success' );
		$this->error_msg    = ucpm_option( 'contact_form_error' );
		$this->show_error   = ucpm_option( 'contact_form_include_error' );
	}

	/**
	 * [save_quiz_results description]
	 * @return [type] [description]
	 */
	public function ucpm_contact_form_callback() {

		$flag = false;
		$message = '';
		parse_str($_POST['data'], $PostData);
		// If no form submission, bail
		if ( ! empty( $PostData ) && isset( $PostData['object_id'] ) ) {

			// Get CMB2 metabox object
			$cmb = $this->form_instance();
			// Check security nonce
			if ( isset( $PostData[ $cmb->nonce() ] ) && wp_verify_nonce( $PostData[ $cmb->nonce() ], $cmb->nonce() ) ) {

				$post_data = array();
				// Get our shortcode attributes and set them as our initial post_data args
				if ( isset( $PostData['atts'] ) ) {
					foreach ( (array) $PostData['atts'] as $key => $value ) {
						$post_data[ $key ] = sanitize_text_field( $value );
					}
					unset( $PostData['atts'] );
				}
				/**
				 * Fetch remaining sanitized values
				 */
				$meta_values = $cmb->get_sanitized_values( $PostData );
				// set some meta values
				$meta_values['_ucpm_inquiry_listing_title'] = $post_data['listing_title'];
				$meta_values['_ucpm_inquiry_listing_id'] = $post_data['listing_id'];
				unset( $post_data['listing_title'] );
				unset( $post_data['listing_id'] );

				// Create the new post
				$new_submission_id = wp_insert_post( $post_data, true );
				if ( is_wp_error( $new_submission_id ) ) {
					$error = $cmb->prop( 'submission_error', $new_submission_id );
					$message = $this->error_notice( $error );
				} else {
					// Loop through remaining (sanitized) data, and save to post-meta
					foreach ( $meta_values as $key => $value ) {
							if ( is_array( $value ) ) {
								$value = array_filter( $value );
								if( ! empty( $value ) ) {
										update_post_meta( $new_submission_id, $key, $value );
								}
							} else {
								update_post_meta( $new_submission_id, $key, $value );
							}
					}
					$message = $this->success_notice();
					$flag = true;
				}
			} else {
				$error = $cmb->prop( 'submission_error', new WP_Error( 'security_fail', esc_html__( 'Security check failed.', 'ucpm' ) ) );
				$message = $this->error_notice( $error );
			}

		} else {
			$message = $this->error_notice();
		}
		wp_send_json( array(
				'flag'		=> $flag,
				'message'	=> $message,
		) );
		die();
	}

	/**
	 * Set the email content type
	 */
	public function set_content_type( $content_type ) {
		$type = ucpm_option( 'contact_form_email_type' );
		if( $type == 'html_email' ) {
			$return = 'text/html';
		} else {
			$return = 'text/html';	
		}
		return $return;
	}

	/**
	 * The success notice.
	 */
	public function success_notice() {
		return apply_filters( 'ucpm_contact_form_success', '<p class="alert success alert-success">' . esc_html( $this->success_msg ) . '</p>' );
	}

	/**
	 * The error notice.
	 */
	public function error_notice( $error ) {
		$show = $this->show_error == 'yes' ? '<br><strong>' . $error->get_error_message() . '</strong>' : '';
		return apply_filters( 'ucpm_contact_form_error', '<p class="alert error warning alert-warning alert-error">' . esc_html( $this->error_msg ) . $show . '</p>' );
	}

	/**
	 * Register the form and fields for our front-end submission form
	 */
	public function register_contact_form() {

		$metabox = new_cmb2_box( array(
			'id'           	=> 'ucpm_contact_form',
			'title'         => esc_html__( 'Contact Form', 'ucpm' ),
			'object_types' 	=> array( 'listing-inquiry' ),
			'cmb_styles' 	=> false,
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true, // Show field names on the left
		) );

		$fields = array();

		$fields[0] = array(
			'name' => 'URL',
			'desc' => 'Do not fill this field in',
			'id'   => 'url',
			'type' => 'text',
			'attributes' => array(
				'placeholder' => esc_html__( 'Anti spam field', 'ucpm' ),
			)
		);
		$fields[1] = array(
			'name' => 'Comment',
			'desc' => 'Do not fill this field in',
			'id'   => 'comment',
			'type' => 'textarea_small',
			'attributes' => array(
				'placeholder' => esc_html__( 'Anti spam field', 'ucpm' ),
			)
		);

		$fields[10] = array(
			'name' => esc_html__( 'First Name', 'ucpm' ),
			'desc' => '',
			'id'   => '_ucpm_inquiry_first_name',
			'type' => 'text',
			'attributes' => array(
				'required' => 'required',
                'placeholder' => esc_html__( 'First Name', 'ucpm' ),
			)
		);

        $fields[20] = array(
            'name' => esc_html__( 'Last Name', 'ucpm' ),
            'desc' => '',
            'id'   => '_ucpm_inquiry_last_name',
            'type' => 'text',
            'attributes' => array(
                'required' => 'required',
                'placeholder' => esc_html__( 'Last Name', 'ucpm' ),
            )
        );

		$fields[30] = array(
			'name' => esc_html__( 'Email', 'ucpm' ),
			'desc' => '',
			'id'   => '_ucpm_inquiry_email',
			'type' => 'text_email',
			'attributes' => array(
				'required' => 'required',
                'placeholder' => esc_html__( 'Email', 'ucpm' ),
			)
		);

		$fields[40] = array(
			'name' => esc_html__( 'Phone', 'ucpm' ),
			'desc' => '',
			'id'   => '_ucpm_inquiry_phone',
			'type' => 'text',
			'attributes' => array(
				//'type' => 'number'
                'placeholder' => esc_html__( 'Phone', 'ucpm' ),
			)
		);

		$fields[50] = array(
			'name'    => esc_html__( 'Message', 'ucpm' ),
			'id'      => '_ucpm_inquiry_message',
			'type'    => 'textarea',
			'attributes' => array(
				'required' => 'required',
                'placeholder' => esc_html__( 'Message', 'ucpm' ),
			)
		);

		$has_consent	= ucpm_option( 'contact_form_consent_label' ) ? ucpm_option( 'contact_form_consent_label' ) : '';
		$has_consent = apply_filters('contact_form_consent_label', $has_consent);

		// filter the fields & sort numerically
		$fields = apply_filters( 'ucpm_contact_fields', $fields );
		ksort( $fields );

		// loop through ordered fields and add them
		if( $fields ) {
				foreach ($fields as $key => $value) {
						$fields[$key] = $metabox->add_field( $value );
				}
		}

	}

	/**
	 * Gets the front-end-post-form cmb instance
	 *
	 * @return CMB2 object
	 */
	public function form_instance() {
		// Use ID of metabox in wds_frontend_form_register
		$metabox_id = 'ucpm_contact_form';
		// Post/object ID is not applicable since we're using this form for submission
		$object_id  = 'ucpm_contact_form_object_id';
		// Get CMB2 metabox object
		return cmb2_get_metabox( $metabox_id, $object_id );
	}

	/**
	 * Handle the ucpm-contact shortcode
	 *
	 * @param  array  $atts Array of shortcode attributes
	 * @return string       Form html
	 */
	public function contact_form_shortcode( $atts = array() ) {

		// Get CMB2 metabox object
		$cmb = $this->form_instance();

		// Parse attributes
		$atts = shortcode_atts( array(
			'post_title' 	=> sprintf( __( 'Inquiry on listing #%s', 'ucpm' ), get_the_ID() ),
			'post_content' 	=> '',
			'post_author' 	=> 1,
			'post_status' 	=> 'publish',
			'post_type'   	=> 'listing-inquiry',
			'listing_title' => get_the_title(),
			'listing_id' 	=> get_the_ID(),
		), $atts, 'ucpm-contact-form' );

		/*
		 * Let's add these attributes as hidden fields to our cmb form
		 * so that they will be passed through to our form submission
		 */
		foreach ( $atts as $key => $value ) {
			$cmb->add_hidden_field( array(
				'field_args'  => array(
					'id'    => "atts[$key]",
					'type'  => 'hidden',
					'default' => $value,
				),
			) );
		}

		// Initiate our output variable
		$output = '';
		// Get any submission errors
		if ( ( $error = $cmb->prop( 'submission_error' ) ) && is_wp_error( $error ) ) {
			// If there was an error with the submission, add it to our ouput.
			$output .= $this->error_notice( $error );
		}

		// If the post was submitted successfully, notify the user.
		if ( isset( $_GET['success'] ) && ( $post = get_post( absint( sanitize_text_field( $_GET['success'] ) ) ) ) ) {

			// Add notice of submission to our output
			$output .= $this->success_notice();

		}
				$loader_image = '';
		// Get our form
		$output .= cmb2_get_metabox_form( $cmb, 'ucpm_contact_form_object_id', array( 'save_button' => esc_html__( 'Contact', 'ucpm' ) ) );
		$consent_desc	= ucpm_option( 'contact_form_consent_desc' ) ? ucpm_option( 'contact_form_consent_desc' ) : '';
		$consent_desc	= apply_filters('contact_form_consent_desc', $consent_desc);

		return $output;

	}

	/**
	 * Get email from
	 */
	public function email_from(){
		$from_email	= ucpm_option( 'email_from' ) ? ucpm_option( 'email_from' ) : get_bloginfo( 'admin_email' );
		$from_name 	= ucpm_option( 'email_from_name' ) ? ucpm_option( 'email_from_name' ) : get_bloginfo( 'name' );
		return apply_filters( 'ucpm_email_from', wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES ) . ' <' . sanitize_email( $from_email ) . '>' );
	}

	/**
	 * Email recipient (the agent)
	 */
	public function recipient( $listing_ID ){
		$admin_email    = get_bloginfo( 'admin_email' );
		return apply_filters( 'ucpm_contact_form_recipient', sanitize_email( $admin_email ) );
	}

	/**
	 * Email Cc
	 */
	public function cc(){
		$return = ucpm_option( 'contact_form_cc' );
		return apply_filters( 'ucpm_contact_form_cc', $return );
	}

	/**
	 * Email Bcc
	 */
	public function bcc(){
		$return = ucpm_option( 'contact_form_bcc' );
		return apply_filters( 'ucpm_contact_form_bcc', $return );
	}

	/**
	 * Email headers
	 */
	public function headers( $inquiry_email ){
		$headers[] = 'From: ' . $this->email_from();
				$headers[] = 'Reply-To: ' . $inquiry_email;
				if( $this->cc() ) {
						$headers[] = 'Cc: ' . $this->cc();
				}
				if( $this->bcc() ) {
						$headers[] = 'Bcc: ' . $this->bcc();
				}
		return apply_filters( 'ucpm_contact_form_headers', $headers );
	}

	/**
	 * Email Subject
	 */
	public function subject(){
		$subject = ucpm_option( 'contact_form_subject' );
		if( ! isset( $subject ) || empty( $subject ) ) {
			$subject = __( 'New inquiry on listing #{property_id}', 'ucpm' );
		}
		return apply_filters( 'ucpm_contact_form_subject', $subject );
	}

	/**
	 * Email Message
	 */
	public function message(){
		$message = ucpm_option( 'contact_form_message' );
		if( ! isset( $message ) || empty( $message ) ) {
			$message =  esc_html__( 'Hi,', 'ucpm' ) . "\r\n" .
						__( 'There has been a new inquiry on <strong>{property_title}</strong>', 'ucpm' ) . "\r\n" .
						__( 'Name: {inquiry_name}', 'ucpm' ) . "\r\n" .
						__( 'Email: {inquiry_email}', 'ucpm' ) . "\r\n" .
						__( 'Phone: {inquiry_phone}', 'ucpm' ) . "\r\n" .
						__( 'Message: {inquiry_message}', 'ucpm' ) . "\r\n";
		}
		return apply_filters( 'ucpm_contact_form_message', wpautop( wp_kses_post( $message ) ) );
	}

	/**
	 * Find
	 */
	public function find() {
		$find = array();
		$find['listing_title']      = '{property_title}';
		$find['listing_id']         = '{property_id}';
		$find['inquiry_name']       = '{inquiry_name}';
		$find['inquiry_email']      = '{inquiry_email}';
		$find['inquiry_phone']      = '{inquiry_phone}';
		$find['inquiry_message']    = '{inquiry_message}';
		return apply_filters( 'ucpm_contact_form_find', $find );
	}

	/**
	 * Replace
	 */
	public function replace( $listing_ID, $inquiry_ID ) {
		$replace = array();
		$replace['listing_title']   = get_the_title( $listing_ID );
		$replace['listing_id']      = $listing_ID;
		$replace['inquiry_name']    = get_post_meta( $inquiry_ID, '_ucpm_inquiry_first_name', true ) . ' ' . get_post_meta( $inquiry_ID, '_ucpm_inquiry_last_name', true );;
		$replace['inquiry_email']   = get_post_meta( $inquiry_ID, '_ucpm_inquiry_email', true );
		$replace['inquiry_phone']   = get_post_meta( $inquiry_ID, '_ucpm_inquiry_phone', true );
		$replace['inquiry_message'] = get_post_meta( $inquiry_ID, '_ucpm_inquiry_message', true );
		return apply_filters( 'ucpm_contact_form_replace', $replace );
	}

	/**
	 * Format email string.
	 *
	 */
	public function format_string( $string, $listing_ID, $inquiry_ID ) {
		return str_replace( $this->find(), $this->replace( $listing_ID, $inquiry_ID ), esc_html__( $string ) );
	}

	/**
	 * Notify agents by sending them an email
	 *
	 */
	public function send_notification( $listing_ID, $inquiry_ID ){

		// to
		$to = $this->recipient( $listing_ID );

		// subject
		$subject = $this->format_string( $this->subject(), $listing_ID, $inquiry_ID );

		// message
		$message = $this->format_string( $this->message(), $listing_ID, $inquiry_ID );

				// set headers
		$headers = $this->headers( get_post_meta( $inquiry_ID, '_ucpm_inquiry_email', true ) );

		if( $sent = wp_mail( $to, $subject, $message, $headers ) ) {

				$existing_inquiries 	= get_post_meta( $listing_ID, '_ucpm_listing_inquiries', true );
				$existing_inquiries[] 	= $inquiry_ID;
				update_post_meta( $listing_ID, '_ucpm_listing_inquiries', $existing_inquiries );

		} else {
				//pp( 'no worky' );
		}

	}

}

return new ucpm_Contact_Form();