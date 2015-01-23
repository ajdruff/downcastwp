
-- Main query that matches whois results with webhost.

-- sample webhost lookup query. 
select `display_name` from (
select `webhosts`.* , `whois`.`nserver_domain`,`whois`.`ip_org_name`,`whois`.`ip_org_id` from 
`find_host_webhosts` webhosts
inner join `find_host_webhost_org_nserver` `whois`
on `webhosts`.`webhost_id`=`whois`.`webhost_id`
) table_c
where `ip_org_name` = 'Unified Layer'
and `nserver_domain`='bluehost.com'
and `ip_org_id`='BLUEH-2';
    
select `display_name` from (
select `webhosts`.* , `whois`.`nserver_domain`,`whois`.`ip_org_name`,`whois`.`ip_org_id` from 
`find_host_webhosts` webhosts
inner join `find_host_webhost_org_nserver` `whois`
on `webhosts`.`webhost_id`=`whois`.`webhost_id`
) table_c
where `ip_org_name` = 'unknown'
and `nserver_domain`='liquidweb.com'
and `ip_org_id`='unknown';


select `display_name` from (
select `webhosts`.* , `whois`.`nserver_domain`,`whois`.`ip_org_name`,`whois`.`ip_org_id` from 
`find_host_webhosts` webhosts
inner join `find_host_webhost_org_nserver` `whois`
on `webhosts`.`webhost_id`=`whois`.`webhost_id`
) table_c
where `ip_org_name` = 'Rackspace Hosting'
and `nserver_domain`='cpostores.com'
and `ip_org_id`='RACKS-8';

