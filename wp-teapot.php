<?php
/*
Plugin Name: WP Teapot
Plugin URI: http://www.stevenword.com/wp-teapot/
Description: I'm a teapot
Text Domain: wp-teapot
Version: 1.0.2
Author: Steven Word
Author URI: http://stevenword.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2014 Steven K. Word

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>

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
*/

/**
 * WP Teapot
 */
class WP_Teapot {
	const VERSION        = '1.0.2';
	const REVISION       = '20220123';
	const METAKEY        = 'wp_teapot';
	const NONCE          = 'wp_teapot_nonce';
	const NONCE_FAIL_MSG = 'Cheatin&#8217; huh?';
	const TEXT_DOMAIN    = 'wp-teapot';

	private $is_teapot = false;

	/* Define and register singleton */
	private static $instance = false;
	public static function instance() {
		if( ! self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Clone
	 *
	 * @since 1.0.0
	 */
	private function __clone() { }

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$plugin_dir = basename( dirname( __FILE__ ) );
		load_plugin_textdomain( self::TEXT_DOMAIN, false, $plugin_dir . '/languages/' );
		self::init();
	}

	/**
	 * WP Init
	 *
	 * @since 1.0.0
	 */
	public function init() {

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// Actions
		add_action( 'init', array( $this, 'add_endpoint' ), 11 );
		add_action( 'send_headers', array( $this, 'action_send_headers' ) );
		add_action( 'parse_request', array( $this, 'parse_request' ) );
	}

	public function activate() {
		$this->add_endpoint();
		flush_rewrite_rules();
	}

	public function deactivate() {
		flush_rewrite_rules();
	}

	public function add_endpoint() {
		add_rewrite_endpoint( 'brew', EP_ROOT );
	}

	public function parse_request( $wp ) {
		if( isset( $wp->query_vars['brew'] ) ) {
			$this->is_teapot = true;
		}
	}

	/**
	 * Send em' headers, like they've never been sent before!
	 *
	 * @since 1.0.0
	 */
	public function action_send_headers() {
		// if this is not a request for json or a singular object then bail
		if ( ! $this->is_teapot ) {
			return;
		}

		$code = 418; // Short and stout
		status_header( $code ); // Here is my handle
		$code_desc = get_status_header_desc( $code ); // Here is my spout

		$this->render_template();
		exit();
	}

	public function render_template() {
		echo '<!doctype html>
		<html class="google" lang="en">
		<meta charset="utf-8">
		<meta content="initial-scale=1, minimum-scale=1, width=device-width" name="viewport">
		<title>Error 418 (I’m a teapot)!?</title>
		<link href="//www.gstatic.com/teapot/teapot.min.css" rel="stylesheet">
		<h2>' . get_bloginfo( 'sitename' ) . '</h2>
		<p><b>418.</b> <ins>I’m a teapot.</ins></p>
		<p>The requested entity body is short and stout. <ins>Tip me over and pour me out.</ins></p>
		<div id="teaset">
			<div id="teabot"></div>
			<div id="teacup"></div>
		</div>
		</html>';
	}

}
WP_Teapot::instance();
