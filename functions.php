<?php

function file_path_from_url( $url ) {
    $wp_upload_dir = wp_upload_dir();
    return str_replace($wp_upload_dir['baseurl'], $wp_upload_dir['basedir'], $url);
}

function url_from_file_path( $file_path ) {
    $wp_upload_dir = wp_upload_dir();
    return str_replace($wp_upload_dir['basedir'], $wp_upload_dir['baseurl'], $file_path);
}

function attach_media_to_post( $post_id, $image_url, $set_as_thumbnail=false, $title='', $content='', $status='inherit' ) {
    $filename = file_path_from_url( $image_url );
    $filetype = wp_check_filetype( basename( $filename ), null );
    $wp_upload_dir = wp_upload_dir();
    $attachment = array(
        'guid'           => $image_url, 
        'post_mime_type' => $filetype['type'],
        'post_title'     => $title,
        'post_content'   => $content,
        'post_status'    => $status
    );
    $attach_id = wp_insert_attachment( $attachment, $filename, $post_id );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
    wp_update_attachment_metadata( $attach_id, $attach_data );
    if( $set_as_thumbnail ) {
        set_post_thumbnail( $post_id, $attach_id );
    }
    return $attach_id;
}

function get_attach_ids_for_post( $post_id ) {
    $attach_ids = array();
    $existing_media = get_post_meta( $post_id, '_zmovies_attach_ids', true );
    if($existing_media) {
        $attach_ids = explode(",", $existing_media);
    }
    return $attach_ids;
}

/*
    -- Credit to: http://pastebin.com/kgLt1RrG
	$meta_key    - What meta key to check against (required)
	$post_type   - What post type to check (default - post)
	$fields      - Whether to query all the post table columns, or just a select one ... all, titles, ids, or guids (all returns an array of objects, others return an array of values)
*/
function posts_without_meta( $meta_key = '', $post_type = 'post', $fields = 'all' ) {
	global $wpdb;
	
	if( !isset( $meta_key ) || !isset( $post_type ) || !isset( $fields ) )
		return false;
	
	// Meta key is required
	if( empty( $meta_key ) )
		return false;
	
	// All parameters are expected to be strings
	if( !is_string( $meta_key ) || !is_string( $post_type ) || !is_string( $fields ) )
		return false;
	
	if( empty( $post_type ) )
		$post_type = 'post';

	if( empty( $fields ) )
		$fields = 'all';
	
	// Since all parameters are strings, bind them into one for a cheaper preg match (rather then doing one for each)
	$possibly_unsafe_text = $meta_key . $post_type . $fields;
	
	// Simply die if anything not a letter, number, underscore or hyphen is present
	if( preg_match( '/([^a-zA-Z0-9_-]+)/', $possibly_unsafe_text ) ) {
		wp_die( 'Invalid characters present in call to function (valid chars are a-z, 0-9, A-Z, underscores and hyphens).' );
		exit;
	}
	
	switch( $fields ) :
		case 'ids':
			$cols = 'p.ID';
			break;
		case 'titles':
			$cols = 'p.post_title';
			break;
		case 'guids':
			$cols = 'p.guid';
			break;
		case 'all':
		default:
			$cols = 'p.*';
			break;
	endswitch;
	
	if( 'all' == $fields )
		$result = $wpdb->get_results( $wpdb->prepare( "
			SELECT $cols FROM {$wpdb->posts} p
			WHERE NOT EXISTS
			(
				SELECT pm.* FROM {$wpdb->postmeta} pm
				WHERE p.ID = pm.post_id
				AND pm.meta_key = '%s'
			)
			AND p.post_type = '%s'
			", 
			$meta_key, 
			$post_type
		) );
	// get_col is nicer for single column selection (less data to traverse)
	else 
		$result = $wpdb->get_col( $wpdb->prepare( "
			SELECT $cols FROM {$wpdb->posts} p
			WHERE NOT EXISTS
			(
				SELECT pm.* FROM {$wpdb->postmeta} pm
				WHERE p.ID = pm.post_id
				AND pm.meta_key = '%s'
			)
			AND p.post_type = '%s'
			", 
			$meta_key, 
			$post_type
		) );
	
	return $result;
}

?>