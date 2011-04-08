<?php
//avoid direct calls to this file where wp core files not present
if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

class Smooth_Minibar_Settings {
	
	protected $smooth_minibar;
	
	protected $textdomain;
	
	public function __construct() {
		
		$this->smooth_minibar = new Smooth_Minibar();
		$this->textdomain = $this->smooth_minibar->get_textdomain();
		
		add_filter( 'screen_layout_columns', array( $this, 'get_screen_layout_columns' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_post_save_' . $this->textdomain . '_general', array( $this, 'save_settings' ) );
	}
	
	public function get_screen_layout_columns($columns, $screen) {
		
		if ( $screen == $this->pagehook )
			$columns[$this->pagehook] = 2;
		
		return $columns;
	}
	
	public function add_options_page() {
		
		//$menutitle  = '<span class="smooth-minibar-icon">&nbsp;</span>';
		$menutitle = $this->smooth_minibar->get_plugin_data( 'Name' );
		$this->pagehook = add_options_page( 
			__( 'Smooth Minibar Settings', $this->textdomain ), 
			$menutitle, 
			'manage_options',
			$this->textdomain . '-settings', 
			array( $this, 'get_options_page' )
		);
		
		add_action( 'load-' . $this->pagehook, array( $this, 'on_load_page' ) );
	}
	
	public function on_load_page() {
		
		wp_enqueue_script( array('common', 'wp-lists', 'postbox') );
		// include style for seetings page, incl. h2-icon
		wp_enqueue_style( $this->textdomain . '-settings', plugins_url( 'css/settings.css', __FILE__ ) );
		// include class for meta boxes About
		require_once( dirname( __FILE__ ) . '/class.about-meta-box.php' );
		$about_meta_mox = new About_Meta_Box();
	}
	
	public function get_options_page() {
		global $screen_layout_columns;
		
		$data = array('My Data 1', 'My Data 2', 'Available Data 1');
		?>
		<div id="<?php echo $this->textdomain; ?>-general" class="wrap">
			<?php screen_icon( $this->textdomain . '-settings' ); ?>
			<h2><?php echo $this->smooth_minibar->get_plugin_data( 'Name' ); ?></h2>
			<form action="admin-post.php" method="post">
				<?php wp_nonce_field( $this->textdomain . '-general' ); ?>
				<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', FALSE ); ?>
				<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', FALSE ); ?>
				<input type="hidden" name="action" value="save_<?php echo $this->textdomain; ?>_general" />
			
				<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
					<div id="side-info-column" class="inner-sidebar">
						
						<?php do_meta_boxes( $this->pagehook, 'side', $data ); ?>
						
					</div>
					<div id="post-body" class="has-sidebar">
						<div id="post-body-content" class="has-sidebar-content">
							
							<?php do_meta_boxes( $this->pagehook, 'normal', $data ); ?>
							
							<?php do_meta_boxes( $this->pagehook, 'additional', $data ); ?>
							
							<p>
								<input type="submit" value="Save Changes" class="button-primary" name="Submit"/>	
							</p>
						</div>
					</div>
					<br class="clear"/>
					
				</div>	
			</form>
		</div>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function($) {
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			});
			//]]>
		</script>
		
		<?php
	}

	public function save_settings() {
		
		if ( ! current_user_can('manage_options') )
			wp_die( __('Cheatin&#8217; uh?') );
		
		check_admin_referer( $this->textdomain . '-general' );
		
		wp_redirect( $_POST['_wp_http_referer'] );
	}
	
}
?>