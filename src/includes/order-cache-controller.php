<?php

namespace WooMinecraft\Orders\Cache;

/**
 * Sets up all the things related to Order cache handling.
 */
function setup() {
	$n = function( $string ) {
		return __NAMESPACE__ . '\\' . $string;
	};

	add_action( 'save_post', $n( 'bust_command_cache' ) );
}

/**
 * Helper method for transient busting
 *
 * @param int $post_id
 */
function bust_command_cache( $post_id = 0 ) {
	global $wpdb;

	if ( ! empty( $post_id ) && 'shop_order' !== get_post_type( $post_id ) ) {
		return;
	}

	$keys = $wpdb->get_col( $wpdb->prepare( "select distinct option_name from {$wpdb->options} where option_name like '%s'", '%' . $this->command_transient . '%' ) ); // @codingStandardsIgnoreLine Have to use this.
	if ( ! $keys ) {
		return;
	}

	foreach ( $keys as $key ) {
		$key = str_replace( '_transient_', '', $key );
		delete_transient( $key );
	}
}

/**
 * Creates a transient based on the wmc_key variable
 *
 * @since 1.2
 *
 * @return string|false The key on success, false if no GET param can be found.
 */
function get_transient_key() {
	$key = sanitize_text_field( $_GET['wmc_key'] ); // @codingStandardsIgnoreLine we don't care, just escape the data.
	if ( ! $key ) {
		return false;
	}

	return $this->command_transient . '_' . $key;
}
