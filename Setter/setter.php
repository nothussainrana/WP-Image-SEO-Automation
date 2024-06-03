<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
Plugin Name: Image SEO Setter
Plugin URI: https://github.com/nothussainrana/File-Renaming-on-upload-fork
Description: Customised renaming on upload for image files and setting of alt_text, description, caption, title.
Version: 0.0.2
Author: nothussainrana
Author URI: https://github.com/nothussainrana
License: MIT License
License URI: https://www.mit.edu/~amini/LICENSE.md
*/

add_filter('sanitize_file_name', 'custom_filename');
add_filter('wp_insert_attachment_data', 'custom_image_title');
add_action('add_attachment', 'custom_image_alt_text');

/**
 * Get parent post id
 *
 * @version 0.0.1
 * @since   0.0.1
 *
 * @return string
 */
function get_parent_post_id(): string {
	if ( isset( $_REQUEST['post_id'] ) ) {
			$post_id = $_REQUEST['post_id'];
	} elseif ( isset( $_REQUEST['post_ID'] ) ) {
			$post_id = $_REQUEST['post_ID'];
	} elseif ( isset( $_REQUEST['post'] ) ) {
			$post_id = $_REQUEST['post'];
	} else {
			$post_id = "0";
	}
	return filter_var( $post_id, FILTER_SANITIZE_NUMBER_INT );
}
function custom_filename($filename) {
	$post_id = get_parent_post_id();
	$path_info = pathinfo($filename);

	if($post_id != "0"){
		$terms = wp_get_post_terms($post_id, 'product_cat');
		$categories = array();

        foreach ($terms as $term) {
            $categories[] = $term->name;
        }

		$categories = implode("-",$categories);
		$product_name = get_the_title($post_id);
		$random_number = sprintf("%04d", rand(0, 9999));

		$filename = $categories."-furniture-".$product_name."-".$post_id."-".$random_number.".".$path_info['extension'];
		$filename = strtolower(str_replace(" ","_",$filename));
	}
	return $filename;
}

function custom_image_title($data) {
	$post_id = get_parent_post_id();

	if($post_id != "0"){
		$terms = wp_get_post_terms($post_id, 'product_cat');
		$categories = array();

		foreach ($terms as $term) {
			$categories[] = $term->name;
		}

		$subcategory = end($categories);
		$product_name = get_the_title($post_id);

		$data['post_title'] = $subcategory."-".$product_name;

		$data['post_excerpt'] = strtoupper($product_name);

		//$data['post_content'] = "this is the description;
	}
    return $data;
}

function custom_image_alt_text($attachment_id){
	$post_id = get_parent_post_id();
	if($post_id != "0"){
		$terms = wp_get_post_terms($post_id, 'product_cat');
		$categories = array();

		foreach ($terms as $term) {
			$categories[] = $term->name;
		}

		$subcategory = end($categories);
		$product_name = get_the_title($post_id);

		$alt_text = "Teak Furniture Malaysia ".$subcategory." ".$product_name;
		update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt_text);
	}
}