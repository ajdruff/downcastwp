-- run this query to update the webhost_org_nserver table. it will add any new ip_org/nserver_domain combinations it found

-- TODO : Turn this into a stored procedure and modulize it so its easier to understand
INSERT into `find_host_webhost_org_nserver` (

-- start select
SELECT 
0 as id,
'null' as `webhost_id` ,
`hostlookups`.`nserver_domain`,
`hostlookups`.`nserver`,
`hostlookups`.`ip_org_name`,
`hostlookups`.`ip_org_id`  FROM (
-- Gets sample nameserver for each org/nserver_domain group
-- and then uses a join to filter out ones we already have
select 
*
 from (
select 'null' as id,'null' as webhost_id ,`nserver`,`nserver_domain`,`ip_org_name`,`ip_org_id` from (
select `nserver`,`nserver_domain`,`ip_org_name`,`ip_org_id`, COUNT(*) TotalCount
 from `find_host_hostlookups` 
GROUP BY `nserver`,`nserver_domain`,`ip_org_name`,`ip_org_id`
HAVING COUNT(*) > 2
ORDER BY `ip_org_id` ASC
)unique_ip_blocks_with_example_nserver
Group By `nserver_domain`,`ip_org_name`,`ip_org_id`) unique_ip_blocks_with_example_nserver_grouped
)hostlookups
LEFT JOIN `find_host_webhost_org_nserver` webhost_org_nserver
ON hostlookups.nserver_domain = webhost_org_nserver.nserver_domain
WHERE hostlookups.nserver_domain IS null
OR webhost_org_nserver.nserver_domain IS null
-- end select
)
--end insert



------------------------------------------

-- Explanation: See the associated _readme.sql file for an explanation of this query
