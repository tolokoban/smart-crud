DROP TABLE IF EXISTS `${PREFIX}user`;
CREATE TABLE `user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(256),
  `password` VARCHAR(256),
  `name` VARCHAR(256),
  `roles` VARCHAR(512) DEFAULT '[]',
  `enabled` TINYINT(1),
  `creation` CHAR(14),
  `data` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `${PREFIX}user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `name` (`name`);


