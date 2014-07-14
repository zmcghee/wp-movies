<?php

$data_saved = false;

function add_or_update_post_meta( $post_id, $key, $value ) {
    if ( ! update_post_meta ($post_id, $key, $value, false) ) { 
        add_post_meta($post_id, $key, $value, false);	
    }; 
}

function is_featured_image( $poster_or_backdrop, $movie ) {
    $setting = get_option('zmovies_featured_image');
    if($setting == 'backdrop then poster') {
        if(($poster_or_backdrop=='backdrop' && $movie->backdrop_path) || ($poster_or_backdrop=='poster' && !$movie->backdrop_path)) {
            return true;
        }
        return false;
    } else if($setting == 'poster then backdrop') {
        if(($poster_or_backdrop=='poster' && $movie->poster_path) || ($poster_or_backdrop=='backdrop' && !$movie->poster_path)) {
            return true;
        }
        return false;
    } else {
        return false;
    }
}

if( isset($_POST['posts']) ) { ?>

<div class="updated" id="importing-message">
    <p><strong><?php _e('Importing... Please do not navigate away from this page.', 'menu-test' ); ?></strong></p>
</div>

<?php

    foreach($_POST['posts'] as $post) {
    
        if(empty($post)) {
            continue;
        }
    
        $parts = explode("|", $post);
        $post_id = $parts[0];
        $tmdb_id = $parts[1];
        
        // Store TMDb ID
        add_or_update_post_meta( $post_id, 'tmdb_id', $tmdb_id );
        
        // Copy images and store JSON
        $movie = Movies::TMDb($tmdb_id);
        $json = $movie->json($copy_images=true);
        add_or_update_post_meta( $post_id, '_zmovies_json', $json );
        
        // Fetch movie data back from WP
        $movie = new Movie( $post_id );
        
        // Attach media
        if( trim(get_option('zmovies_attach_media')) == 'y' ) {
            $attach_ids = get_attach_ids_for_post( $post_id );
            if($movie->backdrop_path) {
                $attach_id = attach_media_to_post( $post_id, $movie->backdrop_path, is_featured_image('backdrop', $movie), $movie->title );
                $attach_ids[] = $attach_id;
            }
            if($movie->poster_path) {
                $attach_id = attach_media_to_post( $post_id, $movie->poster_path, is_featured_image('poster', $movie), $movie->title );
                $attach_ids[] = $attach_id;
            }
            add_or_update_post_meta( $post_id, '_zmovies_attach_ids', implode(",", $attach_ids) );
        }

    }

    $data_saved = true;

}

?>