DROP TABLE IF EXISTS `${PREFIX}user`;
CREATE TABLE `${PREFIX}user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `dashboard` TEXT,
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

DROP TABLE IF EXISTS `${PREFIX}organization`;
CREATE TABLE `${PREFIX}organization` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}structure`;
CREATE TABLE `${PREFIX}structure` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  `exams` TEXT,
  `vaccins` TEXT,
  `patient` TEXT,
  `forms` TEXT,
  `types` TEXT,
  `organization` INT(11),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}carecenter`;
CREATE TABLE `${PREFIX}carecenter` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  `code` VARCHAR(256),
  `organization` INT(11),
  `structure` INT(11),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}patient`;
CREATE TABLE `${PREFIX}patient` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(256),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}patientField`;
CREATE TABLE `${PREFIX}patientField` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(256),
  `value` TEXT,
  `patient` INT(11),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}file`;
CREATE TABLE `${PREFIX}file` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  `hash` VARCHAR(256),
  `mime` VARCHAR(256),
  `size` INT(11),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}admission`;
CREATE TABLE `${PREFIX}admission` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `enter` CHAR(14),
  `exit` CHAR(14),
  `patient` INT(11),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}consultation`;
CREATE TABLE `${PREFIX}consultation` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `date` CHAR(14),
  `admission` INT(11),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}data`;
CREATE TABLE `${PREFIX}data` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(256),
  `value` TEXT,
  `consultation` INT(11),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}shapshot`;
CREATE TABLE `${PREFIX}shapshot` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(256),
  `value` TEXT,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}attachment`;
CREATE TABLE `${PREFIX}attachment` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  `desc` VARCHAR(256),
  `date` CHAR(14),
  `mime` VARCHAR(256),
  `patient` INT(11),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}vaccin`;
CREATE TABLE `${PREFIX}vaccin` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(256),
  `date` CHAR(14),
  `lot` VARCHAR(256),
  `patient` INT(11),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `${PREFIX}Organization_User` (
  `User` INT(11) NOT NULL,
  `Organization` INT(11) NOT NULL,
  PRIMARY KEY (`User`, `Organization`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `${PREFIX}Organization_User`
  ADD FOREIGN KEY (`User`) REFERENCES `${PREFIX}user`(id) ON DELETE CASCADE,
  ADD FOREIGN KEY (`Organization`) REFERENCES `${PREFIX}organization`(id) ON DELETE CASCADE;

CREATE TABLE `${PREFIX}Carecenter_User` (
  `User` INT(11) NOT NULL,
  `Carecenter` INT(11) NOT NULL,
  PRIMARY KEY (`User`, `Carecenter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `${PREFIX}Carecenter_User`
  ADD FOREIGN KEY (`User`) REFERENCES `${PREFIX}user`(id) ON DELETE CASCADE,
  ADD FOREIGN KEY (`Carecenter`) REFERENCES `${PREFIX}carecenter`(id) ON DELETE CASCADE;

ALTER TABLE `${PREFIX}structure`
  ADD FOREIGN KEY (`organization`) REFERENCES `${PREFIX}organization`(id) ON DELETE CASCADE;

ALTER TABLE `${PREFIX}carecenter`
  ADD FOREIGN KEY (`organization`) REFERENCES `${PREFIX}organization`(id) ON DELETE CASCADE,
  ADD FOREIGN KEY (`structure`) REFERENCES `${PREFIX}structure`(id);

ALTER TABLE `${PREFIX}patientField`
  ADD FOREIGN KEY (`patient`) REFERENCES `${PREFIX}patient`(id) ON DELETE CASCADE;

ALTER TABLE `${PREFIX}admission`
  ADD FOREIGN KEY (`patient`) REFERENCES `${PREFIX}patient`(id) ON DELETE CASCADE;

ALTER TABLE `${PREFIX}consultation`
  ADD FOREIGN KEY (`admission`) REFERENCES `${PREFIX}admission`(id) ON DELETE CASCADE;

ALTER TABLE `${PREFIX}data`
  ADD FOREIGN KEY (`consultation`) REFERENCES `${PREFIX}consultation`(id) ON DELETE CASCADE;

ALTER TABLE `${PREFIX}attachment`
  ADD FOREIGN KEY (`patient`) REFERENCES `${PREFIX}patient`(id) ON DELETE CASCADE;

ALTER TABLE `${PREFIX}vaccin`
  ADD FOREIGN KEY (`patient`) REFERENCES `${PREFIX}patient`(id) ON DELETE CASCADE;


INSERT INTO `${PREFIX}user` (`id`, `login`, `password`, `name`, `roles`, `enabled`, `creation`, `data`) VALUES
(1, '${USER}', '${PASSWORD}', 'Administrator', '["USER", "POWER", "ADMIN"]', 1, ${DATE}, '{}');