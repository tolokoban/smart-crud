DROP TABLE IF EXISTS `Doctor`;
CREATE TABLE `Doctor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255)
);

DROP TABLE IF EXISTS `Doctor_Visit`;
CREATE TABLE `Doctor_Visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor` INT,
  `visit` INT
);

DROP TABLE IF EXISTS `Patient`;
CREATE TABLE `Patient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255),
  `age` INT,
  `gender` ENUM('MALE', 'FEMALE', 'OTHER')
);

DROP TABLE IF EXISTS `Visit`;
CREATE TABLE `Visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` CHAR(14),
  `status` ENUM('ASKED', 'PROPOSED', 'VALIDATED')
);

DROP TABLE IF EXISTS `Patient_Visit`;
CREATE TABLE `Patient_Visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit` INT,
  `patient` INT
);

