DROP TABLE IF EXISTS `test_Tag`;
CREATE TABLE `test_Tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  `name` VARCHAR(255)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `test_Issue_Tag`;
CREATE TABLE `test_Issue_Tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  `tag` INT,
  `issue` INT
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `test_Issue`;
CREATE TABLE `test_Issue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  `title` VARCHAR(255),
  `content` TEXT,
  `author` INT,
  `date` CHAR(14),
  `status` ENUM('OPEN', 'FIXED', 'CLOSED'),
  `type` ENUM('BUG', 'FEATURE')
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `test_Comment`;
CREATE TABLE `test_Comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  `content` TEXT,
  `author` INT,
  `date` CHAR(14),
  `issue` INT
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `test_Vote`;
CREATE TABLE `test_Vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  `user` INT,
  `issue` INT,
  `vote` INT
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `test_User`;
CREATE TABLE `test_User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  `login` VARCHAR(255),
  `password` VARCHAR(255),
  `name` VARCHAR(255),
  `comment` TEXT,
  `roles` TEXT,
  `enabled` INT,
  `creation` CHAR(14)
) DEFAULT CHARSET=utf8;

