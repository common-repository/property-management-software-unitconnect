/*
 * Property Management Software by UnitConnect
 * https://wordpress.com/plugins/ucpm/
 */
(function ($) {
	/**
	 * Archive page
	 */
	wp_real_estate_view_switcher();
	wp_real_estate_ordering();
	wp_real_estate_buy_sell();

	/**
	 * Single property
	 */
	if ($('body').hasClass('ucpm')) {
		if ($('body').find('#ucpm-map').length > 0) {
			wp_real_estate_google_map();
		}
	}

	/**
	 * ================================= FUNCTIONS =======================================
	 */

	/**
	 * Ordering
	 */
	function wp_real_estate_ordering() {
		$('.ucpm-ordering').on('change', 'select.properties-orderby', function () {
			var orderby = $(this).val();
			var search_form_data = $(this).closest('form').serialize();
			ucpm_orderby_ajax_filter( 'ucpm_orderby_value', orderby, search_form_data );
		});

		$('.ucpm-ordering').on('change', 'select.ucpm-city-select', function () {
			var orderby = $(this).closest('.ucpm-ordering').find('select.properties-orderby').val();
			var search_form_data = $(this).closest('form').serialize();

			ucpm_orderby_ajax_filter( 'ucpm_orderby_value', orderby, search_form_data );
		});

		$('.ucpm-search-field').on('keyup', function () {
			var orderby = $(this).closest('.ucpm-ordering').find('select.properties-orderby').val();
			var search_form_data = $(this).closest('form').serialize();
			ucpm_orderby_ajax_filter( 'ucpm_orderby_value', orderby, search_form_data );
		});
	}

	function ucpm_orderby_ajax_filter( action, orderby, formdata ) {

		$('#ucpm-archive-wrapper').find('.ucpm-orderby-loader').addClass('in');
		$.ajax({
			type: 'POST',
			url: ucpm.ajax_url,
			data: {
				'action': action,
				'order_by': orderby,
				'search_data': formdata
			},
			success: function (response) {
				$('#ucpm-archive-wrapper').find('.ucpm-items').html(response);
				$('#ucpm-archive-wrapper').find('.ucpm-orderby-loader').removeClass('in')
				var newurl = window.location.pathname;
				if( window.location.search == '' ) {
					newurl = newurl+'?ucpm-orderby='+orderby;
				} else {
					var search_string = window.location.search;
					if (search_string.indexOf("ucpm-orderby") <= 0) {
						newurl = window.location.href+'&ucpm-orderby='+orderby;
					} else {
						var search_parameters = search_string.split('&');
						jQuery.each(search_parameters, function(key, value){
							if (value.indexOf("ucpm-orderby") >= 0) {
								var orderby_value = value.split('=');
								newurl = newurl+orderby_value[0]+'='+orderby;
							} else {
								newurl = newurl+value;
							}
							if( search_parameters.length < (key+1) ) {
								newurl = newurl + '&';
							}
						});
					}
				}
				$('body').find('.ucpm-pagination').attr('data-orderby', orderby);
				window.history.pushState({path:newurl},'',newurl);
			}
		});

	}

	$('.ucpm-search-btn').on('click', function () {
		$(this).parent().find('.ucpm-search-field').toggle();
	});

	$('.ucpm-city-btn').on('click', function () {
		$(this).parent().find('.ucpm-city-select').toggle();
	});

	$('.ucpm-select-btn').on('click', function () {
		$(this).parent().find('.properties-orderby').toggle();
	});

	$('.ucpm-pagination a').on('click', function(e){
		e.preventDefault();
		var url = $(this).attr('href');
		var orderby = $(this).parents('.ucpm-pagination').attr('data-orderby');
		url = url+'&ucpm-orderby='+orderby;
		window.location.href = url;
		return false;
	});

	/**
	 * Buy/Sell option
	 */
	function wp_real_estate_buy_sell() {
		$('.ucpm-search-form').on('change', 'select.purpose', function () {
			if ($(this).parents('.widget').length == 0) {
				$(this).closest('form').submit();
			}
		});
	}

	/**
	 * View switcher
	 */
	function wp_real_estate_view_switcher() {

		$('.ucpm-view-switcher div').click(function () {
			var view = $(this).attr('id');
			switch_view(view);
		});

		function switch_view(to) {

			var from = (to == 'list') ? 'grid' : 'list';

			var ucpm_items = $('.ucpm-items li');
			$.each(ucpm_items, function (index, property) {
				if ($(this).parents('.widget').length == 0) {
					$(this).parents('.ucpm-items').removeClass(from + '-view');
					$(this).parents('.ucpm-items').addClass(to + '-view');
				}

			});
		}

	}
	/**
	 * Google map
	 */
	function wp_real_estate_google_map() {
		if (ucpm.lat) {
			var lat = ucpm.lat;
			var lng = ucpm.lng;
			var options = {
				center: new google.maps.LatLng(lat, lng),
				zoom: parseInt(ucpm.map_zoom),
			}
			var mapClass = $('.ucpm-map');
			
			$.each(mapClass, function (key, value) {
				ucpm_map = new google.maps.Map(mapClass[key], options);
				var position = new google.maps.LatLng(lat, lng);
				var set_marker = new google.maps.Marker({
					icon: ' ',
					label: {
						fontFamily: 'ucpmwp',
						text: "\140",
						fontSize: '60px',
						color: '#44a3d3'
					},
					map: ucpm_map,
					position: position
				});
			});

		}

	}

	$('.ucpm-contact-form .cmb-form').on('submit', function (e) {

		e.preventDefault();
		var $form = $(this);
		$form.addClass('in');
		var speed = 700;
		$('html, body').animate({scrollTop: $form.parent().offset().top}, speed);
		$.ajax({
			type: 'POST',
			url: ucpm.ajax_url,
			data: {
				'action': 'ucpm_contact_form',
				'data': $(this).serialize(),
			},
			success: function (response) {

				$form.removeClass('in');
				$form.parent().find('.message-wrapper').html(response.message);
				$('html, body').animate({scrollTop: $form.parent().offset().top - 150}, speed);
				$form.find('input#_ucpm_inquiry_first_name').val('');
				$form.find('input#_ucpm_inquiry_last_name').val('');
				$form.find('input#_ucpm_inquiry_email').val('');
				$form.find('input#_ucpm_inquiry_phone').val('');
				$form.find('textarea#_ucpm_inquiry_message').val('');

			}
		}).error(function () {
			var html = '<p class="alert error warning alert-warning alert-error">There was an error. Please try again.</p>';
			$form.removeClass('in');
			$form.parent().find('.message-wrapper').html(html);
			$('html, body').animate({scrollTop: $form.parent().offset().top - 150}, speed);
		});
		return false;

	});

	if ($('.nearby-properties-wrapper').length > 0) {
		var lat, lng;
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function (position) {

				lat = position.coords.latitude;
				lng = position.coords.longitude;
				$('.nearby-properties-wrapper').each(function () {

					var $this = $(this);
					var data = {};
					data['current_lat'] = position.coords.latitude;
					data['current_lng'] = position.coords.longitude;
					data['measurement'] = $this.attr('data-distance');
					data['radius'] = $this.attr('data-radius');
					data['number'] = $this.attr('data-number');
					data['compact'] = $this.attr('data-compact');

					$.ajax({
						type: 'POST',
						url: ucpm.ajax_url,
						data: {
							'action': 'ucpm_nearby_properties',
							'data': data
						},
						success: function (response) {

							var view = $this.attr('data-property-view');
							var columns = $this.attr('data-columns');
							$this.html(response.data);
							$this.find('ul.ucpm-items').addClass(view);
							if (view == 'grid-view')
								$this.find('ul.ucpm-items li.compact').removeClass('compact');

							$this.find('ul.ucpm-items li').removeClass(function (index, className) {
								return (className.match(/(^|\s)col-\S+/g) || []).join(' ');
							}).addClass('col-' + columns);

							$this.removeAttr('data-distance data-radius data-number data-compact data-property-view data-columns');

						}
					}).error(function () {
						$this.html('No properties near your location');
					});

				});
			});
		}
	}

})(jQuery);