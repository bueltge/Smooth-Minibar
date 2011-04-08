<?php
//avoid direct calls to this file where wp core files not present
if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

class About_Meta_Box {
	
	protected $smooth_minibar;
	
	protected $textdomain;
	
	protected $pagehook;
	
	public function __construct() {
		
		$this->smooth_minibar = new Smooth_Minibar();
		$this->textdomain = $this->smooth_minibar->get_textdomain();
		$this->pagehook = 'settings_page_' . $this->textdomain . '-settings';
		
		add_meta_box( 
			$this->textdomain . '-sidebox-about',
			__( 'About the plugin', $this->textdomain ),
			array( $this, 'get_about_metabox' ),
			$this->pagehook,
			'side', // normal, additional
			'core'
		);
	}
	
	/**
	 * below the metaboxes for settings
	 * siede, normal, additional
	 */
	public function get_about_metabox($data) {
		?>
		<p><?php _e('Further information: Visit the <a href="http://bueltge.de">plugin homepage</a> for further information or to grab the latest version of this plugin.', $this->textdomain); ?></p>
		<p>
		<span style="float: left;">
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="4578111">
			<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="<?php _e('PayPal - The safer, easier way to pay online!', $this->textdomain); ?>">
			<img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1">
		</form>
		</span>
		<?php _e('You want to thank me? Visit my <a href="http://bueltge.de/wunschliste/">wishlist</a> or donate.', $this->textdomain); ?>
		</p>
		<p>&copy; Copyright 3/2011 - <?php echo date('m/Y'); ?> <a href="http://bueltge.de/">Frank B&uuml;ltge</a></p>
		<?php
	}
	
}
?>