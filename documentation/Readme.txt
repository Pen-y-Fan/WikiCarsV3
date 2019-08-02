Documentation


The layout of the project is as follows

root:
|-htconfig  contains the database configuration file dbConfigwiki.php
|   | dbConfigwiki.php  Database, username and password for MySQL
|-public_html This should be the public folder on your website, may need
    |           to be renamed to the host naming convention.
    |-includes      Contains functions
    |-create        initalisation files, including CSV files
    |   | CarData.csv               The CSV file contining all cars
    |   | CarsUpgrades.csv          The CSV file containing all the car upgrade data
    |   | initialise_tCarData.php   Initializes the tCarData table using the
                                    CarData.csv file.
    |   | initialise_tUpgrades.php  Initializes the tUpgrades table using the
    |   |                           CarsUpgrades.csv note this can take 10 min!
    |   | initialiseWikiDatabase.php  Creates the database, needs the root (or
    |                                   account with create access)
    | CarData.php   Car data webpage to choose the upgrade
    | CarUpgrades.php   Final webpage to calculate the upgrades required
    | index.php     Index page displaying all pages in the cars table


Creating the database.

First edit the dbConfigwiki.php change the following lines:
$host=      "localhost";    // Need localhost on LINUX
//$host=      '127.0.0.1';  // 127.0.0.1 on Windows
$dbname=    'wikidb';       // Name of your database
$user=      'dbusername';   // Your DB user name
$password=  'password';     // Your DB password


$hosts comment out the localhost line, if running on Windows and uncomment the 127.0.0.1 line

$dbname should be the name of the database default is wikidata, if the host adds a suffix change this file name.

In MySQL / Miranda create a user account with permission to drop and create tables, as well as select. On a home system the password could be 'root' and password ''

$user=      'dbusername';    MySQL / Miranda user name created above.
$password=  'password';     Password for the created account.

Save the dbConfigwiki.php  file.


Next edit the initialiseWikiDatabase.php file in public_html\create folder

$host="127.0.0.1";      On LINUX change to 'localhost'

$root="root";           Enter an account with permission to create databases.
                        On hosted site the database may need to be created in
                        MySQLAdmin
$root_password="";      Password for the account.

Using PowerQuery create the car data CSV files in public_html\create folder:
CarData.csv
CarsUpgrades.csv

One common problem is EXCEL creates a blank line at the end of the CSV file, to avoid this edit the CSV and remove any blank lines.

The database can now be created by navigating to the initialize webpages:
On local PC: http://localhost/initialise/initialiseWikiDatabase.php
On hosted environment: https://<websitename>/initialise/initialiseWikiDatabase.php

Next initialise the tCarData table:
On local PC: http://localhost/initialise/initialise_tCarData.php
On hosted environment: https://<websitename>/initialise/initialise_tCarData.php
This normally only take as a few seconds.
Note any error messages.
The first time the script is runs, there will be an error that tCarData didn't exist, as one stage is to drop the table. This could be improved with a DROP IF EXISTS. This message can be ignored.

Finally initialize the tCarData table:
On local PC: http://localhost/initialise/initialise_tUpgrades.php
On hosted environment: https://<websitename>/initialise/initialise_tUpgrades.php
This create allot of data and doesn't show any progress, it is important to leave the script run, it can take 10 minutes. Note any error messages.
One common error is for the script to time out. If this happens change the allowed run time in php.ini.

The first time the script is run, there will be an error that tUpgrades didn't exist, as one stage is to drop the table. This could be improved with a DROP IF EXISTS, in the future. This message can be ignored.

Another common problem is EXCEL creates a blank line at the end of the CSV file, to avoid this edit the CSV and remove any blank lines.

The first time the script is run, there will be an error that tCarData didn't exist, as one stage is to drop the table. This could be improved with a DROP IF EXISTS, in the future.


The website is now configured and good to go. Navigate the to home page:
On local PC: http://localhost
On hosted environment: https://<websitename>

Enjoy!

Any problems let me know.
