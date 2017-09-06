# Ryerson Events Bot
A PHP twitter bot that posts currently happening events at Ryerson University from the connectRU RSS feed.
* [About This Project](http://ryda.ca/ryerson-events)
* [Ryerson Live Twitter Feed](https://twitter.com/ryersonevents)
* [Brock Live Twitter Feed (Version 1 Code)](https://twitter.com/brockuevents)

![A screenshot of the bot](https://mir-cdn.behance.net/v1/rendition/project_modules/fs/c32abf55682179.5990ce2074bf4.png)

## About
Originally built for Brock University, I decided to refactor and rebuild this bot for Ryerson, and any other universities using CollegiateLink software by CampusLabs.

## Setup
1. Clone this repository
2. Change the '/includes/sample_api_keys.php' file to 'api_keys.php' and add in your required variables
   Change the '/includes/sample_config.php' file to 'config.php' and add in your required variables

`````php

<?php
	
$twitter_consumer_key = "1234567890";
$twitter_consumer_secret = "123456790";
$twitter_oauth_token = "12345-12345";
$twitter_oauth_secret = "1234567890";
$file_access_key = "SuperSecretKey";
	
?>

`````

The twitter variables can be obtained by creating a new app on apps.twitter.com, and the file_access_key variable is whatever you want to set it to.

3. Change the $feedURL variable in the cron.php file to the RSS feed of your school's CollegiateLink main events.
4. Schedule the cron.php file with a cron job, each hour.


## Credits
Special thanks to walkswithme.net for providing the twitter oAuth wrapper. Definitely couldn't have coded that myself.
