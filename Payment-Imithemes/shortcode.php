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
		$output = '<div id="message"><div class="alert alert-error">'.esc_html__('Error! Please enter your PayPal email address in causes options page.','framework').'</div></div>';
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
	$current_user = wp_get_current_user();
	  $this_email = $current_user->user_email;
	  $this_fname = $current_user->user_firstname;
	  $this_lname = $current_user->user_lastname;
	  $this_username = $current_user->display_name;
	  $this_actualname = ($this_fname=='')?$this_username:$this_fname; }
	$unique = uniqid();
	$output .= '<div class="row">
                        	<div class="col-md-6">
                                <label>'.esc_html__('How much would you like to donate?','framework').'</label>
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
                                <label>'.esc_html__('Enter custom donation amount','framework').'</label>
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
                        		<input type="text" value="'.$this_actualname.'" id="username" name="fname" class="form-control" placeholder="'.esc_html__('First name (Required)','framework').'">
								<input type="hidden" id="postname" name="postname" value="causes">
                            </div>
                        	<div class="col-md-6">
                        		<input id="lastname" value="'.$this_lname.'" type="text" name="lname" class="form-control" placeholder="'.esc_html__('Last name','framework').'">
                            </div>
                      	</div>
                    	<div class="row">
                        	<div class="col-md-6">
                        		<input type="text" value="'.$this_email.'" name="email" id="email" class="form-control" placeholder="'.esc_html__('Your email (Required)','framework').'">
                            </div>
                        	<div class="col-md-6">
                        		<input id="phone" type="phone" name="phone" class="form-control" placeholder="'.esc_html__('Your phone','framework').'">
                            </div>
                       	</div>
                    	<div class="row">
                        	<div class="col-md-6">
                        		<textarea id="address" rows="3" cols="5" class="form-control" placeholder="'.esc_html__('Your Address','framework').'"></textarea>
                            </div>
                        	<div class="col-md-6">
                        		<textarea id="notes" rows="3" cols="5" class="form-control" placeholder="'.esc_html__('Additional Notes','framework').'"></textarea>
                            </div>
						   </div>
						   <div class="row">
						   <div class="col-md-6">
							   <textarea id="zip" rows="3" cols="5" class="form-control" placeholder="'.esc_html__('Your Zip','framework').'"></textarea>
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
	$output .= '<input id="donate-cause" type="submit" name="donate" class="btn btn-primary btn-lg btn-block" value="'.esc_html__('Donate Now','framework').'">';
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
	$event_multiple_tickets = get_post_meta(get_the_ID(), 'nativechurch_ticket_status', true);
	$tickets_type1 = get_post_meta( get_the_ID(), 'nativechurch_event_type1', true );
	$nativechurch_event_ticket1 = get_post_meta( get_the_ID(), 'nativechurch_event_ticket1', true );
	
	$nativechurch_event_booked1 = get_post_meta( get_the_ID(), 'nativechurch_event_booked1', true );
	$nativechurch_event_amount1 = get_post_meta( get_the_ID(), 'nativechurch_event_amount1', true );
	$tickets_type2 = get_post_meta( get_the_ID(), 'nativechurch_event_type2', true );
	$nativechurch_event_ticket2 = get_post_meta( get_the_ID(), 'nativechurch_event_ticket2', true );
	
	$nativechurch_event_booked2 = get_post_meta( get_the_ID(), 'nativechurch_event_booked2', true );
	$nativechurch_event_amount2 = get_post_meta( get_the_ID(), 'nativechurch_event_amount2', true );
	$tickets_type3 = get_post_meta( get_the_ID(), 'nativechurch_event_type3', true );
	$nativechurch_event_ticket3 = get_post_meta( get_the_ID(), 'nativechurch_event_ticket3', true );
	
	$nativechurch_event_booked3 = get_post_meta( get_the_ID(), 'nativechurch_event_booked3', true );
	$nativechurch_event_amount3 = get_post_meta( get_the_ID(), 'nativechurch_event_amount3', true );
	if(empty($email)){
		$output = '<div id="message"><div class="alert alert-error">'.esc_html__('Error! Please enter your PayPal email address in payment options page.','framework').'</div></div>';
		return $output;
	}
	$paypal_url =  ($paypal_payment=="live")?"https://www.paypal.com/cgi-bin/webscr":"https://www.sandbox.paypal.com/cgi-bin/webscr";
	$normal_url = esc_url(add_query_arg(array('tx'=>'free','item_number'=>$event_id.'-'.$unique),$return));
	if(($amount!=0||$amount!='')&&($nativechurch_event_ticket1>=0||$nativechurch_event_ticket2>=0||$nativechurch_event_ticket3>=0)) {
	$paypal_payment = ($paypal_payment=="live")?"https://www.paypal.com/cgi-bin/webscr":"https://www.sandbox.paypal.com/cgi-bin/webscr";
	}
	else {
	$paypal_payment = esc_url(add_query_arg(array('tx'=>'free','item_number'=>$event_id.'-'.$unique),$return));
	}
	$nativechurch_event_ticket1 = ($nativechurch_event_ticket1<=0)?esc_html__('All Booked', 'framework'):$nativechurch_event_ticket1;
	$nativechurch_event_ticket2 = ($nativechurch_event_ticket2<=0)?esc_html__('All Booked', 'framework'):$nativechurch_event_ticket2;
	$nativechurch_event_ticket3 = ($nativechurch_event_ticket3<=0)?esc_html__('All Booked', 'framework'):$nativechurch_event_ticket3;
        $window_target = '';
        if(!empty($new_window)){
            $window_target = 'target="_blank"';
        }
	$output .= '<div class="wp_paypal_button_widget_any_amt">';
	$output .= '<form id="event-'.$event_id.'" class="paypal-submit-form" name="_xclick" class="wp_accept_pp_button_form_any_amount" action="'.$paypal_payment.'" method="post" '.$window_target.'>';
$output .= '<input type="hidden" value="'.$paypal_url.'" id="paypal-url">';
$output .= '<input type="hidden" value="'.$normal_url.'" id="normal-url">';
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
	$current_user = wp_get_current_user();
	  $this_email = $current_user->user_email;
	  $this_fname = $current_user->user_firstname;
	  $this_lname = $current_user->user_lastname;
	  $this_username = $current_user->display_name;
	  $this_actualname = ($this_fname=='')?$this_username:$this_fname; }
	
	$output .= '
                    	<div class="row">
                        	<div class="col-md-6">
                        		<input type="text" value="'.$this_actualname.'" id="username" name="fname" class="form-control" placeholder="'.esc_html__('First name (Required)'.'framework').'">
								<input type="hidden" id="postname" name="postname" value="event">
                            </div>
                        	<div class="col-md-6">
                        		<input id="lastname" type="text" value="'.$this_lname.'" name="lname" class="form-control" placeholder="'.esc_html__('Last name','framework').'">
								<input type="hidden" value="'.get_post_meta($event_id,'imic_event_registration_fee',true).'" id="reg-status">
									<input type="hidden" value="'.get_query_var('event_date').'" id="event-reg-date">
                            </div>
                      	</div>
                    	<div class="row">
                        	<div class="col-md-6">
                        		<input type="text" value="'.$this_email.'" name="email" id="email" class="form-control" placeholder="'.esc_html__('Your email (Required)','framework').'">
                            </div>
                        	<div class="col-md-6">
                        		<input id="phone" type="phone" name="phone" class="form-control" placeholder="'.esc_html__('Your phone','framework').'">
                            </div>
                       	</div>
                    	<div class="row">
                        	<div class="col-md-6">
                        		<textarea id="address" rows="3" cols="5" class="form-control" placeholder="'.esc_html__('Your Address','framework').'"></textarea>
                            </div>
                        	<div class="col-md-6">
                        		<textarea id="notes" rows="3" cols="5" class="form-control" placeholder="'.esc_html__('Additional Notes','framework').'"></textarea>
                            </div>
						   </div>
						   <div class="row">
						   <div class="col-md-6">
							   <textarea id="zip" rows="3" cols="5" class="form-control" placeholder="'.esc_html__('Your Zip','framework').'"></textarea>
						   </div>
						  </div>';
						if($event_multiple_tickets==1)
						{
                        $output .= '<table width="100%" class="table-tickets">';
						$output .= '<tr class="head-table-tickets">';
							$output .= '<td>'.esc_html__('Type', 'framework').'</td>';
							$output .= '<td>'.esc_html__('Available ', 'framework').'</td>';
							$output .= '<td>'.esc_html__('Price', 'framework').'</td>';
							$output .= '<td>'.esc_html__('Quantity', 'framework').'</td>';
							$output .= '<td class="tickets-total-cost">'.esc_html__('Total', 'framework').' '.imic_get_currency_symbol(get_option('paypal_currency_options')).'<span>0</span></td>';
						$output .= '</tr>';
						if($tickets_type1!='')
						{
								$output .= '<tr>';
								$output .= '<td class="ticket-name">'.$tickets_type1.'</td>';
								$output .= '<td class="ticket-available">'.$nativechurch_event_ticket1.'</td>';
								if($nativechurch_event_amount1>=1)
								{
									$output .= '<td class="ticket-price">'.imic_get_currency_symbol(get_option('paypal_currency_options')).$nativechurch_event_amount1.'</td>';
								}
								else
								{
									$output .= '<td class="ticket-price">'.$nativechurch_event_amount1.'</td>';
								}
								$output .= '<td>';
								if($nativechurch_event_ticket1>=10)
								{
									$output .= '<select name="gold-ticket" id="platinum-ticket" class="premium-tickets">';
									for($i=0; $i<=10; $i++)
									{
										$output .= '<option value="'.$i.'">'.$i.'</option>';
									}
									$output .= '</select>';
								}
								elseif($nativechurch_event_ticket1>0)
								{
									$output .= '<select name="gold-ticket" id="platinum-ticket" class="premium-tickets">';
									for($i=0; $i<=$nativechurch_event_ticket1; $i++)
									{
										$output .= '<option value="'.$i.'">'.$i.'</option>';
									}
									$output .= '</select>';
								}
								else
								{
									$output .= $nativechurch_event_ticket1;
								}
								$output .= '</td>';
								$output .= '<td class="ticket-cost-calculated"></td>';
								$output .= '</tr>';
						}
						if($tickets_type2!='')
						{
								$output .= '<tr>';
								$output .= '<td class="ticket-name">'.$tickets_type2.'</td>';
								$output .= '<td class="ticket-available">'.$nativechurch_event_ticket2.'</td>';
								if($nativechurch_event_amount2>=1)
								{
									$output .= '<td class="ticket-price">'.imic_get_currency_symbol(get_option('paypal_currency_options')).$nativechurch_event_amount2.'</td>';
								}
								else
								{
									$output .= '<td class="ticket-price">'.$nativechurch_event_amount2.'</td>';
								}
								$output .= '<td>';
								if($nativechurch_event_ticket2>=10)
								{
									$output .= '<select name="gold-ticket" id="gold-ticket" class="premium-tickets">';
									for($i=0; $i<=10; $i++)
									{
										$output .= '<option value="'.$i.'">'.$i.'</option>';
									}
									$output .= '</select>';
								}
								elseif($nativechurch_event_ticket2>0)
								{
									$output .= '<select name="gold-ticket" id="gold-ticket" class="premium-tickets">';
									for($i=0; $i<=$nativechurch_event_ticket2; $i++)
									{
										$output .= '<option value="'.$i.'">'.$i.'</option>';
									}
									$output .= '</select>';
								}
								else
								{
									$output .= $nativechurch_event_ticket2;
								}
								$output .= '</td>';
								$output .= '<td class="ticket-cost-calculated"></td>';
								$output .= '</tr>';
						}
						if($tickets_type3!='')
						{
								$output .= '<tr>';
								$output .= '<td class="ticket-name">'.$tickets_type3.'</td>';
								$output .= '<td class="ticket-available">'.$nativechurch_event_ticket3.'</td>';
								if($nativechurch_event_amount3>=1)
								{
									$output .= '<td class="ticket-price">'.imic_get_currency_symbol(get_option('paypal_currency_options')).$nativechurch_event_amount3.'</td>';
								}
								else
								{
									$output .= '<td class="ticket-price">'.$nativechurch_event_amount3.'</td>';
								}
								$output .= '<td>';
								if($nativechurch_event_ticket3>=10)
								{
									$output .= '<select name="gold-ticket" id="silver-ticket" class="premium-tickets">';
									for($i=0; $i<=10; $i++)
									{
										$output .= '<option value="'.$i.'">'.$i.'</option>';
									}
									$output .= '</select>';
								}
								elseif($nativechurch_event_ticket3>0)
								{
									$output .= '<select name="gold-ticket" id="silver-ticket" class="premium-tickets">';
									for($i=0; $i<=$nativechurch_event_ticket3; $i++)
									{
										$output .= '<option value="'.$i.'">'.$i.'</option>';
									}
									$output .= '</select>';
								}
								else
								{
									$output .= $nativechurch_event_ticket3;
								}
								$output .= '</td>';
								$output .= '<td class="ticket-cost-calculated"></td>';
								$output .= '</tr>';
						}
						}
						$output .= '</table>';
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
	$button_value = (($amount==0||$amount=='')&&($nativechurch_event_ticket1<=0&&$nativechurch_event_ticket2<=0&&$nativechurch_event_ticket3<=0))?esc_html__('Register','framework'):esc_html__('Pay For Event','framework');
	$disable_button = ((get_post_meta( get_the_ID(), 'nativechurch_event_ticket1', true )>0||get_post_meta( get_the_ID(), 'nativechurch_event_ticket2', true )>0||get_post_meta( get_the_ID(), 'nativechurch_event_ticket3', true )>0)||($event_multiple_tickets==0))?'':'disabled';
	$output .= '<input id="register-event" type="submit" name="donate" class="btn btn-primary btn-lg btn-block" value="'.$button_value.'" '.$disable_button.'>';
	$output .= '<div id="message"></div>';
	$output .= '</form>';
	
	$output .= '</div>';
	return $output;
} ?>