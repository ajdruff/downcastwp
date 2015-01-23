-- run this query to update the webhost_org_nserver table. it will add any new ip_org/nserver_domain combinations it found

-- TODO : Turn this into a stored procedure and modulize it so its easier to understand
INSERT into `find_host_webhost_org_nserver` (

-- start select
SELECT 
<FIELD_LIST> FROM (
SUB_QUERY_A
)TABLEA
LEFT JOIN `find_host_webhost_org_nserver` TABLEB
ON TABLEA.nserver_domain = TABLEB.nserver_domain
WHERE TABLEA.nserver_domain IS null
OR TABLEB.nserver_domain IS null
-- end select
)
--end insert



-- <FIELD_LIST>
-- these are the fields that you want to have inserted into the table
0 as id,
'null' as `webhost_id` ,
`hostlookups`.`nserver_domain`,
`hostlookups`.`nserver`,
`hostlookups`.`ip_org_name`,
`hostlookups`.`ip_org_id` 

-- SUB_QUERY_A
-- Finds Unique ip_org_name/nserver_domain combinations with a sample nserver from polled hostlookups data
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




------------------------------------------

-- Explanation: 
