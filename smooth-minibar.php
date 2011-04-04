<?php
/**
 * @package Smooth Minibar
 */
/*
Plugin Name: Smooth Minibar
Plugin URI: http://bueltge.de/
Description: 
Version: 0.0.2
Author: Frank Bültge
Author URI: http://bueltge.de/
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class Smooth_Minibar {
	
	/**
	 * constructer
	 * 
	 * @uses add_filter, add_action
	 * @access public
	 * @since 0.0.1
	 * @return void
	 */
	public function __construct() {
		
		add_action( 'admin_enqueue_scripts',	array( $this, 'enqueue_script' ), 10, 1 );
		add_action( 'admin_print_styles',		array( $this, 'enqueue_style' ) );
		
		add_action( 'admin_footer', array( $this, 'get_minibar' ) );
	}
	
	/**
	 * include scripts in WP backend
	 * 
	 * @uses wp_enqueue_script
	 * @access public
	 * @since 0.0.1
	 * @param unknown_type $pagehook
	 * @return void
	 */
	public function enqueue_script($pagehook) {
		// for echo current page
		if ( defined('WP_DEBUG') && WP_DEBUG && 
			isset( $_GET['debug'] ) && 'true' == $_GET['debug'] )
			echo '<br /><br />Pagehook: <code>' . $pagehook . '</code>';
		
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
		
		$editor_pages	= array( 'post.php', 'post-new.php', 'comment.php' );
		$comments_pages	= array( 'edit-comments.php' );
		$custom_pages	= array( 'edit.php', 'edit-tags.php' );
		
		if ( in_array( $pagehook, $editor_pages ) ) {
			wp_enqueue_script( 
				'jquery-caret', 
				WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ) . '/js/jquery.caret.1.02' . $suffix. '.js', 
				array( 'jquery' )
			);
			wp_enqueue_script( 
				'smooth-minibar-editor', 
				WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ) . '/js/editor.js', 
				array( 'jquery-caret' )
			);
			wp_enqueue_script( 
				'smooth-minibar', 
				WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ) . '/js/script.js', 
				array( 'jquery-caret', 'smooth-minibar-editor' )
			);
		} elseif ( in_array( $pagehook, $comments_pages ) ) {
			wp_enqueue_script( 
				'jquery-caret', 
				WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ) . '/js/jquery.caret.1.02' . $suffix. '.js', 
				array( 'jquery' )
			);
			wp_enqueue_script( 
				'smooth-minibar-editor', 
				WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ) . '/js/edit-comments.js', 
				array( 'jquery-caret' )
			);
			wp_enqueue_script( 
				'smooth-minibar', 
				WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ) . '/js/script.js', 
				array( 'jquery-caret', 'smooth-minibar-editor' )
			);
		} elseif ( in_array( $pagehook, $custom_pages ) ) {
			wp_enqueue_script( 
				'jquery-caret', 
				WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ) . '/js/jquery.caret.1.02' . $suffix. '.js', 
				array( 'jquery' )
			);
			wp_enqueue_script( 
				'smooth-minibar-editor', 
				WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ) . '/js/custom.js', 
				array( 'jquery-caret' )
			);
			wp_enqueue_script( 
				'smooth-minibar', 
				WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ) . '/js/script.js', 
				array( 'jquery-caret', 'smooth-minibar-editor' )
			);
		}
	}
	
	/**
	 * include styles in WP backend
	 * 
	 * @uses $pagenow
	 * @access public
	 * @since 0.0.1
	 * @param void
	 * @return void
	 */
	public function enqueue_style() {
		global $pagenow;
		
		$pages = array( 'post.php', 'post-new.php', 'comment.php', 'edit-comments.php', 'edit.php', 'edit-tags.php' );
		
		if ( in_array( $pagenow, $pages ) )
			wp_enqueue_style( 'smooth-minibar', WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ) . '/css/style.css' );
	}
	
	/**
	 * Echo minibar
	 * use filter to add buttons, for buttons in the first toolbat, via mouse select, use smooth_minibar_select_buttons
	 * for buttons with popup, the second toolbar use smooth_minibar_dblselect_buttons
	 * 
	 * @uses $pagenow
	 * @access public
	 * @since 0.0.1
	 * @param void
	 * @return string $minibar_markup
	 */
	public function get_minibar() {
		
		$defaults_select = array (
			'h3' => array( 
				'name'			=> 'h3',
				'title'			=> __( 'Heading' ),
				'data-minibar' 	=> array(
					'wrapTextBefore'	=> '<h3>',
					'wrapTextAfter'		=> '</h3>'
				)
			),
			'h4' => array( 
				'name'			=> 'h4',
				'title'			=> __( 'Heading' ),
				'data-minibar' 	=> array(
					'wrapTextBefore'	=> '<h4>',
					'wrapTextAfter' 	=> '</h4>'
				)
			),
			'bold' => array( 
				'name'			=> 'b',
				'title'			=> __( 'Strong' ),
				'data-minibar' 	=> array(
					'wrapTextBefore'	 => '<strong>',
					'wrapTextAfter'		 => '</strong>'
				)
			),
			'italic' => array(
				'name'			=> 'i',
				'title'			=> 'Emphasized text',
				'data-minibar' 	=> array(
					'wrapTextBefore'	 => '<em>',
					'wrapTextAfter'		 => '</em>'
				)
			),
			'link' => array(
				'name'			=> 'a',
				'title'			=> 'Anchor or link',
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<a>',
					'wrapTextAfter'	 	=> '</a>',
					'attributes' 		=> array(
						'href' => 'Enter the URL of the target'
					)
				)
			),
			'blockquote' => array(
				'name'			=> 'b-quote',
				'title'			=> 'Defines a long quotation',
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<blockquote>',
					'wrapTextAfter' 	=> '</blockquote>'
				)
			),
			'cite' => array(
				'name'			=> 'q',
				'title'			=> 'Defines a citation',
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<cite>',
					'wrapTextAfter' 	=> '</cite>'
				)
			),
			'delete' => array(
				'name'			=> 'del',
				'title'			=> 'Defines text that has been deleted from a document, with timestamp',
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<del datetime="' . date("YmdTh:i:s+00:00") . '">',
					'wrapTextAfter' 	=> '</del>'
				)
			),
			'insert' => array(
				'name'			=> 'ins',
				'title'			=> 'Defines text that has been inserted into a document, with timestamp',
				'data-minibar' 	=> array(
					'wrapTextBefore'	 => '<ins datetime="' . date("YmdTh:i:s+00:00") . '">',
					'wrapTextAfter'		 => '</ins>'
				)
			),
			'unorderedlist' => array(
				'name'			=> 'ul',
				'title'			=> 'unordered list',
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<ul>' . "\r\n",
					'wrapTextAfter' 	=> "\r\n" . '</ul>'
				)
			),
			'orderedlist' => array(
				'name'			=> 'ol',
				'title'			=> 'ordered list',
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<ol>' . "\r\n",
					'wrapTextAfter' 	=> "\r\n" . '</ol>'
				)
			),
			'list' => array(
				'name'			=> 'li',
				'title'			=> 'list tag',
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> "\r\n" . '<li>',
					'wrapTextAfter' 	=> '</li>' . "\r\n"
				)
			),
			'code' => array(
				'name'			=> 'code',
				'title'			=> 'code phrase tag',
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<code>',
					'wrapTextAfter' 	=> '</code>'
				)
			),
			'pre' => array(
				'name'			=> 'pre',
				'title'			=> 'preformatted text',
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> "\r\n" . '<pre>',
					'wrapTextAfter' 	=> '</pre>' . "\r\n"
				)
			)
		);
		
		$defaults_dblclick = array (
			'img' => array( 
				'name'	=> 'img',
				'title'	=> __( 'Insert a image' ),
				'data-minibar' 	=> array(
					'wrapTextBefore' => '<img />',
					'wrapTextAfter' => '',
					'attributes' => array(
						'src' => 'Enter the URL of the image',
						'alt' => 'Enter a description of the image'
					)
				)
			),
			'more' => array(
				'name'			=> 'more',
				'title'			=> 'Here you want to end the excerpted content',
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<!--more-->',
					'wrapTextAfter' 	=> ''
				)
			),
			'nextpage' => array(
				'name'			=> 'nextpage',
				'title'			=> 'Split a single post up into different web pages',
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<!--nextpage-->',
					'wrapTextAfter' 	=> ''
				)
			)
		);
		
		// Make it filterable
		$buttons_select		= apply_filters( 'smooth_minibar_select_buttons', 	$defaults_select );
		$buttons_dblclick	= apply_filters( 'smooth_minibar_dblclick_buttons', 	$defaults_dblclick );
		
		// first buttons, view on select text
		$minibar_buttons_select = '';
		foreach ( $buttons_select as $id => $args ) {
			$minibar_buttons_select .= '<a 
			href="javascript:return false;"
			' . (isset($args['title']) ? ' title="' . $args['name'] . '"' : '') . '
			' . (isset($args['data-minibar']) ? ' data-minibar=\''
			 . json_encode($args['data-minibar'], JSON_FORCE_OBJECT) . '\'' : '') . '
			>' . (isset($args['name']) ? $args['name'] : '') . '</a>' . "\n";
		}
		
		// second buttons, view on double click 
		$minibar_buttons_dblclick = '';
		foreach ( $buttons_dblclick as $id => $args ) {
			$minibar_buttons_dblclick .= '<a 
			href="javascript:return false;"
			' . (isset($args['title']) ? ' title="' .$args['name'].'"' : '').'
			' . (isset($args['data-minibar']) ? ' data-minibar=\'' .json_encode($args['data-minibar'],JSON_FORCE_OBJECT).'\'' : '').'
			>' . (isset($args['name']) ? $args['name'] : '') . '</a>' . "\n";
		}
		
		// get markup
		$minibar_markup = "\n"
			. '<div id="smooth_minibar_menu">'
			. $minibar_buttons_select
			. '</div>' . "\n"
			. '<div id="smooth_minibar_menu_noselect">'
			. $minibar_buttons_dblclick
			. '</div>'
			. "\n";
		
		echo $minibar_markup;
	}
	
}

if ( function_exists('add_action') && class_exists('Smooth_Minibar') ) {
	$smooth_minibar = new Smooth_Minibar();
} else {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
?>
