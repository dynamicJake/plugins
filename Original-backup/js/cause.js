jQuery(".donate-amount").change(function() {
	var $amount = this.value;
	jQuery('input[name$="amount"]').val($amount);
	jQuery('input[name$="Custom Donation Amount"]').on("keyup", function() {
		var sn = jQuery(this).val();
		jQuery('input[name$="amount"]').val(sn);
	});
});
jQuery("a#donate-popup").click(function() {
	jQuery(".donate-amount").val(20);
	jQuery('form.sai input[name$="amount"]').val(20);
	jQuery('.custom-donate-amount').hide();
	jQuery('input[name$="Custom Donation Amount"]').val('');
});
function ValidateEmail(email) {
	var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	return expr.test(email);
}; 
jQuery('.paypal-submit-form').submit(function(e) {
	jQuery("label.error").hide();
	jQuery(".error").removeClass("error");
	var $formid = jQuery(this).attr('id');
	jQuery('form#'+$formid+' #message').empty();
	var $userfield = jQuery("form#"+$formid+" #username");
	var $emailfield = jQuery("form#"+$formid+" #email");
	var $amount = jQuery("form#"+$formid+" input[name$=amount]").val();
	var $itemnumber = jQuery("form#"+$formid+" input[name$=item_number]").val();
	var $phone = jQuery("form#"+$formid+" #phone").val();
	var $reg_status = jQuery("form#"+$formid+" #reg-status").val();
	var $event_reg_date = jQuery("form#"+$formid+" #event-reg-date").val();
	var $postname = jQuery("form#"+$formid+" #postname").val();
	var $address = jQuery("form#"+$formid+" #address").val();
	var $notes = jQuery("form#"+$formid+" #notes").val();
	var $lastname = jQuery("form#"+$formid+" #lastname").val();
	var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var isValid = true;
	if (jQuery.trim($userfield.val()) == '') {
		isValid = false;
		jQuery('form#'+$formid+' #message').append("<div class=\"alert alert-error\">You must enter your name</div>");
		return false;
	} else if(!ValidateEmail($emailfield.val())) {
		isValid = false;
		jQuery('form#'+$formid+' #message').append("<div class=\"alert alert-error\">You must enter your email</div>");
		return false;
	} else {
		jQuery('form#'+$formid+' #message').empty();
		if($amount!=''||$amount!=0) {
		jQuery('form#'+$formid+' #message').append("<div class=\"alert alert-success\">You are redirecting to paypal</div>"); }
		else {
		jQuery('form#'+$formid+' #message').append("<div class=\"alert alert-success\">Registering you for Event</div>"); }
		jQuery.ajax({
			type: 'POST',
			url: urlajax.ajaxurl,
			async: false,
			data: {
				action: 'imic_event_grids',
				itemnumber: $itemnumber,
				reg_date: $event_reg_date,
				reg_status: $reg_status,
				name: $userfield.val(),
				lastname: $lastname,
				email: $emailfield.val(),
				amount: $amount,
				phone: $phone,
				address: $address,
				notes: $notes,
				posttype: $postname,
			},
			success: function(data) {
			},
			complete: function() {
			}
	
	 	});
   	}
	if (isValid == false) {	e.preventDefault(); }
});