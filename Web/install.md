1) set CI settings databse , session , ...

2) if you use database for your sessions, create a session table:

CREATE TABLE IF NOT EXISTS `burge_cmf_sessions` ( 
	`id` varchar(40) NOT NULL, 
	`ip_address` varchar(45) NOT NULL,
	`timestamp` int(10) unsigned DEFAULT 0 NOT NULL, 
	`data` blob NOT NULL, PRIMARY KEY (id), 
	KEY `ci_sessions_timestamp` (`timestamp`)
) 

3) install the Burge CMF:
your_address/admin/install

4) Enjoy developing;

5) After developing , change ENVIRONMENT constant to 'production';

6) Enjoy using your app;

