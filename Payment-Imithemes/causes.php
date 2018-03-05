<?php
/*
Plugin Name: Payment Imithemes
Version: 1.4
Author: IMITHEMES
Author URI: http://www.imithemes.com
Description: This plugin adds causes functionality in native church theme along with payment options for paid events.
License: This plugin is bundled with Native Church Theme and should be use with Native Church Theme only.
Text Domain: framework
Domain Path: /language
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
		$causesOption =	add_submenu_page( 'themes.php',esc_html__('Payment Options','framework'), esc_html__('Payment Options','framework'),'manage_options', 'causes_options', 'causes_options',7 );
		//add_action('load-'.$causesOption, 'causes_option_help_tab');
		add_action( 'admin_init', 'imic_register_settings' );
}
if(!function_exists('causes_option_help_tab')){
	function causes_option_help_tab(){
		$screen = get_current_screen();
		$screen->add_help_tab( array(
			'id'	=> 'auto_return',
			'title'	=> esc_html__('Enable Auto Return','framework'),
			'content'	=> '<p>' . esc_html__( 'Here are the steps to enable Auto Return in your account.','framework').'</p><p>'.
										esc_html__('Log into https://developer.paypal.com','framework').'</p><p>'.
										esc_html__('Click Applications','framework').'</p><p>'.
										esc_html__('Click accounts','framework').'</p><p>'.
										esc_html__('Expand the account in question','framework').'</p><p>'.
										esc_html__('Click Sandbox site','framework').'</p><p>'.
										esc_html__('Login to the test account','framework').'</p><p>'.
										esc_html__('Copy and paste "https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-website-payments" into your browser
										Enable Auto Return and click Save','framework').'</p><p>'.
										esc_html__('Enter the Auto Return URL and click Save','framework').'</p>',
		));
		$screen->add_help_tab( array(
			'id'	=> 'token_id',
			'title'	=> esc_html__('Token ID','framework'),
			'content'	=> '<p>' . esc_html__( 'Here are the steps to enable Auto Return in your account.','framework').'</p>',
		));
		$screen->add_help_tab( array(
			'id'	=> 'template_id',
			'title'	=> esc_html__('Templates','framework'),
			'content'	=> '<p>' . esc_html__( 'To get template ID, Follow Steps.','framework').'</p><p>'.
										esc_html__('Create a page with selecting content with sidebar','framework').'</p><p>'.
										esc_html__('When you publish that page you will get id in dashboard url','framework').'</p><p>'.
										esc_html__('Copy that ID and paste it here','framework').'</p><p>'.
										esc_html__('You can follow this step for both templates(Causes List and Causes Grid)','framework').'</p>',
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
	<h2><?php echo esc_html__('Causes Options','framework'); ?></h2>
    <form method="post" action="options.php">
        <?php settings_fields( 'causes-options-group' ); ?>
        <?php do_settings_sections( 'causes-options-group' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Paypal Email Address:','framework'); ?></th>
                <td><input type="text" name="paypal_email_address" value="<?php echo get_option('paypal_email_address'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Paypal Token ID','framework'); ?></th>
                <td><input type="text" name="paypal_token_id" value="<?php echo get_option('paypal_token_id'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Paypal Currency Options','framework'); ?></th>
                <td>
                    <select id="paypal_currency_options" name="paypal_currency_options">
                        <?php 
                            _e('<option value="USD"'); echo (get_option('paypal_currency_options')=="USD")?'selected':'';  _e('>US Dollar</option>');
                            _e('<option value="AUD"'); echo (get_option('paypal_currency_options')=="AUD")?'selected':'';  _e('>Australian Dollar</option>');
                            _e('<option value="BRL"'); echo (get_option('paypal_currency_options')=="BRL")?'selected':'';  _e('>Brazilian Real</option>');
                            _e('<option value="CAD"'); echo (get_option('paypal_currency_options')=="CAD")?'selected':'';  _e('>Canadian Dollar</option>');
                            _e('<option value="CZK"'); echo (get_option('paypal_currency_options')=="CZK")?'selected':'';  _e('>Czech Koruna</option>');
                            _e('<option value="DKK"'); echo (get_option('paypal_currency_options')=="DKK")?'selected':'';  _e('>Danish Krone</option>');
                            _e('<option value="EUR"'); echo (get_option('paypal_currency_options')=="EUR")?'selected':'';  _e('>Euro</option>');
                            _e('<option value="HKD"'); echo (get_option('paypal_currency_options')=="HKD")?'selected':'';  _e('>Hong Kong Dollar</option>');
                            _e('<option value="HUF"'); echo (get_option('paypal_currency_options')=="HUF")?'selected':'';  _e('>Hungarian Forint</option>');
                            _e('<option value="ILS"'); echo (get_option('paypal_currency_options')=="ILS")?'selected':'';  _e('>Israeli New Sheqel</option>');
                            _e('<option value="JPY"'); echo (get_option('paypal_currency_options')=="JPY")?'selected':'';  _e('>Japanese Yen</option>');
                            _e('<option value="MYR"'); echo (get_option('paypal_currency_options')=="MYR")?'selected':'';  _e('>Malaysian Ringgit</option>');
                            _e('<option value="MXN"'); echo (get_option('paypal_currency_options')=="MXN")?'selected':'';  _e('>Mexican Peso</option>');
                            _e('<option value="NOK"'); echo (get_option('paypal_currency_options')=="NOK")?'selected':'';  _e('>Norwegian Krone</option>');
                            _e('<option value="NZD"'); echo (get_option('paypal_currency_options')=="NZD")?'selected':'';  _e('>New Zealand Dollar</option>');
                            _e('<option value="PHP"'); echo (get_option('paypal_currency_options')=="PHP")?'selected':'';  _e('>Philippine Peso</option>');
                            _e('<option value="PLN"'); echo (get_option('paypal_currency_options')=="PLN")?'selected':'';  _e('>Polish Zloty</option>');
                            _e('<option value="GBP"'); echo (get_option('paypal_currency_options')=="GBP")?'selected':'';  _e('>Pound Sterling</option>'); 
                            _e('<option value="SGD"'); echo (get_option('paypal_currency_options')=="SGD")?'selected':'';  _e('>Singapore Dollar</option>');
                            _e('<option value="SEK"'); echo (get_option('paypal_currency_options')=="SEK")?'selected':'';  _e('>Swedish Krona</option>');
                            _e('<option value="CHF"'); echo (get_option('paypal_currency_options')=="CHF")?'selected':'';  _e('>Swiss Franc</option>');
                            _e('<option value="TWD"'); echo (get_option('paypal_currency_options')=="TWD")?'selected':'';  _e('>Taiwan New Dollar</option>');
                            _e('<option value="THB"'); echo (get_option('paypal_currency_options')=="THB")?'selected':'';  _e('>Thai Baht</option>');
                            _e('<option value="TRY"'); echo (get_option('paypal_currency_options')=="TRY")?'selected':'';  _e('>Turkish Lira</option>');
                        ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Paypal Payment Site','framework'); ?></th>
                <td>
                    <select id="paypal_payment_option" name="paypal_payment_option">
                        <?php 
                        _e('<option value="live"'); echo (get_option('paypal_payment_option')=="live")?'selected':'';  _e('>Live</option>');
                        _e('<option value="sandbox"'); echo (get_option('paypal_payment_option')=="sandbox")?'selected':'';  _e('>Sandbox</option>'); ?>
                    </select>
               </td>
            </tr>
             <!--<tr valign="top">
                <th scope="row"><?php echo esc_html__('Causes Listing Template ID:','framework'); ?></th>
                <td><input type="text" name="causes_list_id" value="<?php echo get_option('causes_list_id'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Causes Grid Template ID:','framework'); ?></th>
                <td><input type="text" name="causes_grid_id" value="<?php echo get_option('causes_grid_id'); ?>" /></td>
            </tr>-->
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Donation Form Info:','framework'); ?></th>
                <td><input type="text" name="donation_form_info" value="<?php echo get_option('donation_form_info'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo esc_html__('Registration Form Info:','framework'); ?></th>
                <td><input type="text" name="registration_form_info" value="<?php echo get_option('registration_form_info'); ?>" /></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
<?php } ?>