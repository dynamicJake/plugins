jQuery("table.causespayments td span.pay, table.eventspayments td span.pay").click(function(event){
	var dateEvent = jQuery(this).attr('id');
	var this_status = jQuery('td.status span#status-'+dateEvent).html();
	jQuery("select#user-payment-status").val(this_status);
	jQuery.ajax({
		type: 'POST',
		url: ajax.url,
		data: {
			action: 'imic_cause_function',
			id: dateEvent,
		},
		success: function(data) {
			jQuery("div#overlay #popup .msg ").empty();
			jQuery("div#overlay #popup .msg ").append(data);
			var overlay = document.getElementById("overlay");
			var popup = document.getElementById("popup");
			overlay.style.display = "block";
			popup.style.display = "block";
			jQuery("div#overlay .update-btn").attr('id',dateEvent);
			jQuery('.close-btn').click(function(){
				jQuery('.overlay-bg, .overlay-content').hide(); // hide the overlay
			});
		},
		error: function(errorThrown) {
		},
		complete: function(){}
	});
});
jQuery("div#overlay .update-btn").click(function(event){
	var user_id = jQuery(this).attr('id');
	var userstatus = jQuery("#user-payment-status option:selected").text();
	jQuery.ajax({
		type: 'POST',
		url: ajax.url,
		data: {
			action: 'imic_cause_status_function',
			id: user_id,
			status: userstatus,
		},
		success: function(data) {
			jQuery("table td.status span#status-"+user_id).empty();
			jQuery("table td.status span#status-"+user_id).html(data);
			jQuery('.overlay-bg, .overlay-content').hide(); // hide the overlay
		},
		error: function(errorThrown) {
		}
	});
});