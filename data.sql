-- Persons: person_id int, first_name varchar(24), last_name varchar(24), address varchar(128), email varchar(128), phone char(10)
-- Users: user_name varchar(32), password varchar(32), role char(1), person_id int, date_registered date
-- Sensors: sensor_id int, location varchar(64), sensor_type char(1), description varchar(128)
-- Subscriptions: sensor_id int, person_id int
-- Audio Recordings: recording_id int, sensor_id int, date_created date, length int, description varchar(128), recorded_data blob
-- Image: image_id int, sensor_id int, date_created date, description varchar(128), thumbnail blob, recorded_data blob
-- Scalar Data: id int, sensor_id int, date_created dat, value float

-- admin       
insert into persons values (1, 'admin', 'admin', 'address1', 'email1', '1111111111');
insert into users values ('admin', 'admin', 'a', 1, NULL);
-- scientist	
insert into persons values (2, 'scientist', 'scientist', 'address2', 'email2', '2222222222'); 
insert into users values ('scientist', 'scientist', 's', 2, NULL);
-- data curator
insert into persons values (3, 'RIP', 'McDavid', 'address3', 'email3', '3333333333');
insert into users values ('datacurator', 'datacurator', 'd', 3, NULL);

-- sensors
insert into sensors values (1, 'location', 'a', 'description');
insert into sensors values (2, 'location', 'i', 'description');
insert into sensors values (3, 'location', 's', 'description');

-- subscriptions
insert into subscriptions values (1, 2);

-- audio recordings

-- images

-- scalar_data
