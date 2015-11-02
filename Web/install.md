1) set CI settings databse , session , ...

2) if you use database for your sessions, create a session table:

CREATE TABLE IF NOT EXISTS `burge_cmf_sessions` ( 
	`id` varchar(40) NOT NULL, 
	`ip_address` varchar(45) NOT NULL,
	`timestamp` int(10) unsigned DEFAULT 0 NOT NULL, 
	`data` blob NOT NULL, PRIMARY KEY (id), 
	KEY `ci_sessions_timestamp` (`timestamp`)
) 

3) Configuration:
	a) Set database name, username, and password
	b) Set 'VISITOR_TRACKING_COOKIE_NAME','TRACKING_ENCRYPTION_KEY','TRACKING_IV'
	

4) install the Burge CMF:
your_address/admin/install

5) Enjoy developing;

6) After developing , change ENVIRONMENT constant to 'production';

7) Enjoy using your app;

