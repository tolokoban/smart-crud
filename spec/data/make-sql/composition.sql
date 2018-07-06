DROP TABLE IF EXISTS `${PREFIX}Group`;
CREATE TABLE `Group` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}Student`;
CREATE TABLE `Student` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}User`;
CREATE TABLE `User` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(256),
  `password` VARCHAR(256),
  `name` VARCHAR(256),
  `roles` VARCHAR(512) DEFAULT '[]',
  `enabled` TINYINT(1),
  `creation` CHAR(14),
  `data` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `${PREFIX}Group`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `${PREFIX}Student`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `${PREFIX}User`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `name` (`name`);

DROP TABLE IF EXISTS `${PREFIX}Group_Student`;
CREATE TABLE `Group_Student` (
  `students` INT(11) NOT NULL,
  `group` INT(11) NOT NULL)
ALTER TABLE `Group_Student` ADD PRIMARY KEY(`students`, `group`);
