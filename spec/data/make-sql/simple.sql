DROP TABLE IF EXISTS `${PREFIX}user`;
CREATE TABLE `${PREFIX}user` (
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
