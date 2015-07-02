DROP TABLE IF EXISTS `upload_file`;
CREATE TABLE `upload_file` (
  `id` int(11) NOT NULL auto_increment,
  `token_upload` int(11) NOT NULL,
  `token_download` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `uploaded` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `upload_token`;
CREATE TABLE `upload_token` (
  `id` int(11) NOT NULL auto_increment,
  `token` varchar(6) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
