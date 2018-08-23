DROP TABLE IF EXISTS `${PREFIX}User`;
CREATE TABLE `${PREFIX}User` (
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

DROP TABLE IF EXISTS `${PREFIX}Organization`;
CREATE TABLE `${PREFIX}Organization` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}Carecenter`;
CREATE TABLE `${PREFIX}Carecenter` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  `organization` INT(11),
  `structure` INT(11),
  FOREIGN KEY (`organization`) REFERENCES `Organization`(id) ON DELETE CASCADE,
  FOREIGN KEY (`structure`) REFERENCES `Structure`(id),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}Structure`;
CREATE TABLE `${PREFIX}Structure` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  `exams` TEXT,
  `vaccins` TEXT,
  `patient` TEXT,
  `forms` TEXT,
  `types` TEXT,
  `organization` INT(11),
  FOREIGN KEY (`organization`) REFERENCES `Organization`(id) ON DELETE CASCADE,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}Patient`;
CREATE TABLE `${PREFIX}Patient` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(256),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}PatientField`;
CREATE TABLE `${PREFIX}PatientField` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(256),
  `value` TEXT,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}File`;
CREATE TABLE `${PREFIX}File` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  `hash` VARCHAR(256),
  `mime` VARCHAR(256),
  `size` INT(11),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}Admission`;
CREATE TABLE `${PREFIX}Admission` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `enter` CHAR(14),
  `exit` CHAR(14),
  `patient` INT(11),
  FOREIGN KEY (`patient`) REFERENCES `Patient`(id) ON DELETE CASCADE,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}Consultation`;
CREATE TABLE `${PREFIX}Consultation` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `date` CHAR(14),
  `admission` INT(11),
  FOREIGN KEY (`admission`) REFERENCES `Admission`(id) ON DELETE CASCADE,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}Data`;
CREATE TABLE `${PREFIX}Data` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(256),
  `value` TEXT,
  `consultation` INT(11),
  FOREIGN KEY (`consultation`) REFERENCES `Consultation`(id) ON DELETE CASCADE,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}Shapshot`;
CREATE TABLE `${PREFIX}Shapshot` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(256),
  `value` TEXT,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}Attachment`;
CREATE TABLE `${PREFIX}Attachment` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256),
  `desc` VARCHAR(256),
  `date` CHAR(14),
  `mime` VARCHAR(256),
  `patient` INT(11),
  FOREIGN KEY (`patient`) REFERENCES `Patient`(id) ON DELETE CASCADE,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}Vaccin`;
CREATE TABLE `${PREFIX}Vaccin` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(256),
  `date` CHAR(14),
  `lot` VARCHAR(256),
  `patient` INT(11),
  FOREIGN KEY (`patient`) REFERENCES `Patient`(id) ON DELETE CASCADE,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `${PREFIX}user` (`id`, `login`, `password`, `name`, `roles`, `enabled`, `creation`, `data`) VALUES
(1, '${USER}', '${PASSWORD}', 'Administrator', '["USER", "POWER", "ADMIN"]', 1, ${DATE}, '{}');
