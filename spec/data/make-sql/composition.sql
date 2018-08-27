DROP TABLE IF EXISTS `${PREFIX}group`;
CREATE TABLE `${PREFIX}group` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}student`;
CREATE TABLE `${PREFIX}student` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  `group` INT(11),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}teacher`;
CREATE TABLE `${PREFIX}teacher` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

CREATE TABLE `${PREFIX}Group_Teacher` (
  `Teacher` INT(11) NOT NULL,
  `Group` INT(11) NOT NULL,
  PRIMARY KEY (`Teacher`, `Group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `${PREFIX}Group_Teacher`
  ADD FOREIGN KEY (`Teacher`) REFERENCES `${PREFIX}teacher`(id) ON DELETE CASCADE,
  ADD FOREIGN KEY (`Group`) REFERENCES `${PREFIX}group`(id) ON DELETE CASCADE;

CREATE TABLE `${PREFIX}Group_Teacher_2` (
  `Teacher` INT(11) NOT NULL,
  `Group` INT(11) NOT NULL,
  PRIMARY KEY (`Teacher`, `Group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `${PREFIX}Group_Teacher_2`
  ADD FOREIGN KEY (`Teacher`) REFERENCES `${PREFIX}teacher`(id) ON DELETE CASCADE,
  ADD FOREIGN KEY (`Group`) REFERENCES `${PREFIX}group`(id) ON DELETE CASCADE;

ALTER TABLE `${PREFIX}student`
  ADD FOREIGN KEY (`group`) REFERENCES `${PREFIX}group`(id) ON DELETE CASCADE;


INSERT INTO `${PREFIX}user` (`id`, `login`, `password`, `name`, `roles`, `enabled`, `creation`, `data`) VALUES
(1, '${USER}', '${PASSWORD}', 'Administrator', '["USER", "POWER", "ADMIN"]', 1, ${DATE}, '{}');
