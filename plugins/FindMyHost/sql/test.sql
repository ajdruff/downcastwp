Insert into find_host_hostlookups (`nserver_domain`,`nserver`,`domain`,`ip`,`registrar`,`registrar_url`,`registrar_iana_id`,`registrar_reseller`,`ip_org_name`,`ip_org_id`,`ip_net_name`,`ip_net_handle`,`ip_net_type`,`ip_origin_as`,`bgp_as_number`,`bgp_as_name`,`asn_org_name`,`asn_org_id`,`asn_as_number`,`asn_as_name`,`asn_as_handle`,`webhost_guess`) VALUES ('worldnic.com','ns57.worldnic.com','stayvietnam.com','184.73.224.210','NETWORK SOLUTIONS, LLC.','unknown','unknown','unknown','Amazon.com, Inc.','AMAZO-4','AMAZON-EC2-7','NET-184-72-0-0-1','Direct Allocation','unknown','14618','Amazon.com, Inc.','Amazon.com, Inc.','unknown','14618','AMAZON-AES','AS14618','Amazon.com, Inc.') ;

select * from find_host_hostlookups;





Insert into find_host_hostlookups 
(
`nserver_domain`,`nserver`,`domain`,`ip`,`registrar`,`registrar_url`,`registrar_iana_id`,`registrar_reseller`,`ip_org_name`,`ip_org_id`,`ip_net_name`,
`ip_net_handle`,`ip_net_type`,`ip_origin_as`,`bgp_as_number`,`bgp_as_name`,`asn_org_name`,`asn_org_id`,`asn_as_number`,
`asn_as_name`,`asn_as_handle`,`webhost_guess`
) 



VALUES (
'worldnic.com',
'ns57.worldnic.com',
'stayvietnam.com',
'184.73.224.210',
'NETWORK SOLUTIONS, LLC.','unknown','unknown','unknown','Amazon.com, Inc.','AMAZO-4','AMAZON-EC2-7','NET-184-72-0-0-1','Direct Allocation','unknown','14618','Amazon.com, Inc.','Amazon.com, Inc.','unknown','14618','AMAZON-AES','AS14618','Amazon.com, Inc.'

) ;

