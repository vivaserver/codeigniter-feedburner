DROP TABLE IF EXISTS feeds;
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
