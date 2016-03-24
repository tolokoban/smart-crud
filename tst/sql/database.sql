DROP TABLE IF EXISTS `Doctor`;
CREATE TABLE `Doctor` (
  `name` VARCHAR(255)
);

DROP TABLE IF EXISTS `Doctor_Visit`;
CREATE TABLE `Doctor_Visit` (
  `doctor` INT,
  `visit` INT
);

DROP TABLE IF EXISTS `Patient`;
CREATE TABLE `Patient` (
  `name` VARCHAR(255),
  `age` INT,
  `gender` ENUM('MALE', 'FEMALE', 'OTHER')
);

DROP TABLE IF EXISTS `Visit`;
CREATE TABLE `Visit` (
  `date` CHAR(14),
  `status` ENUM('ASKED', 'PROPOSED', 'VALIDATED')
);

DROP TABLE IF EXISTS `Patient_Visit`;
CREATE TABLE `Patient_Visit` (
  `visit` INT,
  `patient` INT
);

