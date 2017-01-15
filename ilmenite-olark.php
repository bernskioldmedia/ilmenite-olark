<?php
/*
 *	Plugin Name: 	Ilmenite Olark Integration
 *	Plugin URI: 	https://github.com/bernskioldmedia/Ilmenite-Olark
 *	Description: 	Integrates live chat application Olark with your WordPress site with full localization support.
 *	Author: 		Bernskiold Media
 *	Version: 		1.0.2
 *	Author URI: 	http://www.bernskioldmedia.com/
 *	Text Domain: 	ilmenite-olark
 *	Domain Path: 	/languages
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

class Ilmenite_Olark {

	/**
	 * The Plugin Path
	 * @var string
	 */
	public $plugin_path;

	/**
	 * The Plugin URL
	 * @var string
	 */
	public $plugin_url;

	/**
	 * The Plugin Version
	 * @var string
	 */
	public $plugin_version;

	/**
	* @var The single instance of the class
	*/
	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {

		// Set the plugin path
		$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );

		// Set the plugin URL
		$this->plugin_url = untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );

		// Set the plugin version
		$this->plugin_version = '1.0.2';

		// Add Translation Loading
		add_action( 'plugins_loaded', array( $this, 'add_textdomain' ) );

		// Add the Olark Base Code
		add_action( 'wp_footer', array( $this, 'olark_code' ) );

		// Add the Olark Localization Code
		add_action( 'wp_footer', array( $this, 'olark_localization' ) );

		// Include Settings
		include( 'admin/ilmenite-olark-options.php' );
		new Ilmenite_Olark_Options();

	}

	/**
	 * Load the Translations
	 */
	public function add_textdomain() {
		load_plugin_textdomain( 'ilmenite-olark', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Get Plugin Options
	 *
	 * Wrapper function to get options from the namespaced array,
	 * to reduce work with get_option().
	 *
	 * @param  string $option  The Option Key
	 * @param  String $default The Default Value
	 * @return string          The Option Value
	 */
	public function get_plugin_options( $option, $default = '' ) {

		if ( ! $option ) {
			return false;
		}

		$options_array = get_option( 'ilolark_settings', array() );

		if ( empty( $options_array ) ) {
			return false;
		}

		$requested_option = $options_array[ $option ];

		// If we have an option, return it,
		// otherwise return the default.
		if ( $requested_option ) {
			return $requested_option;
		} else {
			return $default;
		}

	}

	/**
	 * Add the Olark Code
	 */
	public function olark_code() {

		// Get activation status
		$activated = $this->get_plugin_options( 'ilolark_activated' );

		// Get the Account ID from the options
		$account_id = $this->get_plugin_options( 'ilolark_siteid' );

		// If Olark box isn't activated, then don't run any further.
		if ( '1' != $activated ) {
			return;
		}

		// If no account ID is set, we don't output anything.
		if ( ! $account_id ) {
			return;
		}

		ob_start(); ?>

		<!-- begin olark code -->
		<script data-cfasync="false" type='text/javascript'>/*<![CDATA[*/window.olark||(function(c){var f=window,d=document,l=f.location.protocol=="https:"?"https:":"http:",z=c.name,r="load";var nt=function(){
		f[z]=function(){
		(a.s=a.s||[]).push(arguments)};var a=f[z]._={
		},q=c.methods.length;while(q--){(function(n){f[z][n]=function(){
		f[z]("call",n,arguments)}})(c.methods[q])}a.l=c.loader;a.i=nt;a.p={
		0:+new Date};a.P=function(u){
		a.p[u]=new Date-a.p[0]};function s(){
		a.P(r);f[z](r)}f.addEventListener?f.addEventListener(r,s,false):f.attachEvent("on"+r,s);var ld=function(){function p(hd){
		hd="head";return["<",hd,"></",hd,"><",i,' onl' + 'oad="var d=',g,";d.getElementsByTagName('head')[0].",j,"(d.",h,"('script')).",k,"='",l,"//",a.l,"'",'"',"></",i,">"].join("")}var i="body",m=d[i];if(!m){
		return setTimeout(ld,100)}a.P(1);var j="appendChild",h="createElement",k="src",n=d[h]("div"),v=n[j](d[h](z)),b=d[h]("iframe"),g="document",e="domain",o;n.style.display="none";m.insertBefore(n,m.firstChild).id=z;b.frameBorder="0";b.id=z+"-loader";if(/MSIE[ ]+6/.test(navigator.userAgent)){
		b.src="javascript:false"}b.allowTransparency="true";v[j](b);try{
		b.contentWindow[g].open()}catch(w){
		c[e]=d[e];o="javascript:var d="+g+".open();d.domain='"+d.domain+"';";b[k]=o+"void(0);"}try{
		var t=b.contentWindow[g];t.write(p());t.close()}catch(x){
		b[k]=o+'d.write("'+p().replace(/"/g,String.fromCharCode(92)+'"')+'");d.close();'}a.P(2)};ld()};nt()})({
		loader: "static.olark.com/jsclient/loader0.js",name:"olark",methods:["configure","extend","declare","identify"]});
		/* custom configuration goes here (www.olark.com/documentation) */
		olark.identify('<?php echo $account_id; ?>');/*]]>*/</script><noscript><a href="https://www.olark.com/site/<?php echo $account_id; ?>/contact" title="<?php _e( 'Contact us', 'ilmenite-olark' ); ?>" target="_blank"><?php _e( 'Questions? Feedback?', 'ilmenite-olark' ); ?></a> <?php _e( 'powered by <a href="http://www.olark.com?welcome" title="Olark live chat software">Olark live chat software</a>', 'ilmenite-olark' ); ?></noscript>
		<!-- end olark code -->

		<?php
		echo ob_get_clean();

	}

	/**
	 * Add Olark Localization Code
	 */
	public function olark_localization() {

		// Get Localization Status
		$localize = $this->get_plugin_options( 'ilolark_localize' );

		// If we haven't set to localize the script, then don't output the localization code
		if ( '1' != $localize ) {
			return;
		}

		ob_start(); ?>

		<script type='text/javascript'>
		   olark.configure('locale.welcome_title', "<?php _e( 'Help? Click to chat!', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.chatting_title', "<?php _e( 'Chatting With Our Agent', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.unavailable_title', "<?php _e( 'Help? Send a message!', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.busy_title', "<?php _e( 'Help? Send a message!', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.away_message', "<?php _e( 'Do you need help? Leave a message and we will get back to you as soon as we can.', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.loading_title', "<?php _e( 'Loading...', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.welcome_message', "<?php _e( 'Welcome! You can use this window to get in touch with one of our representatives right away.', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.busy_message', "<?php _e( 'All of our representatives are with other customers at this time. We will be with you shortly.', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.chat_input_text', "<?php _e( 'Type here and hit enter to chat', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.name_input_text', " <?php _e( 'and type your Name', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.email_input_text', " <?php _e( 'and type your Email', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.offline_note_message', "<?php _e( 'Do you need help? Leave a message and we will get back to you as soon as we can.', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.send_button_text', "<?php _e( 'Send', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.offline_note_thankyou_text', "<?php _e( 'Thank you for your message. We will get back to you as soon as we can.', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.offline_note_error_text', "<?php _e( 'You must complete all fields and specify a valid email address', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.offline_note_sending_text', "<?php _e( 'Sending...', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.operator_is_typing_text', "<?php _e( 'is typing...', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.operator_has_stopped_typing_text', "<?php _e( 'has stopped typing', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.introduction_error_text', "<?php _e( 'Please leave a name and email address so we can contact you in case we get disconnected', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.introduction_messages', "<?php _e( 'Welcome, just fill out some brief information and click \'Start chat\' to talk to us', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.introduction_submit_button_text', "<?php _e( 'Start chat', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.disabled_input_text_when_convo_has_ended', "<?php _e( 'chat ended, refresh for new chat', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.disabled_panel_text_when_convo_has_ended', "<?php _e( 'This conversation has ended, but all you need to do is refresh the page to start a new one!', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.name_input_text', "<?php _e( 'Name', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.phone_input_text', "<?php _e( 'Phone', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.email_input_text', "<?php _e( 'E-mail', 'ilmenite-olark' ); ?>");
		   olark.configure('locale.send_button_text', "<?php _e( 'Send', 'ilmenite-olark' ); ?>");
		</script>

		<?php
		echo ob_get_clean();

	}

}

function IlmeniteOlark() {
    return Ilmenite_Olark::instance();
}

// Initialize the class instance only once
IlmeniteOlark();