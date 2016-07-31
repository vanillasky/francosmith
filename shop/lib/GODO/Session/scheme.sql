CREATE TABLE `gd_session` (
`id` char(32) NOT NULL,
`data` text NOT NULL default '',
`expire` int(11) unsigned NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=euckr;