DROP TABLE IF EXISTS `tagfollower`;
DROP TABLE IF EXISTS `clubmember`;
DROP TABLE IF EXISTS `clubfollower`;
DROP TABLE IF EXISTS `eventfollower`;
DROP TABLE IF EXISTS `clubevent`;
DROP TABLE IF EXISTS `eventtag`;
DROP TABLE IF EXISTS `clubtag`;
DROP TABLE IF EXISTS `event`;
DROP TABLE IF EXISTS `club`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `tag`;


CREATE TABLE `user` (
  `user_id` int PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) NOT NULL,
  `acct_type` int(1) NOT NULL DEFAULT 1, -- Faculty = 0, Student = 1, Admin = 2
  `create_timestmp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updt_timestmp` datetime ON UPDATE CURRENT_TIMESTAMP,
  `verify` char(32),
  `is_inactive` bit(1) DEFAULT 1
);

CREATE TABLE `event` (
  `event_id` int PRIMARY KEY AUTO_INCREMENT,
  `event_name` varchar(255) NOT NULL,
  `event_desc` varchar(2048),
  `has_food` bit(1) DEFAULT 0,
  `event_time` datetime NOT NULL,
  `latitude` float,
  `longitude` float,
  `location` varchar(512),
  `duration` varchar(28),
  `external_url1` varchar(512),
  `external_url2` varchar(512),
  `external_url3` varchar(512),
  `photo_path` varchar(255),
  `create_timestmp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updt_timestmp` datetime ON UPDATE CURRENT_TIMESTAMP,
  `is_inactive` bit(1) DEFAULT 0,
  `event_contact` int NOT NULL
);

CREATE TABLE `club` (
  `club_id` int PRIMARY KEY AUTO_INCREMENT,
  `photo_path` varchar(255),
  `club_name` varchar(255),
  `club_desc` varchar(1024),
  `fac_sponsor_id` int,
  `create_timestmp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updt_timestmp` datetime ON UPDATE CURRENT_TIMESTAMP,
  `is_inactive` bit(1) DEFAULT 0
);



CREATE TABLE `clubmember` (
  `user_id` int,
  `club_id` int,
  `is_contact` bit(1) DEFAULT 0,
  `can_edit` bit(1) DEFAULT 0
);

CREATE TABLE `clubfollower` (
	`user_id` int,
	`club_id` int
);

CREATE TABLE `eventfollower` (
  `event_id` int,
  `user_id` int
);

CREATE TABLE `clubevent` (
  `event_id` int,
  `club_id` int
);

CREATE TABLE `eventtag` (
  `event_id` int,
  `tag_id` int
);

CREATE TABLE `clubtag` (
  `club_id` int,
  `tag_id` int
);
CREATE TABLE `tagfollower` (
  `user_id` int,
  `tag_id` int
);

CREATE TABLE `tag` (
  `tag_id` int PRIMARY KEY AUTO_INCREMENT,
  `tag` varchar(64) NOT NULL
);

ALTER TABLE `clubmember` ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

ALTER TABLE `clubmember` ADD FOREIGN KEY (`club_id`) REFERENCES `club` (`club_id`);

ALTER TABLE `clubevent` ADD FOREIGN KEY (`club_id`) REFERENCES `club` (`club_id`);

ALTER TABLE `clubevent` ADD FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`);

ALTER TABLE `eventfollower` ADD FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`);

ALTER TABLE `eventfollower` ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

ALTER TABLE `eventtag` ADD FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`);

ALTER TABLE `eventtag` ADD FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`);

ALTER TABLE `event` ADD FOREIGN KEY (`event_contact`) REFERENCES `user` (`user_id`);

ALTER TABLE `clubtag` ADD FOREIGN KEY (`club_id`) REFERENCES `club` (`club_id`);

ALTER TABLE `clubtag` ADD FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`);

ALTER TABLE `tagfollower` ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

ALTER TABLE `tagfollower` ADD FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`);

ALTER TABLE `clubfollower` ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

ALTER TABLE `clubfollower` ADD FOREIGN KEY (`club_id`) REFERENCES `club` (`club_id`);

CREATE INDEX idx_event ON `event`(event_id);
CREATE INDEX idx_user ON `user`(user_id);
CREATE INDEX idx_user ON `club`(club_id);
CREATE INDEX idx_tag ON `tag`(tag_id);
CREATE INDEX idx_clubmember on `clubmember` (club_id, user_id);
CREATE INDEX idx_clubtag on `clubtag` (club_id, tag_id);
CREATE INDEX idx_eventtag on `eventtag` (event_id, tag_id);
CREATE INDEX idx_usertag on `tagfollower` (user_id, tag_id);
CREATE INDEX idx_clubevent on `clubevent` (club_id, event_id);
CREATE INDEX idx_eventfollower on `eventfollower` (event_id, user_id);
CREATE INDEX idx_clubfollower on `clubfollower` (club_id, user_id);

INSERT INTO tag (tag_id, tag) VALUES
(1, 'Biology'),
(2, 'Mathematics'),
(3, 'Technology'),
(4, 'Art'),
(5, 'Science'),
(6, 'Performance'),
(7, 'Theater'),
(8, 'Chemistry'),
(9, 'Culture'),
(10, 'Cuisine'),
(11, 'Animals'),
(12, '21'),
(13, 'Official'),
(14, 'Party'),
(15, 'Greek'),
(16, 'Mechanics'),
(17, 'Engineering'),
(18, 'AI'),
(19, 'Business'),
(20, 'Networking'),
(21, 'Food'),
(22, 'Sports'),
(23, 'Football'),
(24, 'Volleyball'),
(25, 'Soccer')
;
