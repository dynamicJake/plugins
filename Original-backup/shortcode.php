<?php 

function causes_shortcode($args)
{
	extract( shortcode_atts( array(
		'email' => get_option('paypal_email_address'),
		'cause_id' => '',
		'description' => '',	
		'currency' => get_option('paypal_currency_options'),
		'reference' => '',	
		'return' => '',
		'cancel_url' => '',
       'tax' => '',
	    'paypal_payment' => get_option('paypal_payment_option'),
	), $args));	

	$output = "";

	if(empty($email)){
		$output = '<div id="message"><div class="alert alert-error">Error! Please enter your PayPal email address in causes options page.</div></div>';
		return $output;
	}
	$paypal_payment = ($paypal_payment=="live")?"https://www.paypal.com/cgi-bin/webscr":"https://www.sandbox.paypal.com/cgi-bin/webscr";

        $window_target = '';
        if(!empty($new_window)){
            $window_target = 'target="_blank"';
        }
	$output .= '<div class="wp_paypal_button_widget_any_amt">';
	$output .= '<form id="cause-'.$cause_id.'" class="paypal-submit-form sai" name="_xclick" action="'.$paypal_payment.'" method="post" '.$window_target.'>';

	if(!empty($reference)){
		$output .= '<div class="wp_pp_button_reference_section">';
		$output .= '<label for="wp_pp_button_reference">'.$reference.'</label>';
		$output .= '<br />';
		$output .= '<input type="hidden" name="on0" value="Reference" />';
		$output .= '<input type="text" name="os0" value="" class="wp_pp_button_reference" />';
		$output .= '</div>';
	}
	$this_email = '';
	$this_fname = '';
	$this_lname = '';
	$this_username = '';
	$this_actualname = '';
	if(is_user_logged_in()) {
	global $current_user;
      get_currentuserinfo();
	  $this_email = $current_user->user_email;
	  $this_fname = $current_user->user_firstname;
	  $this_lname = $current_user->user_lastname;
	  $this_username = $current_user->display_name;
	  $this_actualname = ($this_fname=='')?$this_username:$this_fname; }
	$unique = uniqid();
	$output .= '<div class="row">
                        	<div class="col-md-6">
                                <label>'.__('How much would you like to donate?','framework').'</label>
                                <div class="input-group margin-20">
                                    <span class="input-group-addon">'.imic_get_currency_symbol(get_option('paypal_currency_options')).'</span>
                                    <select id="amount"'.get_the_ID().'" name="donation amount" class="form-control donate-amount">
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="101">100+</option>
                                    </select>
                                </div>
                            </div>
                        	<div class="col-md-6 custom-donate-amount">
                                <label>'.__('Enter custom donation amount','framework').'</label>
                                <div class="input-group margin-20">
                                    <span class="input-group-addon">'.imic_get_currency_symbol(get_option('paypal_currency_options')).'</span>
                        			<input type="text" id="101" name="Custom Donation Amount" class="form-control">
									<input type="hidden" value="'.get_post_meta($cause_id,'imic_event_registration_fee',true).'" id="reg-status">
									<input type="hidden" value="'.get_query_var('event_date').'" id="event-reg-date">
                                </div>
                            </div>
                        </div>
                    	<div class="row">
                        	<div class="col-md-6">
                        		<input type="text" value="'.$this_actualname.'" id="username" name="fname" class="form-control" placeholder="First name (Required)">
								<input type="hidden" id="postname" name="postname" value="causes">
                            </div>
                        	<div class="col-md-6">
                        		<input id="lastname" value="'.$this_lname.'" type="text" name="lname" class="form-control" placeholder="Last name">
                            </div>
                      	</div>
                    	<div class="row">
                        	<div class="col-md-6">
                        		<input type="text" value="'.$this_email.'" name="email" id="email" class="form-control" placeholder="Your email (Required)">
                            </div>
                        	<div class="col-md-6">
                        		<input id="phone" type="phone" name="phone" class="form-control" placeholder="Your phone">
                            </div>
                       	</div>
                    	<div class="row">
                        	<div class="col-md-6">
                        		<textarea id="address" rows="3" cols="5" class="form-control" placeholder="Your Address"></textarea>
                            </div>
                        	<div class="col-md-6">
                        		<textarea id="notes" rows="3" cols="5" class="form-control" placeholder="Additional Notes"></textarea>
                            </div>
                       	</div>';
	$output .= '<input type="hidden" name="rm" value="2">';
	$output .= '<input type="hidden" name="amount" value="">';	
	$output .= '<input type="hidden" name="cmd" value="_donations">';
	$output .= '<input type="hidden" name="business" value="'.$email.'">';
	$output .= '<input type="hidden" name="currency_code" value="'.$currency.'">';
	$output .= '<input type="hidden" name="item_name" value="'.stripslashes($description).'">';
	$output .= '<input type="hidden" name="item_number" value="'.$cause_id.'-'.$unique.'">';
	$output .= '<input type="hidden" name="return" value="'.get_permalink($cause_id).'" />';
        if(is_numeric($tax)){
            $output .= '<input type="hidden" name="tax" value="'.$tax.'" />';
        }
	if(!empty($cancel_url)){
		$output .= '<input type="hidden" name="cancel_return" value="'.$cancel_url.'" />';
	}
	if(!empty($country_code)){
		$output .= '<input type="hidden" name="lc" value="'.$country_code.'" />';
	}
	$output .= '<input id="donate-cause" type="submit" name="donate" class="btn btn-primary btn-lg btn-block" value="Donate Now">';
	$output .= '<div id="message"></div>';
	$output .= '</form>';
	
	$output .= '</div>';
	return $output;
} 

function events_shortcode($args)
{
	extract( shortcode_atts( array(
		'email' => get_option('paypal_email_address'),
		'event_id' => '',
		'description' => '',	
		'amount' => '',
		'currency' => get_option('paypal_currency_options'),
		'reference' => '',	
		'return' => '',
		'cancel_url' => '',
       'tax' => '',
	    'paypal_payment' => get_option('paypal_payment_option'),
	), $args));	

	$output = "";
	$unique = uniqid();
	if(empty($email)){
		$output = '<div id="message"><div class="alert alert-error">Error! Please enter your PayPal email address in payment options page.</div></div>';
		return $output;
	}
	if($amount!=0||$amount!='') {
	$paypal_payment = ($paypal_payment=="live")?"https://www.paypal.com/cgi-bin/webscr":"https://www.sandbox.paypal.com/cgi-bin/webscr"; }
	else {
	$paypal_payment = esc_url(add_query_arg(array('tx'=>'free','item_number'=>$event_id.'-'.$unique),$return)); }

        $window_target = '';
        if(!empty($new_window)){
            $window_target = 'target="_blank"';
        }
	$output .= '<div class="wp_paypal_button_widget_any_amt">';
	$output .= '<form id="event-'.$event_id.'" class="paypal-submit-form" name="_xclick" class="wp_accept_pp_button_form_any_amount" action="'.$paypal_payment.'" method="post" '.$window_target.'>';

	if(!empty($reference)){
		$output .= '<div class="wp_pp_button_reference_section">';
		$output .= '<label for="wp_pp_button_reference">'.$reference.'</label>';
		$output .= '<br />';
		$output .= '<input type="hidden" name="on0" value="Reference" />';
		$output .= '<input type="text" name="os0" value="" class="wp_pp_button_reference" />';
		$output .= '</div>';
	}
	$this_email = '';
	$this_fname = '';
	$this_lname = '';
	$this_username = '';
	$this_actualname = '';
	if(is_user_logged_in()) {
	global $current_user;
      get_currentuserinfo();
	  $this_email = $current_user->user_email;
	  $this_fname = $current_user->user_firstname;
	  $this_lname = $current_user->user_lastname;
	  $this_username = $current_user->display_name;
	  $this_actualname = ($this_fname=='')?$this_username:$this_fname; }
	
	$output .= '
                    	<div class="row">
                        	<div class="col-md-6">
                        		<input type="text" value="'.$this_actualname.'" id="username" name="fname" class="form-control" placeholder="First name (Required)">
								<input type="hidden" id="postname" name="postname" value="event">
                            </div>
                        	<div class="col-md-6">
                        		<input id="lastname" type="text" value="'.$this_lname.'" name="lname" class="form-control" placeholder="Last name">
								<input type="hidden" value="'.get_post_meta($event_id,'imic_event_registration_fee',true).'" id="reg-status">
									<input type="hidden" value="'.get_query_var('event_date').'" id="event-reg-date">
                            </div>
                      	</div>
                    	<div class="row">
                        	<div class="col-md-6">
                        		<input type="text" value="'.$this_email.'" name="email" id="email" class="form-control" placeholder="Your email (Required)">
                            </div>
                        	<div class="col-md-6">
                        		<input id="phone" type="phone" name="phone" class="form-control" placeholder="Your phone">
                            </div>
                       	</div>
                    	<div class="row">
                        	<div class="col-md-6">
                        		<textarea id="address" rows="3" cols="5" class="form-control" placeholder="Your Address"></textarea>
                            </div>
                        	<div class="col-md-6">
                        		<textarea id="notes" rows="3" cols="5" class="form-control" placeholder="Additional Notes"></textarea>
                            </div>
                       	</div>';
	$output .= '<input type="hidden" name="rm" value="2">';
	$output .= '<input type="hidden" name="amount" value="'.$amount.'">';	
	$output .= '<input type="hidden" name="cmd" value="_xclick">';
	$output .= '<input type="hidden" name="business" value="'.$email.'">';
	$output .= '<input type="hidden" name="currency_code" value="'.$currency.'">';
	$output .= '<input type="hidden" name="item_name" value="'.stripslashes($description).'">';
	$output .= '<input type="hidden" name="item_number" value="'.$event_id.'-'.$unique.'">';
	$output .= '<input type="hidden" name="return" value="'.$return.'" />';
        if(is_numeric($tax)){
            $output .= '<input type="hidden" name="tax" value="'.$tax.'" />';
        }
	if(!empty($cancel_url)){
		$output .= '<input type="hidden" name="cancel_return" value="'.$cancel_url.'" />';
	}
	if(!empty($country_code)){
		$output .= '<input type="hidden" name="lc" value="'.$country_code.'" />';
	}
	$button_value = ($amount==0||$amount=='')?'Register':'Pay For Event';
	$output .= '<input id="register-event" type="submit" name="donate" class="btn btn-primary btn-lg btn-block" value="'.$button_value.'">';
	$output .= '<div id="message"></div>';
	$output .= '</form>';
	
	$output .= '</div>';
	return $output;
} ?>