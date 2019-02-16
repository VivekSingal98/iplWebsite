-- NOTE - columns with only check constraints can take in NULL values

CREATE DATABASE ipl;

\c ipl

CREATE TABLE team(
  team_id int PRIMARY KEY, -- AUTO
  team_name text NOT NULL UNIQUE
  );

CREATE TABLE player(
  player_id int PRIMARY KEY, -- AUTO
  player_name text NOT NULL, 
  dob text NOT NULL, -- converted to date type later using alter table statement
  batting_hand text NOT NULL CHECK(batting_hand IN ('Right-hand bat','Left-hand bat','Right-handed')),
  bowling_skill text CHECK(bowling_skill IN ('Left-arm fast','Left-arm medium','Legbreak googly','Right-arm fast','Left-arm medium-fast','Right-arm bowler','Right-arm offbreak','Left-arm fast-medium','Right-arm fast-medium','Slow left-arm chinaman','Slow left-arm orthodox','Right-arm medium-fast','Right-arm medium','Right-arm medium fast','Legbreak')),
  country_name text NOT NULL
  );

CREATE TABLE venue(
  venue_id int PRIMARY KEY, -- AUTO
  venue_name text NOT NULL,
  city_name text NOT NULL,
  country_name text NOT NULL
  );

CREATE TABLE match(
  match_id int PRIMARY KEY, -- AUTO
  team1 text NOT NULL REFERENCES team(team_name), 
  team2 text NOT NULL REFERENCES team(team_name) CHECK(team1 != team2),
  match_date text NOT NULL, -- converted to date type later using alter table statement
  season_year int NOT NULL, 
  venue_id int NOT NULL REFERENCES venue(venue_id),
  toss_winner text CHECK(team1 = toss_winner OR team2 = toss_winner), 
  match_winner text CHECK(team1 = match_winner OR team2 = match_winner),
  toss_name text CHECK(toss_name IN ('bat','field')),
  win_type text CHECK(win_type IN ('tie','runs','wickets')),
  outcome_type text NOT NULL CHECK(outcome_type IN ('Result','Tied','Superover','Abandoned','Rain','Live')),
  manofmatch text,  -- trigger such that man of the match should be playing in the match
  win_margin int 
  );

CREATE TABLE player_match(
  match_id int REFERENCES match,
  player_id int REFERENCES player,
  role_desc text CHECK(role_desc IN ('CaptainKeeper','Keeper','Player','Captain')),
  player_team text REFERENCES team(team_name),
  PRIMARY KEY(match_id,player_id)
  );

CREATE TABLE team_match(
  match_id int REFERENCES match,
  innings_no int NOT NULL CHECK(innings_no > 0 AND innings_no < 5),
  team_batting int NOT NULL REFERENCES team,
  team_bowling int NOT NULL REFERENCES team,
  PRIMARY KEY(match_id,innings_no)
  );

CREATE TABLE ball(
  match_id int NOT NULL REFERENCES match,
  over_id int NOT NULL CHECK(over_id > 0 AND over_id < 21),
  ball_id int NOT NULL CHECK(ball_id > 0 AND ball_id < 10),
  innings_no int NOT NULL CHECK(innings_no > 0 AND innings_no < 5),
  striker_batting_position int CHECK(striker_batting_position > 0 AND striker_batting_position < 12),  -- data does not exist for 2017
  extra_type text NOT NULL CHECK(extra_type IN ('wides','byes','No Extras','noballs','legbyes','penalty') ),
  runs_scored int NOT NULL CHECK(runs_scored >= 0 AND runs_scored <= 6), 
  extra_runs int NOT NULL CHECK(runs_scored >= 0 AND runs_scored <= 7),
  out_type text NOT NULL CHECK(out_type IN ('Not Applicable','retired hurt','caught','Keeper Catch','bowled','caught and bowled','stumped','run out','lbw','obstructing the field','hit wicket')),
  striker int NOT NULL REFERENCES player, -- trigger such that striker should be playing in the match
  non_striker int NOT NULL REFERENCES player, -- trigger such that non_striker should be playing in the match
  bowler int NOT NULL REFERENCES player, -- trigger such that bowler should be playing in the match
  player_out int NULL REFERENCES player, -- trigger such that player_out should be playing in the match
  fielder int NULL REFERENCES player, -- trigger such that fielder should be playing in the match
  PRIMARY KEY (match_id,over_id,ball_id,innings_no)
  );

CREATE TABLE login(
  uname text PRIMARY KEY,
  password text NOT NULL
  );

INSERT INTO login VALUES('adarsh','$2y$10$OcES.iLzdvPsF9Mt.4YzwOtpnvNvSYuzfsTZ8zxgXA6nFhIpDp54u');
INSERT INTO login VALUES('chinmaya','$2y$10$OcES.iLzdvPsF9Mt.4YzwOtpnvNvSYuzfsTZ8zxgXA6nFhIpDp54u');
INSERT INTO login VALUES('vivek','$2y$10$OcES.iLzdvPsF9Mt.4YzwOtpnvNvSYuzfsTZ8zxgXA6nFhIpDp54u');

SET datestyle to DMY, SQL; -- To display the date in DD/MM/YEAR format
ALTER TABLE player ALTER COLUMN dob TYPE DATE using to_date(dob, 'MM/DD/YYYY');
ALTER TABLE match ALTER COLUMN match_date TYPE DATE using to_date(match_date, 'MM/DD/YYYY');

-- AUTO INCREMENTS --
--CREATE SEQUENCE team_id_seq WITH 14 INCREMENT BY 1;
--ALTER TABLE team ( team_id int DEFAULT nextval('team_id_seq') );
--ALTER SEQUENCE team_id_seq OWNED BY team.team_id;

--CREATE SEQUENCE player_id_seq WITH 498 INCREMENT BY 1;
--ALTER TABLE player ( player_id int DEFAULT nextval('player_id_seq') );
--ALTER SEQUENCE player_id_seq OWNED BY player.player_id;

--CREATE SEQUENCE venue_id_seq WITH 35 INCREMENT BY 1;
--ALTER TABLE venue ( venue_id int DEFAULT nextval('venue_id_seq') );
--ALTER SEQUENCE venue_id_seq OWNED BY venue.venue_id;

--CREATE SEQUENCE match_id_seq WITH 1082650 INCREMENT BY 1;
--ALTER TABLE match ( match_id int DEFAULT nextval('match_id_seq') );
--ALTER SEQUENCE match_id_seq OWNED BY match.match_id;


-- VIEWS --
CREATE VIEW match_venue AS SELECT match_id,team1,team2,match_date,season_year,venue_name,city_name,country_name,toss_winner,match_winner,toss_name,win_type,outcome_type,manofmatch,win_margin FROM match NATURAL JOIN venue;
CREATE VIEW player_name_match AS SELECT player_id,player_name,match_id,player_team FROM player_match NATURAL JOIN player;

-- TRIGGERS -- 
CREATE TRIGGER manofmatch_check AFTER INSERT UPDATE ON match
REFERENCING NEW ROW AS nrow
FOR EACH ROW
WHEN (nrow.manofmatch NOT IN (
SELECT player_name
FROM player_name_match WHERE nrow.match_id = player_name_match.match_id)) 
BEGIN
ROLLBACK
END;

CREATE TRIGGER players_check AFTER INSERT UPDATE ON ball
REFERENCING NEW ROW AS nrow
FOR EACH ROW
WHEN (
nrow.striker NOT IN (
SELECT player_id
FROM player_match WHERE nrow.match_id = player_match.match_id) 
OR 
nrow.non_striker NOT IN (
SELECT player_id
FROM player_match WHERE nrow.match_id = player_match.match_id)
OR 
nrow.bowler NOT IN (
SELECT player_id
FROM player_match WHERE nrow.match_id = player_match.match_id)
OR 
nrow.player_out NOT IN (
SELECT player_id
FROM player_match WHERE nrow.match_id = player_match.match_id)
OR 
nrow.fielder NOT IN (
SELECT player_id
FROM player_match WHERE nrow.match_id = player_match.match_id)) 
BEGIN
ROLLBACK
END;