DROP TABLE IF EXISTS `${PREFIX}group`;
CREATE TABLE `group` (
  `id` INT(11) NOT NULL AUTO_INCREMENT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `${PREFIX}student`;
CREATE TABLE `student` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

ALTER TABLE `${PREFIX}group`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `${PREFIX}student`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `${PREFIX}user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `name` (`name`);

DROP TABLE IF EXISTS `${PREFIX}group`;
CREATE TABLE `group_students_student` (
  `group_id` INT(11) NOT NULL,
  `student_id` INT(11) NOT NULL)
ALTER TABLE `group_students_student` ADD PRIMARY KEY(`group_id`, `student_id`);

DROP TABLE IF EXISTS `${PREFIX}student`;
CREATE TABLE `student_group_group` (
  `student_id` INT(11) NOT NULL,
  `group_id` INT(11) NOT NULL)
ALTER TABLE `student_group_group` ADD PRIMARY KEY(`student_id`, `group_id`);
