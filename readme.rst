===============
WordPress Movies Plugin
===============

Attach data (including movie posters and backdrop images) from
TheMovieDb.org to your posts or events in WordPress.

For screenshots, visit http://zmcghee.github.io/wp-movies/


Installation
============

Download this package and upload to plugins:
https://github.com/zmcghee/wp-movies/archive/master.zip

After you activate the plugin, configure it by going to the *Settings*
screen under the *Movies* menu in your admin dashboard. You'll need a
TMDb API key, which you can get at http://www.themoviedb.org/documentation/api


How does it work?
=================

To attach movie data to your posts, you use the *Import Tool* under the
*Movies* menu in your admin dashboard. The tool suggests movie matches
based on your post titles.

If you want to filter by a specific post type, you can configure that in
*Settings*, also in the *Movies* menu. For example, we use the Events
Calendar (Pro) plugin, which sets events to a post type of ``tribe_events``.

Unless you've configured it to do otherwise, the import tool will:

* Import a backdrop image for the matching movie if one is available,
  attach it to the post, and set it as the featured image.
* Import a poster image for the matching movie if one is available and
  attach it to the post. If no backdrop image was available, it will set
  the poster image as the featured image.
* Set a custom metadata field with the tmdb_id for the matching movie.
* Store some other data about the movie that you can use in your theme's
  templates (see below).


What other data can I use in my theme?
======================================

Once movie data is imported and attached to a post, it's stored as
custom metadata which is accessible through the Movie object.
A basic example looks like::

    $movie = new Movie( $post->ID );
    if( $movie->title )
        echo $movie->title;
    if( $movie->genres )
        echo implode(", ", $movie->genres);


Properties of every Movie object::

    $movie->tmdb_id
    $movie->title
    $movie->year  # this is parsed from the tmdb release date (if found)
    $movie->backdrop_path  # full url from your wp uploads dir
    $movie->poster_path  # full url from your wp uploads dir
    $movie->genres  # array in plain text, e.g. array('Action', 'Adventure')
    $movie->imdb_id
    $movie->runtime  # in minutes, e.g. 121
    $movie->languages  # array in plain text, e.g. array('English')
    $movie->overview  # tmdb's text synopsis of movie

Although every Movie object has these properties, they may be empty
depending on the source data. (If you haven't imported any movie data to
a post, conjuring a Movie object from that post will also return a valid
Movie object with all empty values.)


Can I call the TMDb API directly?
=================================

I certainly don't recommend doing it on request in your theme's templates,
but if you want to ping the TMDb API directly for additional data, you can
use ``Movies::$TMDB``, an instance of TMDB_V3_API_PHP, for that.

Here's a quick example::

    $search = Movies::$TMDB->searchMovie("Back to the Future");
    foreach($search['results'] as $result) {
        echo $result['title'];
    }

Documentation for that library is available at
https://github.com/pixelead0/tmdb_v3-PHP-API-/


About This Version
==================

This is the first version of this plugin. It hasn't been tested
thoroughly, although so far it works for me. There is some code
cleanup to be done, especially in the admin screens. I'd like to
add tests, I just have no experience with testing WP plugins.


Credits & License
=================

This package is licensed under the BSD license. A copy of this license
is enclosed in license.txt. More information at http://opensource.org/licenses/bsd-license.php

This package includes pixelead0's TMDB API v3 PHP class, which in turn
is based in part on Jonas De Smet's TMDb PHP API class. Both are BSD
licensed and redistributed here under the terms of that license.

This package also includes a function posted anonymously to Pastebin
at http://pastebin.com/kgLt1RrG. My gratitude to its author.