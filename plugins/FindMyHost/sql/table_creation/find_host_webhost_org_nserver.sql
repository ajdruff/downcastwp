CREATE TABLE `find_host_webhost_org_nserver` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webhost_id` varchar(255) DEFAULT NULL,
  `nserver` varchar(255) DEFAULT NULL,
  `nserver_domain` varchar(255) DEFAULT NULL,
  `ip_org_name` varchar(255) DEFAULT NULL,
  `ip_org_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nserver_org` (`webhost_id`,`nserver_domain`,`ip_org_name`,`ip_org_id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=latin1;


