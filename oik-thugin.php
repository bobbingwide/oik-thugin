<?php 
/**
Plugin Name: oik-thugin
Depends: oik base plugin, oik fields, oik themes, oik-shortcodes
Plugin URI: https://www.bobbingwide.com/blog/oik_plugins/oik-thugin
Description: Letter taxonomies for oik-plugins.com	- pseudo grandchild theme
Version: 0.1.0
Author: bobbingwide
Author URI: https://bobbingwide.com/about-bobbing-wide
Text Domain: oik_thugin
Domain Path: /languages/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

    Copyright 2017-2019 Bobbing Wide (email : herb@bobbingwide.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/


/**
 * Register the additional taxonomies for oik_thugin
 *
 * - Depends on oik-a2z for the "letters" taxonomy.
 * - Depends on oik-shortcodes-a2z for the "oik_letters" taxonomy. 
 * - oik-a2z automatically registers the filter that will set the taxonomy term from the title or content. 
 *
 */ 
function oik_thugin_loaded() {
  add_action( 'oik_fields_loaded', 'oik_thugin_oik_fields_loaded', 16 );
	add_action( "wp_enqueue_scripts", "oik_thugin_enqueue_scripts", 12 );
	add_filter( 'genesis_pre_get_option_footer_text', "oik_thugin_genesis_footer_creds_text", 11 );
	//add_action( 'genesis_entry_footer', 'oik_thugin_genesis_entry_footer', 11 );
	//add_filter( "register_post_type_args", "oik_thugin_register_post_type_args", 10, 2 );
}

/**
 * Implements 'oik_fields_loaded' for oik-plugins.com / oik-plugins.co.uk
 *
 * * Registers the letters taxonomy for oik plugins, themes, shortcodes and FAQs
 * * Registers the _plugin_ref for posts, pages and FAQs
 *
 * Note: The association of a post type to a letter taxonomy will automatically set the 
 * filter hooks which automatically set the taxonomy terms for a post
 * from the title and/or content. 
 * 
 */ 
function oik_thugin_oik_fields_loaded() {
	oik_thugin_register_oik_faq();
	register_taxonomy_for_object_type( "letters", "page" ); 
	bw_register_field_for_object_type( "letters", "page" );
	
	bw_register_custom_tags( "letters", "oik-plugins", "Letters" );
	bw_register_field_for_object_type( "letters", "oik-plugins" );
	
	register_taxonomy_for_object_type( "letters", "oik-themes" );
	bw_register_field_for_object_type( "letters", "oik-themes" );
	
	//register_taxonomy_for_object_type( "letters", "oik_shortcodes" );
	//bw_register_field_for_object_type( "letters", "oik_shortcodes" );
	
	/**
	 * oik-faq is defined using oik-types. 
	 * We need to do extend oik-faq using code that runs after oik-types.
	 */
	bw_register_custom_tags( "letters", "oik-faq", "Letters" );
	bw_register_field_for_object_type( "letters", "oik-faq" );
	
	
	bw_register_field( "_plugin_ref", "noderef", "Component", array( "#type" => array( "oik-plugins", "oik-themes" ), "#multiple" => 5, "#optional" => true ) );
	
	bw_register_field_for_object_type( "_plugin_ref", "post" );
	bw_register_field_for_object_type( "_plugin_ref", "page" );
	bw_register_field_for_object_type( "_plugin_ref", "oik-faq" );
	
}

/**
 * Registers the oik-faq CPT
 *
 */
function oik_thugin_register_oik_faq() {
	$post_type = 'oik-faq';
	$post_type_args = array();
	$post_type_args['label'] = 'FAQ';
	$post_type_args['description'] = 'Frequently Asked Questions';
	$post_type_args['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'author', 'publicize', 'clone' );
	//$post_type_args['taxonomies'] = array( "oik_tags" );
	$post_type_args['has_archive'] = true;
	$post_type_args['menu_icon'] = 'dashicons-admin-plugins';
	$post_type_args['show_in_rest'] = true;
	//$post_type_args['template'] = oikp_oik_plugins_CPT_template();
	bw_register_post_type( $post_type, $post_type_args );



}

/**
 * Implements 'register_post_type_args' for cloning
 *
 * This should only be done on the master, not the slave
 */
	
function oik_thugin_register_post_type_args( $args, $post_type ) {
	
	$post_types = array( "post", "page", "oik-plugins", "attachment", "oik-themes", 'oik-faq' );
	bw_trace2( $post_types, "post_types", false );
	$add_clone = in_array( $post_type, $post_types );
	if ( $add_clone ) {
		$args['supports'][] = 'clone';
	}
	bw_trace2( $add_clone, "add_clone", true );
	return( $args );

}

/**
 * Enqueues bwlink.css for styling of bobbing wide
 * 
 * Note: This is enqueued after oik-custom.css ( priority 12 )
 */
function oik_thugin_enqueue_scripts() {
	$timestamp = null;
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		$timestamp = filemtime( __DIR__ . '/css/oik-thugin.css' );
	}
	wp_enqueue_style( 'oik-thugin-css', oik_url( "css/oik-thugin.css", "oik-thugin" ), array(), $timestamp );
}

/**
 * Appends more stuff to the footer credits.
 * 
 * @param string $text - the footer credits so far
 * @return string a few additions to brighten the day
 */
function oik_thugin_genesis_footer_creds_text( $text ) { 
	$text .= "[div more][wp v p m][ediv]"; 
	return( $text );
}

/**
 * Adds [bw_fields] for single posts only
 *
 * @TODO This could fail if oik is not loaded. 
 */
function oik_thugin_genesis_entry_footer() {
	$post = get_post();
	if ( $post->post_type == "post" ) {
		echo bw_do_shortcode( "[bw_fields]" );
	}
}

oik_thugin_loaded();
