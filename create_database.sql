-- Table Structures
CREATE TABLE games (
   game_id int PRIMARY KEY AUTO_INCREMENT,
   p1_id int,
   p2_id int,
   p1_rating int,
   p2_rating int,
   -- each game of prismata has between 5 and 11 units
   -- this will be a string of ids like 1|4|56|3|111
   unit_ids varchar(50),
   game_date datetime,
   replay_code varchar(20),
   result int -- 0, 1, or 2 for p1, p2, draw
);

CREATE TABLE users (
   user_id int PRIMARY KEY AUTO_INCREMENT,
   name varchar(20),
   peak_rating int,
   latest_rating int
);

CREATE TABLE units (
   unit_id int PRIMARY KEY AUTO_INCREMENT,
   name varchar(20),
   slug varchar(20)
);
