# DatabaseFinal
##Database final project, Joshua McEwen, Ethan M

Our app is for a manager to manage a basketball team. The two tables shown are for the stats  across a season and the measurements the players have. It can add a player, delete or edit as was required.

CREATE TABLE Player_Stats(
	player_Num int NOT NULL AUTO_INCREMENT,
	player_name varchar(255) NOT NULL,
	position varchar(2) NOT NULL,
	points int DEFAULT 0,
	assists int DEFAULT 0,
	steals int DEFAULT 0,
	PRIMARY KEY(player_Num)
);

CREATE TABLE Players(
	player_Num int NOT NULL AUTO_INCREMENT,
	player_name varchar(255) NOT NULL,
	height_feet int NOT NULL,
	height_inches int NOT NULL,
	weight int NOT NULL,
	PRIMARY KEY(player_Num)
);

Players -> player_number(player_Num), player_Name, height_feet, height_inches, weight  
                                        
Player_Stat -> player_Num, player_Name, position, points, assist, steals

create: add Player -> links to both table in the database to get the fields to fill in then fills in the information to both tables that is then (line 396)

read: the tables -> display the tables in the database with the information contained in  them. thought the play stat table doesn't include the players number, started around (line 116)

Update: information to the tables -> called by the form when the entry has a number for the player. This then tell the action that it will be update the entry with the id that is given. (line 540)

Delete: taked player_Num and used it as an id to take that out of the tables, both of them as they share the player_Num (line 241)

(https://youtu.be/-UsoAG3SDEs)
