

<?php 
$markdown = <<<content


# Ocean Observation System Documentation

---

<a name="table_of_contents"></a>
## Table of Contents
---

1. [Installation](#installation)
2. [Usage Guide](#usage_guide)
  1. [Login Module](#module_login)
  2. [Sensor Management module](#module_sensor)
  3. [User Management module](#module_user)
  4. [Subscribe Module](#module_subscribe)
  5. [Uploading Module](#module_upload)
  6. [Search Module](#module_search)
  7. [Data Analysis Module](#module_analysis)
3. [License](#license)

<a name="installation"></a>
## Installation 

---

1. Download and unzip all the required files. (e.g. from project github page)
2. Run the setup.sql on sqlplus by typing "@setup.sql"
3. Someone finish this....?

[Go Back](#table_of_contents)

<a name="usage_guide"></a>
## Usage Guide

---

<a name="module_login"></a>
##### Login Module
---
The user will have two text fields they must fill out. The first being an username and the second a password. The user must input 
a correct username and password which exists within the database. Depending on the type of user that logs in, he/she will be given access 
to different modules on the main page. 

[Go Back](#table_of_contents)



<a name="module_sensor"></a>
##### Sensor Management module
---
While signed in as an administrator, you can view the table of all sensors in the databse with their associated information.
To create a sensor, enter the sensor location select a sensor type from the dropdown menu & enter a description & press add sensor.
To remove a sensor, simply enter the sensor id of the sensor you would like to remove & press remove.

[Go Back](#table_of_contents)



<a name="module_user"></a>
##### User Management module
---
While signed in as an administrator, you can view the table of all persons in the database with their corresponding information.
To add a new person, fill in all the fields presented and click add new person.
To update a person, enter the person id of the person that you want to update, the fill any or all of the fields below that you want to update, leaving the rest blank & press update person.
To remove a person, simply enter the id of the person you would like to remove.

While signed in as an administrator, you can view the table of all users in the database with their associated information.
To create a new user account, simply fill in all the fields under the create user account heading & press create user.
To remove a user account, simply enter the username you want to remove & press remove.
To update a user account, enter the username of the user you want to update then fill in any or all of the fields below it, leaving the rest blank and press update user.

[Go Back](#table_of_contents)



<a name="module_subscribe"></a>
##### Subscribe Module
---
While signed in, simply check the checkbox beside the desired sensor and press submit to subscribe to all checked sensors.  
Previously subscribed sensors will be checked beforehand.  

[Go Back](#table_of_contents)



<a name="module_upload"></a>
##### Uploading Module
---
While signed in as a data curator, fill in the fields for either uploading images, audio recordings or csv files. 
For images, the user must choose an image to upload from some directory in jpg form, enter a valid sensor id, a date, and description.
For audio, the user must choose an audio file to upload in wav form, a valid sensor id, a date, the length of the audio file, and a description.
For csv files, the user must upload a csv file of format: sensor id, date, value. 

[Go Back](#table_of_contents)



<a name="module_search"></a>
##### Search Module
---
While signed in as a scientist, fill in the optional fields by entering keywords separated by a comma, selecting a sensor type, and/or specifying a sensor location as desired. 
Then fill in the mandatory start & end dates to query for and click submit.
The sensor type dropdown menu contains the 3 choices of sensor types : images, audio & scalar.
After submitting, the page will display all records that match the query and it's corresponding fields associated with the record.
Any images or audio recordings with descriptions containing any of the keywords will be displayed as well as any images, audio recordings and scalar data that correspond to a sensor that contains the keywords in its description will also be displayed.
For images, the user can choose to download the full sized image by clicking the download button for the corresponding image.
For audio recordings, the user can choose to download the audio file by clicking the download button for the corresponding audio recording.
For scalar data, the user can choose to download the record as a csv file by clicking the download button for the corresponding scalar data.

[Go Back](#table_of_contents)




<a name="module_analysis"></a>
##### Data Analysis Module
---
While signed in, enter the year, query time window and the sensor from the dropdown menu and press submit.  
The year accepts values between the year 1900 to 2099.  
Time window dropdown menu contains the time window options. (yearly,quarterly,monthly,weekly,daily)  
The sensor dropdown menu contains all currently subscribed sensors.  
After submitting, the page will display all records that match the query.
The user can choose to roll up or drill down by choosing a different time window option.


[Go Back](#table_of_contents)




<a name="license"></a>
## License
---
[Go Back](#table_of_contents)
content;
?>