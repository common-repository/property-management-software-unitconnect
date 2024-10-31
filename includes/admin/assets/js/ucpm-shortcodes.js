/*
 * Property Management Software by UnitConnect
 * https://wordpress.com/plugins/ucpm/
 */
jQuery(document).ready(function($) {

	tinymce.create('tinymce.plugins.ucpm_plugin', {
			
		init : function(ed, url) {
			function getField(key, data) {

				switch(data.type) {

					case 'listbox':
						return {
								type	: 'listbox',
								name	: key,
								label	: data.label,
								values	: data.values,
								tooltip	: data.tooltip,
								value	: data.value // Sets the default
							};
					break;

					case 'textbox':
						return {
								type	: 'textbox',
								name	: key,
								label	: data.label,
								tooltip	: data.tooltip,
								value	: data.value
							}
					break;
					case 'container':
						return {
							type	: 'container',
							name	: '',
							label	: '',
							html	: '<h1 style="font-weight: 600">'+data.value+'</h1>'
						}
					break;
					default:
					return false;
				}
			}

			// Register buttons - trigger above command when clicked
			ed.addButton('ucpm_shortcodes_dropdown', {
				type: 'listbox',
				text: 'ucpm Shortcodes',
				icon: false,
				onselect: function(e) {
					var shortcode_args = [];
					var attributes = e.target.settings.attributes;
					var shortcode_name = e.target.settings.shortcode_name;

					if( attributes != undefined ) {
						jQuery.each( attributes, function(key, value) {
							var data = getField(key, value);
							if(data)
								shortcode_args.push(data);
						});

						ed.windowManager.open({
							title: e.target.settings.text,
							autoScroll: true,
							body: shortcode_args,
							onsubmit: function( e ) {
								var shortcode_data = '[';
								shortcode_data += shortcode_name;
								$.each(e.data, function(key, value){
									shortcode_data += ' '+key+'="'+value+'"';
								});
								shortcode_data += ']';
								ed.insertContent( shortcode_data );
							}
						});

					}
				}, values: [
					{
						text			: 'ucpm Properties',
						shortcode_name	: 'ucpm_properties',
						value			: '[ucpm_properties]',
						'attributes'	: ucpm_tinyMCE_object.listings_fields
					},

					{
						text			: 'ucpm Search',
						shortcode_name	: 'ucpm_search',
						value			: '[ucpm_search]',
						attributes		: ucpm_tinyMCE_object.search_fields
					},

					{
						text			: 'ucpm Property',
						shortcode_name	: 'ucpm_property',
						value			: '[ucpm_property]',
						attributes		: ucpm_tinyMCE_object.listing_fields
					},

					{
						text			: 'ucpm Map',
						shortcode_name	: 'ucpm_map',
						value			: '[ucpm_map]',
						attributes		: ucpm_tinyMCE_object.ucpm_map_fields
					}

				], onPostRender: function() {
					// Select the second item by default
				}
			});
		}
	});

	// Register our TinyMCE plugin
	// first parameter is the button ID1
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('ucpm_shortcodes_dropdown', tinymce.plugins.ucpm_plugin);
});