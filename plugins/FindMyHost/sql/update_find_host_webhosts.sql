-- run this query to update the webhost_org_nserver table. it will add any new ip_org/nserver_domain combinations it found

-- TODO : Turn this into a stored procedure and modulize it so its easier to understand
-- todo: fix this query so that before your run it, you edit any nulls with the id of the webhost, then run it, then go into webhosts to add any urls and details of the host.
-- this query updates the find_host_webhosts table with any new hosts found.
-- after its run, you need to edit `find_host_webhost_org_nserver` and replace all nulls in webhost_id with the id of the host, and then update the find_host_webhost_org_nserver with any new hosts (or run an additional query) 
INSERT  into `find_host_webhosts` (
select 
null as id,
concat(`ip_org_name`,'_',`nserver_domain`) as `webhost_id`,
`ip_org_name` as `display_name`,
null as `home_page`,
null as `affiliate_url`,
null as `affiliate_id`,
null as `notes`  
from 
(
select `webhost_id`,`ip_org_name`,`nserver_domain`
 from `find_host_webhost_org_nserver` 
where webhost_id="null"
)table_webhost_org_nserver
)


