<?php

$data_saved = false;

function add_or_update_post_meta( $post_id, $key, $value ) {
    if ( ! update_post_meta ($post_id, $key, $value, false) ) { 
        add_post_meta($post_id, $key, $value, false);	
    }; 
}

if( isset($_POST['posts']) ) {

    foreach($_POST['posts'] as $post) {
    
        if(empty($post)) {
            continue;
        }
    
        $parts = explode("|", $post);
        $post_id = $parts[0];
        $tmdb_id = $parts[1];
        
        add_or_update_post_meta( $post_id, 'tmdb_id', $tmdb_id );
        
        $movie = Movies::TMDb($tmdb_id);
        $json = $movie->json($copy_images=true);
        add_or_update_post_meta( $post_id, '_zmovies_json', $json );

    }

    $data_saved = true;

}

?>