<?php
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
/*
	Example - fetch posts with given meta, then pass to get_posts
	-----------------
	<?php
	// Find posts without a meta entry for the fruit custom field
	$posts_without_fruit = posts_without_meta( 'fruit', '', 'ids' );
	
	// If the result didn't come back false
	if( $posts_without_fruit )
	
		// Pass the IDs returned into get_posts
		$posts_without_meta = get_posts( 'include=' . implode( ',', $posts_without_fruit) );
	
	// Basic get_posts loop
	foreach( $posts_without_meta as $post ) :
		setup_postdata( $post );
		
		the_title();
		echo '<br />';
		
	endforeach;
	
	wp_reset_query();
	?>
*/
?>