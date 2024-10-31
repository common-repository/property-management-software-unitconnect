/*
 * Property Management Software by UnitConnect
 * https://wordpress.com/plugins/ucpm/
 */
(function ($) {

	$("#ucpm-geocomplete").trigger("geocode");

	var lat = $("input[name=_ucpm_listing_lat]").val();
	var lng = $("input[name=_ucpm_listing_lng]").val();

	var location = [lat, lng];
	$("#ucpm-geocomplete").geocomplete({
		map: ".ucpm-admin-map",
		details: "#post", // form id
		detailsAttribute: "data-geo",
		types: ["geocode", "establishment"],
		location: location,
		markerOptions: {
			draggable: true
		}
	});

	$("#ucpm-geocomplete").bind("geocode:dragged", function (event, latLng) {
		$("input[name=_ucpm_listing_lat]").val(latLng.lat());
		$("input[name=_ucpm_listing_lng]").val(latLng.lng());
	});

	$("#ucpm-find").click(function () {
		$("#ucpm-geocomplete").trigger("geocode");
	});

	$("#ucpm-reset").click(function () {
		$("#ucpm-geocomplete").geocomplete("resetMarker");
		return false;
	});

})(jQuery);