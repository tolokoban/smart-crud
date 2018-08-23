DROP TABLE IF EXISTS `${PREFIX}Group`;
CREATE TABLE `${PREFIX}Group` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}Student`;
CREATE TABLE `${PREFIX}Student` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  `group` INT(11),
  FOREIGN KEY (`group`) REFERENCES `Group`(id) ON DELETE CASCADE,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}User`;
CREATE TABLE `${PREFIX}User` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(256),
  `password` VARCHAR(256),
  `name` VARCHAR(256),
  `roles` VARCHAR(512) DEFAULT '[]',
  `enabled` TINYINT(1),
  `creation` CHAR(14),
  `data` TEXT,
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `name` (`name`),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `${PREFIX}user` (`id`, `login`, `password`, `name`, `roles`, `enabled`, `creation`, `data`) VALUES
(1, '${USER}', '${PASSWORD}', 'Administrator', '["USER", "POWER", "ADMIN"]', 1, ${DATE}, '{}');
