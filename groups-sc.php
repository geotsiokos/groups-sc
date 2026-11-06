<?php
/**
 * Plugin Name: Groups Shortcoder validation
 * Description: Checks if shortcodes created with Shortcoder plugin that use Groups shortcodes, should validate
 * Author: gtsiokos
 * Author URI: https://www.netpad.gr
 * Plugin URI: https://www.netpad.gr
 * Version: 1.0.0
 */

if( !defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'groups_shortcodes_validate_contents', 'sc_groups_shortcodes_validate_contents', 10 ,4 );
function sc_groups_shortcodes_validate_contents( $contents, $tag, $atts, $content ) {
	$found_posts_content = array();
	if ( defined( 'SC_POST_TYPE' ) ) {
		if ( preg_match_all( '/\[sc\b[^\]]*?name=["\']([^"\']*)["\']/', $contents, $matches ) !== false ) {
			if ( isset( $matches[1] ) ) {
				$shortcode_titles = $matches[1];
				foreach ( $shortcode_titles as $title ) {
					$query = new WP_Query(
						array(
							'post_type'      => SC_POST_TYPE,
							'title'          => $title,
							'posts_per_page' => 1,
							'no_found_rows'  => true,
						)
					);
					if ( $query->posts ) {
						$found_posts_content[] = $query->posts[0]->post_content;
					}
				}
			}
		}
	}
	if ( count( $found_posts_content ) > 0 ) {
		foreach ( $found_posts_content as $content ) {
			if (
				strpos( $content, 'groups_member' ) !== null ||
				strpos( $content, 'groups_non_member' ) !== null
			) {
				$contents .= implode( ' ', array( $content ) );
			}
		}
	}

	return $contents;
}

