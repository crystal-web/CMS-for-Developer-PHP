<?php
/**
* @title Connection
* @author Christophe BUFFET <developpeur@crystal-web.org> 
* @license Creative Commons By 
* @license http://creativecommons.org/licenses/by-nd/3.0/
* @description 
*/
Class MemberGroupModel extends Model{
	public function install()
	{
	$this->query("CREATE TABLE IF NOT EXISTS `".__SQL."_Member` (
  `idmember` int(11) NOT NULL AUTO_INCREMENT,
  `loginmember` varchar(50) NOT NULL,
  `password` varchar(256) NOT NULL,
  `mailmember` varchar(256) NOT NULL,
  `validemember` enum('on','off') NOT NULL DEFAULT 'off',
  `levelmember` int(1) NOT NULL DEFAULT '1',
  `groupmember` text NOT NULL,
  `firstactivitymember` int(11) NOT NULL,
  `lastactivitymember` int(11) NOT NULL,
  `hash_validation` varchar(255) NOT NULL,
  `ip` varchar(256) NOT NULL,
  PRIMARY KEY (`idmember`),
  UNIQUE KEY `loginmember` (`loginmember`,`mailmember`),
  UNIQUE KEY `loginmember_2` (`loginmember`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `".__SQL."_MemberGroup` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `".__SQL."_MemberGroup` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
");

	}

}
?>