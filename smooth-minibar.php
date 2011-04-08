<?php
/**
 * @package Smooth Minibar
 */

/**
 * Plugin Name: Smooth Minibar
 * Plugin URI: http://bueltge.de/
 * Text Domain: smooth-minibar
 * Domain Path: /languages
 * Description: It is a variation of a toolbar that exposes context-related functionality.
 * Version: 0.0.2
 * Author: Frank Bültge
 * Author URI: http://bueltge.de/
 * Upgrade Check: none
 * License: GPLv2
*/

/*
License:
=====================================================================================================
Copyright 2011 Frank Bueltge  (email : frank@bueltge.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Requirement
=====================================================================================================
This plugin requires WordPress >= 3.0, PHP >=5.2.8 and tested with PHP Interpreter >= 5.3.1 on WP 3.1
*/

class Smooth_Minibar {
	
	// var for multilanguage
	protected $textdomain = 'smooth-minibar';
	// pages in backend to include js/css
	public $editor_pages	= array( 'post.php', 'post-new.php', 'comment.php' );
	public $comments_pages	= array( 'edit-comments.php' );
	public $custom_pages	= array( 'edit.php', 'edit-tags.php' );
	
	/**
	 * constructer
	 * 
	 * @uses add_filter, add_action
	 * @access public
	 * @since 0.0.1
	 * @return void
	 */
	public function __construct() {
		
		$this->localize_plugin();
		// on activation of the plugin add cap to roles
		register_activation_hook( __FILE__, array( $this, 'on_activate' ) );
		
		add_action( 'admin_enqueue_scripts',	array( $this, 'enqueue_script' ), 10, 1 );
		add_action( 'admin_print_styles',		array( $this, 'enqueue_style' ) );
		
		add_action( 'init', array( $this, 'on_admin_init' ) );
		add_action( 'admin_footer', array( $this, 'get_minibar' ) );
	}
	
	/**
	 * points the class
	 * 
	 * @access public
	 * @since 0.0.2
	 * @return void
	 */
	public function get_object() {
		
		if ( NULL === self::$classobj )
			self::$classobj = new self;
		
		return self::$classobj;
	}
	
	/**
	 * return textdomain from var
	 * use filter $this->textdomain . 'textdomain' for add filter functions
	 * 
	 * @uses apply_filters
	 * @access public
	 * @since 0.0.2
	 * @return string $this->textdomain
	 */
	public function get_textdomain() {
		
		return apply_filters( $this->textdomain . 'textdomain', $this->textdomain );
	}
	
	/**
	 * load textfile .mo
	 * 
	 * @uses load_plugin_textdomain
	 * @access public
	 * @since 0.0.2
	 * @return void
	 */
	public function localize_plugin() {
		
		load_plugin_textdomain( $this->textdomain, FALSE, dirname( plugin_basename(__FILE__) ) . '/languages' );
	}
	
	/**
	 * return plugin comment data
	 * 
	 * @since 0.0.2
	 * @access public
	 * @param $value string, default = 'Version'
	 *        Name, PluginURI, Version, Description, Author, AuthorURI, TextDomain, DomainPath, Network, Title
	 * @return string
	 */
	public function get_plugin_data( $value = 'Version' ) {
		
		$plugin_data = get_plugin_data( __FILE__ );
		$plugin_value = $plugin_data[$value];
		
		return $plugin_value;
	}
	
	/**
	 * Check WP- and PHP version on activate plugin
	 * 
	 * @uses wp_sprintf, deactivate_plugins
	 * @access public
	 * @since 0.0.2
	 * @param void
	 * @return void
	 */
	public function on_activate() {
		global $wp_version;
		
		// check wp version
		if ( !version_compare( $wp_version, '3.0', '>=' ) ) {
			deactivate_plugins( __FILE__ );
			die( 
				wp_sprintf( 
					'<strong>%s:</strong> ' . 
					__( 'Sorry, This plugin requires WordPress 3.0+', $this->textdomain )
					, self::get_plugin_data('Name')
				)
			);
		}
		
		// check php version
		if ( version_compare( PHP_VERSION, '5.2.8', '<' ) ) {
			deactivate_plugins( __FILE__ ); // Deactivate ourself
			die( 
				wp_sprintf(
					'<strong>%1s:</strong> ' . 
					__( 'Sorry, This plugin has taken a bold step in requiring PHP 5.2.8+, Your server is currently running PHP %2s, Please bug your host to upgrade to a recent version of PHP which is less bug-prone. At last count, <strong>over 80%% of WordPress installs are using PHP 5.2+</strong>.', $this->textdomain )
					, self::get_plugin_data('Name'), PHP_VERSION 
				)
			);
		}
	}
	
	function on_admin_init() {
		
		if ( !is_admin() )
			return NULL;
		
		require_once( dirname( __FILE__ ) . '/inc/class.settings.php' );
		$smooth_minibar_settings = new Smooth_Minibar_Settings();
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
		
		if ( in_array( $pagehook, $this->editor_pages ) ) {
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
		} elseif ( in_array( $pagehook, $this->comments_pages ) ) {
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
		} elseif ( in_array( $pagehook, $this->custom_pages ) ) {
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
		
		$pages = $this->editor_pages;
		$pages = array_merge( $pages, $this->custom_pages );
		$pages = array_merge( $pages, $this->comments_pages );
		
		if ( ! in_array( $pagenow, $pages ) )
			return NULL;
		
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
		global $pagenow;
		
		$pages = $this->editor_pages;
		$pages = array_merge( $pages, $this->custom_pages );
		$pages = array_merge( $pages, $this->comments_pages );
		
		if ( ! in_array( $pagenow, $pages ) )
			return NULL;
		
		$defaults_select = array (
			'h3' => array( 
				'name'			=> 'h3',
				'title'			=> __( 'Heading', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore'	=> '<h3>',
					'wrapTextAfter'		=> '</h3>'
				)
			),
			'h4' => array( 
				'name'			=> 'h4',
				'title'			=> __( 'Heading', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore'	=> '<h4>',
					'wrapTextAfter' 	=> '</h4>'
				)
			),
			'bold' => array( 
				'name'			=> 'b',
				'title'			=> __( 'Strong', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore'	 => '<strong>',
					'wrapTextAfter'		 => '</strong>'
				)
			),
			'italic' => array(
				'name'			=> 'i',
				'title'			=> __( 'Emphasized text', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore'	 => '<em>',
					'wrapTextAfter'		 => '</em>'
				)
			),
			'link' => array(
				'name'			=> 'a',
				'title'			=> __( 'Anchor or link', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<a>',
					'wrapTextAfter'	 	=> '</a>',
					'attributes' 		=> array(
						'href' => __( 'Enter the URL of the target', $this->textdomain )
					)
				)
			),
			'blockquote' => array(
				'name'			=> 'b-quote',
				'title'			=> __( 'Defines a long quotation', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<blockquote>',
					'wrapTextAfter' 	=> '</blockquote>'
				)
			),
			'cite' => array(
				'name'			=> 'q',
				'title'			=> __( 'Defines a citation', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<cite>',
					'wrapTextAfter' 	=> '</cite>'
				)
			),
			'delete' => array(
				'name'			=> 'del',
				'title'			=> __('Defines text that has been deleted from a document, with timestamp', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<del datetime="' . date("YmdTh:i:s+00:00") . '">',
					'wrapTextAfter' 	=> '</del>'
				)
			),
			'insert' => array(
				'name'			=> 'ins',
				'title'			=> __( 'Defines text that has been inserted into a document, with timestamp', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore'	 => '<ins datetime="' . date("YmdTh:i:s+00:00") . '">',
					'wrapTextAfter'		 => '</ins>'
				)
			),
			'unorderedlist' => array(
				'name'			=> 'ul',
				'title'			=> __( 'unordered list', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<ul>' . "\r\n",
					'wrapTextAfter' 	=> "\r\n" . '</ul>'
				)
			),
			'orderedlist' => array(
				'name'			=> 'ol',
				'title'			=> __( 'ordered list', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<ol>' . "\r\n",
					'wrapTextAfter' 	=> "\r\n" . '</ol>'
				)
			),
			'list' => array(
				'name'			=> 'li',
				'title'			=> __( 'list tag', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> "\r\n" . '<li>',
					'wrapTextAfter' 	=> '</li>' . "\r\n"
				)
			),
			'code' => array(
				'name'			=> 'code',
				'title'			=> __( 'code phrase tag', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<code>',
					'wrapTextAfter' 	=> '</code>'
				)
			),
			'pre' => array(
				'name'			=> 'pre',
				'title'			=> __( 'preformatted text', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> "\r\n" . '<pre>',
					'wrapTextAfter' 	=> '</pre>' . "\r\n"
				)
			)
		);
		
		$defaults_dblclick = array (
			'img' => array( 
				'name'	=> 'img',
				'title'	=> __( 'Insert a image', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore' => '<img />',
					'wrapTextAfter' => '',
					'attributes' => array(
						'src' => __( 'Enter the URL of the image', $this->textdomain ),
						'alt' => __( 'Enter a description of the image', $this->textdomain )
					)
				)
			),
			'more' => array(
				'name'			=> 'more',
				'title'			=> __( 'Here you want to end the excerpted content', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<!--more-->',
					'wrapTextAfter' 	=> ''
				)
			),
			'nextpage' => array(
				'name'			=> 'nextpage',
				'title'			=> __( 'Split a single post up into different web pages', $this->textdomain ),
				'data-minibar' 	=> array(
					'wrapTextBefore' 	=> '<!--nextpage-->',
					'wrapTextAfter' 	=> ''
				)
			)
		);
		
		// Make it filterable
		$buttons_select		= apply_filters( 'smooth_minibar_select_buttons', 	$defaults_select );
		$buttons_dblclick	= apply_filters( 'smooth_minibar_dblclick_buttons', $defaults_dblclick );
		
		// first buttons, view on select text
		$minibar_buttons_select = '';
		foreach ( $buttons_select as $id => $args ) {
			$minibar_buttons_select .= '<a 
			href="javascript:return false;"
			' . (isset($args['title']) ? ' title="' . $args['title'] . '"' : '') . '
			' . (isset($args['data-minibar']) ? ' data-minibar=\''
			 . json_encode($args['data-minibar'], JSON_FORCE_OBJECT) . '\'' : '') . '
			>' . (isset($args['name']) ? $args['name'] : '') . '</a>' . "\n";
		}
		
		// second buttons, view on double click 
		$minibar_buttons_dblclick = '';
		foreach ( $buttons_dblclick as $id => $args ) {
			$minibar_buttons_dblclick .= '<a 
			href="javascript:return false;"
			' . (isset($args['title']) ? ' title="' . $args['title'] . '"' : '').'
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
