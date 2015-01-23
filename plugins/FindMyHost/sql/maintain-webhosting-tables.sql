
-- preliminary queries on creating teh 

update-webhosts 1  ( do a join with existing before inserting to ensure that no dupes will be inserted)

-- this query ensures that sample name servers are actually part of the block and not mismatched
INSERT into `find_host_webhost_org_nserver` (
select 'null' as id,'null' as webhost_id ,`nserver`,`nserver_domain`,`ip_org_name`,`ip_org_id` from (
select `nserver`,`nserver_domain`,`ip_org_name`,`ip_org_id`, COUNT(*) TotalCount from `find_host_hostlookups` 
GROUP BY `nserver`,`nserver_domain`,`ip_org_name`,`ip_org_id`
HAVING COUNT(*) > 2
ORDER BY `ip_org_id` ASC
)nserver_dupes
Group By `nserver_domain`,`ip_org_name`,`ip_org_id`
)

update-webhosts 2 - sort by ip_org_name and delete dupes or add new

update-webhosts 3 
delete  from `find_host_webhost_org_nserver` where webhost_id='null'


select * from find_host_webhost_org_nserver order by webhost_id,ip_org_name

--this adds a unique key 
ALTER IGNORE TABLE `find_host_webhost_org_nserver` ADD UNIQUE INDEX `nserver_org` (`webhost_id`,`nserver_domain`,`ip_org_name`,`ip_org_id`);

-- create a unique index on webhost_id 
-- insert into webhosts table
-- this query ensures that sample name servers are actually part of the block and not mismatched
INSERT IGNORE into `find_host_webhosts` (
select null as id,`webhost_id`,`ip_org_name` as `display_name`,'null' as `home_page`,'null' as `affiliate_url`,'null' as `affiliate_id` from (
select `webhost_id`,`ip_org_name`
 from `find_host_webhost_org_nserver` 
)table_webhost_org_nserver
)


