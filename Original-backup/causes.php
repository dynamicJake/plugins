<?php
/*
Plugin Name: Payment Imithemes
Version: 1.2.0
Author: IMITHEMES
Author URI: http://www.imithemes.com
Description: This plugin adds causes functionality in native church theme.
License: This plugin is bundled with Native Church Theme and should be use with Native Church Theme only.
*/
if ( ! defined( 'IMI_CAUSES_BASE_FILE' ) )
    define( 'IMI_CAUSES_BASE_FILE', __FILE__ );
if ( ! defined( 'IMI_CAUSES_BASE_DIR' ) )
    define( 'IMI_CAUSES_BASE_DIR', dirname( IMI_CAUSES_BASE_FILE ) );
if ( ! defined( 'IMI_CAUSES_PLUGIN' ) )
    define( 'IMI_CAUSES_PLUGIN', plugin_dir_url( __FILE__ ) );
include_once('shortcode.php');
include_once('cause_functions.php');
require_once('causes-type.php');
include_once('events-payment.php');
include_once('causes-payment.php');

add_shortcode('imic_causes', 'causes_donate_now');
function causes_donate_now($args)
{
	$output = causes_shortcode($args);
	return $output;
}
add_shortcode('imic_events', 'events_register_now');
function events_register_now($args)
{
	$output = events_shortcode($args);
	return $output;
}
add_action('admin_menu', 'causes_option_page');
function causes_option_page() {
		global $causesOption;
		$causesOption =	add_submenu_page( 'themes.php',__('Payment Options','framework'), __('Payment Options','framework'),'manage_options', 'causes_options', 'causes_options',7 );
		//add_action('load-'.$causesOption, 'causes_option_help_tab');
		add_action( 'admin_init', 'imic_register_settings' );
}
if(!function_exists('causes_option_help_tab')){
	function causes_option_help_tab(){
		$screen = get_current_screen();
		$screen->add_help_tab( array(
			'id'	=> 'auto_return',
			'title'	=> __('Enable Auto Return'),
			'content'	=> '<p>' . __( 'Here are the steps to enable Auto Return in your account.','framework').'</p><p>'.
										__('Log into https://developer.paypal.com','framework').'</p><p>'
										.__('Click Applications','framework').'</p><p>'.
										__('Click accounts','framework').'</p><p>'.
										__('Expand the account in question','framework').'</p><p>'.
										__('Click Sandbox site','framework').'</p><p>'.
										__('Login to the test account','framework').'</p><p>'.
										__('Copy and paste "https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-website-payments" into your browser
										Enable Auto Return and click Save','framework').'</p><p>'.
										__('Enter the Auto Return URL and click Save','framework').'</p>',
		));
		$screen->add_help_tab( array(
			'id'	=> 'token_id',
			'title'	=> __('Token ID'),
			'content'	=> '<p>' . __( 'Here are the steps to enable Auto Return in your account.','framework').'</p><p>',
		));
		$screen->add_help_tab( array(
			'id'	=> 'template_id',
			'title'	=> __('Templates'),
			'content'	=> '<p>' . __( 'To get template ID, Follow Steps.','framework').'</p><p>'.
										__('Create a page with selecting content with sidebar','framework').'</p><p>'.
										__('When you publish that page you will get id in dashboard url','framework').'</p><p>'.
										__('Copy that ID and paste it here','framework').'</p><p>'.
										__('You can follow this step for both templates(Causes List and Causes Grid)','framework').'</p><p>',
		));
	}
}

function imic_register_settings() {
	//register our settings
	register_setting( 'causes-options-group', 'paypal_email_address' );
	register_setting( 'causes-options-group', 'paypal_token_id' );
	register_setting( 'causes-options-group', 'paypal_currency_options' );
	register_setting( 'causes-options-group', 'paypal_payment_option' );
	register_setting( 'causes-options-group', 'causes_list_id' );
	register_setting( 'causes-options-group', 'causes_grid_id' );
	register_setting( 'causes-options-group', 'donation_form_info' );
	register_setting( 'causes-options-group', 'registration_form_info' );
}
function causes_options() { ?>
<div class="wrap">
	<h2><?php _e('Causes Options','causes-framework'); ?></h2>
    <form method="post" action="options.php">
        <?php settings_fields( 'causes-options-group' ); ?>
        <?php do_settings_sections( 'causes-options-group' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Paypal Email Address:','causes-framework'); ?></th>
                <td><input type="text" name="paypal_email_address" value="<?php echo get_option('paypal_email_address'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Paypal Token ID','causes-framework'); ?></th>
                <td><input type="text" name="paypal_token_id" value="<?php echo get_option('paypal_token_id'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Paypal Currency Options','causes-framework'); ?></th>
                <td>
                    <select id="paypal_currency_options" name="paypal_currency_options">
                        <?php 
                            _e('<option value="USD"'); echo (get_option('paypal_currency_options')=="USD")?'selected':'';  _e('>US Dollar</option>');
                            _e('<option value="GBP"'); echo (get_option('paypal_currency_options')=="GBP")?'selected':'';  _e('>Pound Sterling</option>'); 
                            _e('<option value="EUR"'); echo (get_option('paypal_currency_options')=="EUR")?'selected':'';  _e('>Euro</option>');
                            _e('<option value="AUD"'); echo (get_option('paypal_currency_options')=="AUD")?'selected':'';  _e('>Australian Dollar</option>');
                            _e('<option value="CAD"'); echo (get_option('paypal_currency_options')=="CAD")?'selected':'';  _e('>Canadian Dollar</option>');
                            _e('<option value="NZD"'); echo (get_option('paypal_currency_options')=="NZD")?'selected':'';  _e('>New Zealand Dollar</option>');
                            _e('<option value="HKD"'); echo (get_option('paypal_currency_options')=="HKD")?'selected':'';  _e('>Hong Kong Dollar</option>');
                        ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Paypal Payment Site','causes-framework'); ?></th>
                <td>
                    <select id="paypal_payment_option" name="paypal_payment_option">
                        <?php 
                        _e('<option value="live"'); echo (get_option('paypal_payment_option')=="live")?'selected':'';  _e('>Live</option>');
                        _e('<option value="sandbox"'); echo (get_option('paypal_payment_option')=="sandbox")?'selected':'';  _e('>Sandbox</option>'); ?>
                    </select>
               </td>
            </tr>
             <!--<tr valign="top">
                <th scope="row"><?php _e('Causes Listing Template ID:','causes-framework'); ?></th>
                <td><input type="text" name="causes_list_id" value="<?php echo get_option('causes_list_id'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Causes Grid Template ID:','causes-framework'); ?></th>
                <td><input type="text" name="causes_grid_id" value="<?php echo get_option('causes_grid_id'); ?>" /></td>
            </tr>-->
            <tr valign="top">
                <th scope="row"><?php _e('Donation Form Info:','causes-framework'); ?></th>
                <td><input type="text" name="donation_form_info" value="<?php echo get_option('donation_form_info'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Registration Form Info:','causes-framework'); ?></th>
                <td><input type="text" name="registration_form_info" value="<?php echo get_option('registration_form_info'); ?>" /></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
<?php } ?>