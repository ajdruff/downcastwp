CREATE TABLE `find_host_hostlookups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `webhost_guess` varchar(255) DEFAULT NULL,
  `nserver_domain` varchar(255) DEFAULT NULL,
  `nserver` varchar(255) DEFAULT NULL,
  `registrar` varchar(255) DEFAULT NULL,
  `registrar_url` varchar(255) DEFAULT NULL,
  `registrar_iana_id` varchar(255) DEFAULT NULL,
  `registrar_reseller` varchar(255) DEFAULT NULL,
  `ip_org_name` varchar(255) DEFAULT NULL,
  `ip_org_id` varchar(255) DEFAULT NULL,
  `ip_net_name` varchar(255) DEFAULT NULL,
  `ip_net_handle` varchar(255) DEFAULT NULL,
  `ip_net_type` varchar(255) DEFAULT NULL,
  `ip_origin_as` varchar(255) DEFAULT NULL,
  `bgp_as_number` varchar(255) DEFAULT NULL,
  `bgp_as_name` varchar(255) DEFAULT NULL,
  `asn_org_name` varchar(255) DEFAULT NULL,
  `asn_org_id` varchar(255) DEFAULT NULL,
  `asn_as_number` varchar(255) DEFAULT NULL,
  `asn_as_name` varchar(255) DEFAULT NULL,
  `asn_as_handle` varchar(255) DEFAULT NULL,
  `mark_for_delete` enum('Delete') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3344 DEFAULT CHARSET=latin1;


