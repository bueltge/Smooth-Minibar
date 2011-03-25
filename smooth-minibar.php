<?php
/**
 * @package Smooth Minibar
 */
/*
Plugin Name: Smooth Minibar
Plugin URI: http://bueltge.de/
Description: It is a variation of a toolbar that exposes context-related functionality.
Version: 0.0.1
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
	 * construct
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
	 * Enqueue scripts in WP
	 * 
	 * @uses wp_enqueue_script
	 * @access public
	 * @since 0.0.1
	 * @param unknown_type $pagehook
	 * @return void
	 */
	public function enqueue_script($pagehook) {
		
		if ( defined('WP_DEBUG') && WP_DEBUG )
			echo '<br><br>Pagehook: <code>' . $pagehook . '</code>';
		
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
	 * Enqueue style in WP
	 * 
	 * @uses wp_enqueue_style
	 * @access public
	 * @since 0.0.1
	 * @return void
	 */
	public function enqueue_style() {
		global $pagenow;
		
		$pages = array( 'post.php', 'post-new.php', 'comment.php', 'edit-comments.php', 'edit.php', 'edit-tags.php' );
		
		if ( in_array( $pagenow, $pages ) )
			wp_enqueue_style( 'smooth-minibar', WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ) . '/css/style.css' );
	}
	
	/**
	 * get html for buttons
	 * use the filters smooth_minibar_select_buttons and smooth_minibar_dblclick_buttons for custom bottons
	 * 
	 * @access public
	 * @since 0.0.1
	 * @return strong $minibar_markup
	 */
	public function get_minibar() {
		global $pagenow;
		
		$pages = array( 'post.php', 'post-new.php', 'comment.php', 'edit-comments.php', 'edit.php', 'edit-tags.php' );
		
		if ( !in_array( $pagenow, $pages ) )
			return NULL;
		
		$defaults_select = array (
			'h3' => array( 
				'name'	=> 'h3',
				'title'	=> __( 'Heading' )
			),
			'h4' => array( 
				'name'	=> 'h4',
				'title'	=> __( 'Heading' )
			),
			'bold' => array( 
				'name'	=> 'b',
				'title'	=> __( 'Strong' )
			),
			'italic' => array(
				'name'	=> 'i',
				'title'	=> 'Emphasized text'
			),
			'link' => array(
				'name'	=> 'a',
				'title'	=> 'Anchor or link'
			),
			'blockquote' => array(
				'name'	=> 'b-quote',
				'title'	=> 'Defines a long quotation'
			),
			'cite' => array(
				'name'	=> 'q',
				'title'	=> 'Defines a citation'
			),
			'delete' => array(
				'name'	=> 'del',
				'title'	=> 'Defines text that has been deleted from a document, with timestamp'
			),
			'insert' => array(
				'name'	=> 'ins',
				'title'	=> 'Defines text that has been inserted into a document, with timestamp'
			),
			'unorderedlist' => array(
				'name'	=> 'ul',
				'title'	=> 'unordered list'
			),
			'orderedlist' => array(
				'name'	=> 'ol',
				'title'	=> 'ordered list'
			),
			'list' => array(
				'name'	=> 'li',
				'title'	=> 'list tag'
			),
			'code' => array(
				'name'	=> 'code',
				'title'	=> 'code phrase tag'
			),
			'pre' => array(
				'name'	=> 'pre',
				'title'	=> 'preformatted text'
			)
		);
		
		$defaults_dblclick = array (
			'img' => array( 
				'name'	=> 'img',
				'title'	=> __( 'Insert a image' )
			),
			'more' => array(
				'name'	=> 'more',
				'title'	=> 'Here you want to end the excerpted content'
			),
			'nextpage' => array(
				'name'	=> 'nextpage',
				'title'	=> 'Split a single post up into different web pages'
			)
		);
		
		// Make it filterable
		$buttons_select		= apply_filters( 'smooth_minibar_select_buttons', 	$defaults_select );
		$buttons_dblclick	= apply_filters( 'smooth_minibar_dblclick_buttons', 	$defaults_dblclick );
		
		// first buttons, view on select text
		$minibar_buttons_select = NULL;
		foreach ( $buttons_select as $id => $args ) {
			if ( !isset($args['name']) )
				$args['name'] = $id;
			if ( ( !isset($args['title']) ) )
					$args['title'] = '';
			else
				$args['title'] = ' title="' . $args['title'] . '"';
			
			$minibar_buttons_select .= '
				<a href="javascript:return false;" id="' . $id . '"' . $args['title'] . ' >' . $args['name'] . '</a>' . "\n";
		}
		
		// second buttons, view on double click 
		$minibar_buttons_dblclick = NULL;
		foreach ( $buttons_dblclick as $id => $args ) {
			if ( !isset($args['name']) )
				$args['name'] = $id;
			if ( ( !isset($args['title']) ) )
					$args['title'] = '';
			else
				$args['title'] = ' title="' . $args['title'] . '"';
			
			$minibar_buttons_dblclick .= '
				<a href="javascript:return false;" id="' . $id . '"' . $args['title'] . ' >' . $args['name'] . '</a>' . "\n";
		}
		
		// get markup
		$minibar_markup = "\n" . 
			'<div id="smooth_minibar_menu">' .
			$minibar_buttons_select
			. '</div>' . 
			"\n" . 
			'<div id="smooth_minibar_menu_noselect">' .
			$minibar_buttons_dblclick
			. '</div>' . "\n";
		
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
