drop sequence person_id;
drop sequence sensor_id;

create sequence person_id start with 1 increment by 1;
create sequence sensor_id start with 1 increment by 1;

insert into persons values (person_id.nextval, 'one', 'oneone', '12345', 'one@ualberta.ca', '7801234567');
insert into persons values (person_id.nextval, 'two', 'twotwo', '12345', 'two@ualberta.ca', '7801234567');
insert into persons values (person_id.nextval, 'three', 'threethree', '12345', 'three@ualberta.ca', '7801234567');
insert into persons values (person_id.nextval, 'geneva', 'giang', '12345', 'geneva@ualberta.ca', '7801234567');

insert into users values ('admin', 'admin', 'a', 1, NULL);
insert into users values ('curator', 'curator', 'd', 2, NULL);
insert into users values ('scientist', 'scientist', 's', 3, NULL);
insert into users values ('genevaSci', 'geneva', 's', 4, NULL);

insert into sensors values (sensor_id.nextval, 'YEG', 'a', 'Edmonton Audio Sensor');
insert into sensors values (sensor_id.nextval, 'YYC', 'i', 'Calgary Image Sensor');
insert into sensors values (sensor_id.nextval, 'YYZ', 't', 'Toronto Temp Sensor');
insert into sensors values (sensor_id.nextval, 'YVR', 'o', 'Vancouver Other Sensor');

insert into subscriptions values (1, 4);
insert into subscriptions values (3, 4);
insert into subscriptions values (1, 3);
insert into subscriptions values (2, 3);

commit;
