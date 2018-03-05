<?php get_header();
while(have_posts()):the_post();
$transaction_id=isset($_REQUEST['tx'])?$_REQUEST['tx']:'';
if($transaction_id!='') {
	global $wpdb;
	$table_name = $wpdb->prefix . "imic_payment_transaction";
	$payment_array=imic_validate_payment($transaction_id);
	$st = $payment_array['payment_status'];
	$user_id=isset($_REQUEST['item_number'])?$_REQUEST['item_number']:'';
	$cause_id=strstr($user_id, '-', true);
	$cause_name=get_the_title($cause_id);
	if(!empty($transaction_id)&&!empty($st)){
		$sql_select="select transaction_id from $table_name WHERE `transaction_id` = '$transaction_id'";
		$data =$wpdb->get_results($sql_select,ARRAY_A)or print mysql_error();
		if(empty($data)){
			$amt=isset($_REQUEST['amt'])?$_REQUEST['amt']:'';
			$received_amount = get_post_meta($cause_id,'imic_cause_amount_received',true);
			$updated_amount = $received_amount+$amt;
			if($st=='Completed') {
			update_post_meta($cause_id,'imic_cause_amount_received',$updated_amount); }
			$sql = "UPDATE $table_name SET transaction_id='$transaction_id',status='$st' WHERE cause_id='$user_id'";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}else{}
	}
}
$pageOptions = imic_page_design(); //page design options ?>
<!-- Start Content -->
        <div class="container">
            <div class="row">
            	<?php if(isset($_REQUEST['tx'])){ ?>
                <div class="col-md-12 appear-animation bounceInRight appear-animation-visible" data-appear-animation="bounce-in-up">
                    <div class="alert alert-info fade in">
                        <?php _e('Thanks for your contribution','framework'); ?>
                    </div>
                </div>
                <?php } ?>
                <div class="<?php echo $pageOptions['class']; ?>">
                    <header class="single-post-header clearfix">
                      <h2 class="post-title"><?php echo get_the_title(); ?></h2>
                    </header>
                    <article class="post-content cause-item">
                        <span class="post-meta meta-data">
                          	<span><i class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format')); ?></span>
                          	<?php 
                            //Display all cause categories 			
                            echo get_the_term_list(get_the_ID(), 'causes-category', '<span><i class="fa fa-archive"></i>', ', ', '</span>' ); 
                            //Display cause's total Comments
                            comments_popup_link('<span><i class="fa fa-comment"></i>'.esc_html__('No comments yet','framework').'</span>', '<span><i class="fa fa-comment"></i>1</span>', '<span><i class="fa fa-comment"></i>%</span>', 'comments-link',esc_html__('Comments are off for this post','framework')); 
                            ?>
                        </span>
                        <div class="spacer-20"></div>
                        <div class="row">
                            <div class="col-md-7">
                                <?php //Cause Details
								the_post_thumbnail('full',array('class'=>'img-responsive'));
                              	 ?>
                            </div>
                            <div class="col-md-5">
                                <ul class="list-group">
									<?php
                                    $cause_amount = get_post_meta(get_the_ID(),'imic_cause_amount',true); 
									$cause_received_amount = get_post_meta(get_the_ID(),'imic_cause_amount_received',true); 
									if(!empty($cause_amount)) {
										$cause_percentage = ($cause_received_amount/$cause_amount)*100;
										$cause_percentage = round($cause_percentage); 
										if($cause_percentage<=30) { 
											$class = 'progress-bar-danger'; 
										} elseif(($cause_percentage<=70)&&($cause_percentage>30)) { 
											$class = 'progress-bar-warning'; 
										} else { 
											$class = 'progress-bar-success'; 
										}
                                    ?>	
                                    <li class="list-group-item">
                                        <h4><?php echo esc_html__('Cause Progress','framework'); ?></h4>
                                        <div class="progress">
                                          <div class="progress-bar <?php echo $class; ?>" data-appear-progress-animation="<?php echo $cause_percentage; ?>%" data-appear-animation-delay="200"></div>
                                        </div>
                                    </li>
                                  	<li class="list-group-item"> <span class="badge"><?php echo imic_get_currency_symbol(get_option('paypal_currency_options')). $cause_amount; ?></span> <?php echo esc_html__('Amount Needed','framework'); ?> </li>
                                  	<?php } 
									if(!empty($cause_received_amount)){
									?>
                                  	<li class="list-group-item"> <span class="badge"><?php echo imic_get_currency_symbol(get_option('paypal_currency_options')). $cause_received_amount; ?></span> <?php echo esc_html__('Collected yet','framework'); ?> </li>
                                    <?php }
									if(!empty($cause_received_amount)&&!empty($cause_amount)){ ?>
                                  	<li class="list-group-item"> <span class="badge accent-bg"><?php echo $cause_percentage .'%'; ?></span> <?php echo esc_html__('Percentile','framework'); ?> </li>
                                    <?php }
									//Cause Donation Days left
								  	$now = date('Y-m-d 23:59:59'); // or your date as well
								  	$now = strtotime($now);
								  	$cause_end_date = get_post_meta(get_the_ID(),'imic_cause_end_dt',true);
								  	$cause_end_date = $cause_end_date.' 23:59:59';
								  	$your_date = strtotime($cause_end_date);
								  	$datediff = $your_date - $now;
								  	$days_left = floor($datediff/(60*60*24)); 
								  	$cause_date_msg = '';
								  	if($days_left==0) { $cause_date_msg = '1'; } elseif($days_left<0) { $cause_date_msg = '0'; } else { $cause_date_msg = $days_left+'1'.''; } if($days_left>=0) { ?>	
                                  	<li class="list-group-item"> <span class="badge"><?php echo $cause_date_msg; ?></span> <?php echo esc_html__('Days left to achieve target','framework'); ?></li><?php } else { ?>
                                    <li class="list-group-item"><?php echo esc_html__('This cause has been completed.','framework'); ?></li><?php } ?>
                                </ul><?php if($days_left>=0) { ?>
                                <a href="#" id="donate-popup" class="btn btn-primary btn-lg btn-block donate-paypal" data-toggle="modal" data-target="#PaymentModal"><?php echo esc_html__('Donate Now','framework'); ?></a><?php } ?>
                            </div>
                        </div>
                        <div class="spacer-30"></div>
                        <h3 style="display:none;"><?php echo get_the_title(); ?></h3>
                        <?php the_content(); ?>
                        <?php comments_template('', true); ?> 
                    </article>
                    <!-- Payment Modal Window -->
                    <div class="modal fade" id="PaymentModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="PaymentModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                            	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            	<h4 class="modal-title" id="myModalLabel"><?php echo esc_html__('Donate to: ','framework'); ?><span class="accent-color payment-to-cause"><?php echo get_the_title(); ?></span></h4>
                            </div>
                            <div class="modal-body">
                            	<?php echo do_shortcode('[imic_causes cause_id="'.get_the_ID().'" description="'.get_the_title().'"]'); ?>
                            </div>
                            <div class="modal-footer">
                            	<p class="small short"><?php echo (get_option('donation_form_info')!='')?get_option('donation_form_info'):esc_html__('If you would prefer to call in your donation, please call 1800.785.876','framework'); ?></p>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                
                <?php if(!empty($pageOptions['sidebar'])){ ?>
                <!-- Start Sidebar -->
                <div class="col-md-3 sidebar">
                    <?php dynamic_sidebar($pageOptions['sidebar']); ?>
                </div>
                <!-- End Sidebar -->
                <?php } ?>
    </div>
</div>
<?php endwhile;
get_footer(); ?>