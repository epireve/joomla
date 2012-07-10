CREATE TABLE IF NOT EXISTS `#__xipt_aclrules` (
 `id` int(31) NOT NULL AUTO_INCREMENT,
  `rulename` varchar(250) NOT NULL,
  `aclname` varchar(128) NOT NULL,
  `coreparams` text NOT NULL,
  `aclparams` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__xipt_aec` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `planid` int(11) NOT NULL,
  `profiletype` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__xipt_applications` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `applicationid` int(10) NOT NULL DEFAULT '0',
  `profiletype` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__xipt_profilefields` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fid` int(10) NOT NULL DEFAULT '0',
  `pid` int(10) NOT NULL DEFAULT '0',
  `category` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__xipt_profiletypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `ordering` int(10) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `tip` text NOT NULL,
  `privacy` varchar(20) NOT NULL DEFAULT 'friends',
  `template` varchar(50) NOT NULL DEFAULT 'default',
  `jusertype` varchar(50) NOT NULL DEFAULT 'Registered',
  `avatar` varchar(250) NOT NULL DEFAULT 'components/com_community/assets/user.png',
  `approve` tinyint(1) NOT NULL DEFAULT '0',
  `allowt` tinyint(1) NOT NULL DEFAULT '0',
  `group` int(11) NOT NULL DEFAULT '0',
  `watermark` varchar(250) NOT NULL,
  `params` text NOT NULL,
  `watermarkparams` text NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__xipt_users` (
  `userid` int(11) NOT NULL,
  `profiletype` int(10) NOT NULL DEFAULT '0',
  `template` varchar(80) NOT NULL DEFAULT 'NOT_DEFINED',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__xipt_settings` (
  `name` varchar(250) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__xipt_jstoolbar` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `menuid` int(10) NOT NULL DEFAULT '0',
  `profiletype` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__xipt_settings`
--

INSERT IGNORE INTO `#__xipt_settings` (`name`, `params`) VALUES
('settings', '');


