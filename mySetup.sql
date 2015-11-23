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

insert into users values ('admin', 'admin', 'a', 1, TO_DATE('01/01/2010 10:10:10','dd/mm/yyyy hh24:mi:ss'));

insert into sensors values (sensor_id.nextval, 'YEG', 'a', 'Edmonton Audio Sensor');

commit;
