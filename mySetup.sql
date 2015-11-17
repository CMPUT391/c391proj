drop sequence person_id;
drop sequence sensor_id;
drop sequence image_id;
drop sequence recording_id;
drop sequence scalar_id;

create sequence person_id start with 1 increment by 1;
create sequence sensor_id start with 1 increment by 1;
create sequence image_id start with 1 increment by 1;
create sequence recording_id start with 1 increment by 1;
create sequence scalar_id start with 1 increment by 1;

insert into persons values (person_id.nextval, 'one', 'oneone', '12345', 'one@ualberta.ca', '7801234567');
insert into persons values (person_id.nextval, 'two', 'twotwo', '12345', 'two@ualberta.ca', '7801234567');
insert into persons values (person_id.nextval, 'three', 'threethree', '12345', 'three@ualberta.ca', '7801234567');
insert into persons values (person_id.nextval, 'geneva', 'giang', '12345', 'geneva@ualberta.ca', '7801234567');

insert into users values ('admin', 'admin', 'a', 1, NULL);
insert into users values ('curator', 'curator', 'd', 2, NULL);
insert into users values ('scientist', 'scientist', 's', 3, NULL);
insert into users values ('genevaSci', 'geneva', 's', 4, NULL);

insert into sensors values (sensor_id.nextval, 'YEG', 'a', 'Edmonton Audio Sensor');
insert into sensors values (sensor_id.nextval, 'YVR', 'i', 'Vancouver Other Sensor');
insert into sensors values (sensor_id.nextval, 'YYC', 'a', 'Calgary Audio Sensor');
insert into sensors values (sensor_id.nextval, 'YEG', 'o', 'Edmonton Other Sensor');
insert into sensors values (sensor_id.nextval, 'YEG', 'o', 'Edmonton Downtown Other Sensor');
insert into sensors values (sensor_id.nextval, 'YYZ', 't', 'Toronto Temp Sensor');
insert into sensors values (sensor_id.nextval, 'YVR', 'o', 'Vancouver Other Sensor');



insert into subscriptions values (1, 4);
insert into subscriptions values (2, 4);
insert into subscriptions values (3, 4);
insert into subscriptions values (4, 4);
insert into subscriptions values (1, 3);
insert into subscriptions values (2, 3);


insert into images values (image_id.nextval, 3, TO_DATE('11,16,2015','mm/dd/yyyy'), 'an image of a cat from 401', NULL, NULL);
insert into images values (image_id.nextval, 4, TO_DATE('11,01,2015','mm/dd/yyyy'), 'geneva hockey eberle', NULL, NULL);
insert into images values (image_id.nextval, 1, TO_DATE('11,30,2015','mm/dd/yyyy'), 'geneva 401 cat', NULL, NULL);

insert into audio_recordings values (recording_id.nextval, 3, TO_DATE('11,16,2015','mm/dd/yyyy'), 123, 'geneva memo voice mail iphone', NULL);
insert into audio_recordings values (recording_id.nextval, 4, TO_DATE('11,29,2015','mm/dd/yyyy'), 50, 'cats meow gen', NULL);
insert into audio_recordings values (recording_id.nextval, 1, TO_DATE('11,30,2015','mm/dd/yyyy'), 100, 'geneva 401', NULL);

insert into scalar_data values (scalar_id.nextval, 1, TO_DATE('11,29,2015','mm/dd/yyyy'), 123.56);
insert into scalar_data values (scalar_id.nextval, 4, TO_DATE('11,16,2015','mm/dd/yyyy'), 90);
insert into scalar_data values (scalar_id.nextval, 3, TO_DATE('11,05,2015','mm/dd/yyyy'), 68);

commit;
