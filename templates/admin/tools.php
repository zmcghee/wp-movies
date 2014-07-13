<?php

//must check that the user has the required capability 
if (!current_user_can('manage_options'))
{
  wp_die( __('You do not have sufficient permissions to access this page.') );
}

$operation_completed = false;
$operation_error = false;

if( isset($_POST['operation']) ) {
    if( in_array($_POST['operation'], array('clear_for_post_id', 'clear_for_tmdb_id')) ) {
        if( !isset($_POST['val']) || empty($_POST['val']) ) {
            $operation_error = true;
            $error_msg = "You didn't enter an ID.";
        } else {
            if( $_POST['operation'] == 'clear_for_tmdb_id' ) {
                Movies::clear_data_for_tmdb_id( $_POST['val'] );
                $operation_completed = true;
            } else if( $_POST['operation'] == 'clear_for_post_id' ) {
                Movies::clear_data_for_post( $_POST['val'] );
                $operation_completed = true;
            }
        }
    } else if( $_POST['operation'] == 'clear_all_movie_data' ) {
        Movies::clear_all_data();
        $operation_completed = true;
    } else if( $_POST['operation'] == 'restore_defaults' ) {
        Movies::restore_defaults();
        $operation_completed = true;
    }
}

if ( $operation_error ) { ?>
<div class="updated">
    <p><strong><?php _e("There was an error. $error_msg", 'menu-test' ); ?></strong></p>
</div>
<? } else if( $operation_completed ) { ?>
<div class="updated">
    <p><strong><?php _e('The operation was completed.', 'menu-test' ); ?></strong></p>
</div>
<?php } ?>

<div class="wrap">
    <h2><?php _e( 'Movies Plugin Tools', 'menu-test' ) ?></h2>
    <form method="post" action="" onsubmit="if(!confirm('Are you sure you want to do this?')){ return false; }">
        <p>
            <?php _e("Operation:"); ?> 
            <select name="operation">
                <option selected></option>
                <option value="clear_for_post_id">Clear movie data for a specific post ID</option>
                <option value="clear_for_tmdb_id">Clear movie data for a specific TMDb ID</option>
                <option value="clear_all_movie_data">Clear all movie data for all posts</option>
                <option value="restore_defaults">Restore plugin settings to default (except for TMDB API key)</option>
            </select>
        </p>
        <p>
            <?php _e("ID:"); ?> 
            <input type="text" name="val" value="" size="20"><br />
            <small>enter an ID when applicable; if not, leave blank</small>
        </p>
        <hr />
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Finish') ?>" />
        </p>
    </form>
</div>