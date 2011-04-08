# CodeIgniter Feedburner Library

This library gets a site's Feedburner meta-data information and caches it to a database. Currently these:

* Circulation
* Reach
* Hits

This is useful if don't want to use the default Feedburner's graphic button that just displays the number of subscribers to your feed, but actually more meaningful information in a plain-text format that can be actually styled to your site's looks.

Currently tested on the 1.7.x branch of the CodeIgniter PHP framework.

## Setup

This library caches the Feedburner response for a given time (in seconds) so not to hit the Feedburner API servers on every request. The caching is done to a MySQL table; create it using the dump in the feeds.sql file or with the following schema definition:

    CREATE TABLE IF NOT EXISTS feeds (
      id int(11) unsigned NOT NULL AUTO_INCREMENT,
      uri varchar(255) NOT NULL,
      `date` date NOT NULL,
      circulation int(11) unsigned NOT NULL DEFAULT '0',
      reach int(11) unsigned NOT NULL DEFAULT '0',
      hits int(11) unsigned NOT NULL DEFAULT '0',
      created_at datetime DEFAULT NULL,
      PRIMARY KEY (id),
      KEY uri (uri)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

## Installation

Place the Feedburner.php file in your `system/application/libraries` directory.

Load the library in your `system/application/config/autoload.php` file or directly in your controllers:

    $this->load->library('feedburner');

## Usage

Just use your Feedburner site **name** calling the `get_feed()` method in your controllers, views like so (example extracted from a live site):

    <? $feed = $this->feedburner->get_feed('vivalinux'); ?>
    <? if (is_array($feed) && array_key_exists('circulation',$feed)): ?>
      <div id="feed">
        <p class="rss">
          <strong><?= number_format($feed['circulation'],0,',','.') ?></strong> subscripciones por <a href="http://feeds.feedburner.com/vivalinux">RSS</a> o por <a href="http://feedburner.google.com/fb/a/mailverify?uri=vivalinux&amp;loc=es_ES">E-mail</a>
        </p>
      </div>
    <? endif; ?>

`get_feed()` gets, updates or returns the cached meta-data of the Feedburner site's feed. In the example above, for http://feeds.feedburner.com/vivalinux

## Credits

API reference thanks to:

* http://code.google.com/apis/feedburner/awareness_api.html
* http://visionmasterdesigns.com/tutoral-display-noof-rss-readers-using-feedburner-api-and-php-curl/

Other CodeIgniter resources into own libraries tip thanks to:

* http://zeratool.com/blog/2008/02/03/creating-your-own-library-in-codeigniter/

&copy;2010 Cristian R. Arroyo [cristian.arroyo@vivaserver.com]
