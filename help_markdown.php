

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
[Go Back](#table_of_contents)



<a name="module_sensor"></a>
##### Sensor Management module
---
[Go Back](#table_of_contents)



<a name="module_user"></a>
##### User Management module
---
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
[Go Back](#table_of_contents)



<a name="module_search"></a>
##### Search Module
---
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