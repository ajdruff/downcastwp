CREATE TABLE `find_host_webhosts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webhost_id` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `home_page` varchar(255) DEFAULT NULL,
  `affiliate_url` varchar(255) DEFAULT NULL,
  `affiliate_id` varchar(255) DEFAULT NULL,
  `notes` tinytext,
  PRIMARY KEY (`id`,`webhost_id`),
  UNIQUE KEY `webhost_id` (`webhost_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;


