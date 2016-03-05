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
- Set emai customer env header, name, keywords, and description in language/*/ce_general_lang.php, language/*/ae_general_lang.php, and also email template  language/*/email_lang.php

- Install the Burge CMF:
your_address/admin/install

- Enjoy developing;

- After developing , change ENVIRONMENT constant to 'production';

- Change $config['log_threshold'] from 4 to 1;

- Enjoy using your app;

