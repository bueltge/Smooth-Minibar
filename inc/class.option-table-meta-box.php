<?php
//avoid direct calls to this file where wp core files not present
if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

class Option_Table_Meta_Box {
	
	protected $smooth_minibar;
	
	protected $textdomain;
	
	protected $pagehook;
	
	public function __construct() {
		
		$this->smooth_minibar = new Smooth_Minibar();
		$this->textdomain = $this->smooth_minibar->get_textdomain();
		$this->pagehook = 'settings_page_' . $this->textdomain . '-settings';
		
		add_filter( 'smooth_minibar_dblclick_buttons', array( $this, 'get_smooth_minibar_dblclick_buttons' ), 20, 1 );
		
		add_meta_box( 
			$this->textdomain . '-sidebox-option',
			__( 'Options', $this->textdomain ),
			array( $this, 'get_table_metabox' ),
			$this->pagehook,
			'normal', // normal, additional
			'default' // default, low, high
		);
	}
	
	public function get_smooth_minibar_dblclick_buttons($dblclick_buttons) {
		
		return $dblclick_buttons;
	}
	
	/**
	 * below the metaboxes for settings
	 * siede, normal, additional
	 */
	public function get_table_metabox($data) {
		?>
		<table class="widefat" cellspacing="0">
		<?php
			$dblclick_buttons = array( 'test' => array('data' =>1, 'title' => 2, 'data-minibar' => 3) );
			$style = '';
			foreach ($dblclick_buttons as $type => $data) {
				$style = ('class="alternate"' == $style || 'class="alternate active"' == $style) ? '' : 'alternate';
				
				if ($style != '')
					$style = 'class="' . $style . '"';
				echo '
					<tr ' . $style . '>
						<td class="row-title">' . $type . '</td>
						<td>' . $data['data'] . '</td>
						<td>' . $data['title'] . '</td>
						<td>' . $data['data-minibar'] . '</td>
					</tr>';
			}
		?>
		</table>
		<?php
	}
	
}
?>