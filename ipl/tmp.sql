select count(*) as num_over,count(*) filter(where numWicketsPerOver=4) as num_4w,count(*) filter(where numWicketsPerOver=3) as num_3w 
from (select match_id,over_id,count(out_type) 
filter(where out_type Not in ('Not Applicable','retired hurt','obstructing the field','hit wicket','run out')) as numWicketsPerOver
from ball where bowler=$player_id group by match_id,over_id) as x;

select distinct numWicketsPerOver from (select match_id,over_id,count(out_type) 
filter(where out_type Not in ('Not Applicable','retired hurt','obstructing the field','hit wicket','run out')) as numWicketsPerOver
from ball where bowler=315 group by match_id,over_id) as x;

select sum(runs_scored) + sum(extra_runs) filter(where extra_type NOT In ('penalty')) as total_runs,
count(out_type) filter(where out_type Not in ('Not Applicable','retired hurt','obstructing the field','hit wicket','run out')) as num_wickets 
from ball where bowler=$player_id;

select player_name,total_matches,total_runs,total_balls_faced,total_num4,total_num6,highest_score,average_runs,num_50,
num_100 from (
select player_name,striker,sum(runs_per_match) as total_runs,sum(balls_faced_permatch) as total_balls_faced,sum(num4_permatch) as total_num4,
sum(num6_permatch) as total_num6, max(runs_per_match) as highest_score, AVG(runs_per_match) as average_runs,count(runs_per_match) 
filter(where runs_per_match>=50 and runs_per_match<100) as num_50, count(runs_per_match) filter(where runs_per_match>=100) as num_100
from (select player_name,ball.striker,sum(runs_scored) as runs_per_match,count(ball_id) as balls_faced_permatch,
count(ball_id) filter(where runs_scored=4) as num4_permatch,
count(ball_id) filter(where runs_scored=6) as num6_permatch from ball join player on (ball.striker=player.player_id) 
group by match_id,ball.striker,player.player_name) as x
group by striker,player_name ) as table1
join 
(select player_id,count(distinct match_id) as total_matches from player_match group by player_id) as table2
on striker=player_id 
order by total_runs DESC
limit 10;

select match_id,
select b1.over,b1.striker from ball as b1 join ball as b2 on 
(b1.match_id=b2.match_id and b1.striker=b2.striker)
where (b2.over_id,b2.ball_id)<(b1.over_id,b1.ball_id) or (b2.over_id,b2.ball_id)=(b1.over_id,b1.ball_id)
group by 

drop view if exists ball_cumm_runs2;
drop view if exists ball_cumm_runs1;
drop view if exists ball_cumm_runs;

create view ball_cumm_runs as 
select match_id,innings_no,striker,over_id,ball_id,count(ball_id) filter(where extra_type not in ('wides')) over w
as num_balls,count(ball_id) filter(where runs_scored=6) over w as num_6s,
count(ball_id) filter(where runs_scored=4) over w as num_4s,
sum(runs_scored) over w as cumm_runs 
from ball where innings_no<=2
window w as (partition by match_id,striker order by over_id,ball_id);

create view ball_cumm_runs1 as
select match_id,innings_no,player.player_name,min(num_balls) as balls_faced,max(num_6s) as num_6s,max(num_4s) as num_4s,max(cumm_runs) as runs
from ball_cumm_runs join player on ball_cumm_runs.striker=player.player_id
where cumm_runs>=50
group by match_id,striker,player.player_name,innings_no
order by Balls_Faced
limit 100;

create view ball_cumm_runs2 as
select ball_cumm_runs1.match_id,player_name,team_bowling as team_id,venue_id,match_date,balls_faced,num_6s,num_4s,runs
from ball_cumm_runs1 join team_match on ball_cumm_runs1.match_id=team_match.match_id and ball_cumm_runs1.innings_no=team_match.innings_no
join match on ball_cumm_runs1.match_id=match.match_id;

select ball_cumm_runs2.match_id,player_name,team_name as Against,venue_name as Venue,match_date,balls_faced,num_6s,num_4s,runs
from ball_cumm_runs2 join team on ball_cumm_runs2.team_id=team.team_id
join venue on ball_cumm_runs2.venue_id=venue.venue_id
order by balls_faced,runs desc,num_6s desc,num_4s desc;

392195 
yusuf - pathan: 31

select a.player_id,player_name from 
(select distinct player_id from player_match 
except select distinct striker from ball) as a join player on a.player_id=player.player_id;
