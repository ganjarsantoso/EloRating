CREATE TABLE `members` (
	`id` int(6) NOT NULL auto_increment,
	`nama` varchar(35) NOT NULL,
	`URLfoto` longtext NOT NULL,
	`EloPoint` float NOT NULL,
	`P` int(6) NOT NULL,
	`W` int(6) NOT NULL,
	`D` int(6) NOT NULL,
	`L` int(6) NOT NULL,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;