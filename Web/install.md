- set CI settings databse , session , ...

- if you use database for your sessions, create a session table:

CREATE TABLE IF NOT EXISTS `burge_cmf_sessions` ( 
	`id` varchar(40) NOT NULL, 
	`ip_address` varchar(45) NOT NULL,
	`timestamp` int(10) unsigned DEFAULT 0 NOT NULL, 
	`data` blob NOT NULL, PRIMARY KEY (id), 
	KEY `ci_sessions_timestamp` (`timestamp`)
) 

- Configuration:
	a) Set database name, username, and password
	b) Set 'VISITOR_TRACKING_COOKIE_NAME','TRACKING_ENCRYPTION_KEY','TRACKING_IV'
	
- Set mail configs in application/helper/init_helper in burge_cmf_send_mail function

- Install the Burge CMF:
your_address/admin/install

- Enjoy developing;

- After developing , change ENVIRONMENT constant to 'production';

- Enjoy using your app;

