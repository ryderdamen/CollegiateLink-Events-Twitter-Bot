# CollegiateLink Events Bot
A PHP twitter bot that posts currently happening events for institutions using the CollegiateLink framework.
* [About This Project](http://ryda.ca/ryerson-events)
* [Ryerson Live Twitter Feed](https://twitter.com/ryersonevents)
* [Brock Live Twitter Feed](https://twitter.com/brockuevents)

![A screenshot of the bot](https://mir-cdn.behance.net/v1/rendition/project_modules/fs/c32abf55682179.5990ce2074bf4.png)

## About
Originally built for Brock University, I decided to refactor and rebuild this bot for Ryerson, and any other universities using CollegiateLink software by CampusLabs.

## Setup
1. Clone this repository
2. Change the '/includes/sample_api_keys.php' file to 'api_keys.php' and add in your required variables

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

3. Change the '/includes/sample_config.php' file to 'config.php' and add in your required variables


`````php

<?php
	
// Twitter Event Bot
// Author: Ryder Damen
// Use this file to configure the bot to your particular school.

//******************************************//
//              Global Variables            //
//******************************************//
	
	// The URL for the RSS event feed of your collegiatelink web portal
	$feedURL = "https://connectru.ryerson.ca/events/events.rss";

	// The email to notify if a tweet is posted, and the message to send
	$notifyEmail = "";
	$tweetNotificationMessage = "CollegiateLink Event Bot: A tweet has been posted.";

	// The PHP timezone ID for your school (ex: America/Toronto)
	$timeZoneSet = "America/Toronto";


//******************************************//
//       Daily Summary Page Variables       //
//******************************************//

	// Enables a daily summary page to be generated as an index in the today folder
	$enableDailySummaryPage = true;
	
	// The name of your school, for display on the daily summary folder
	$schoolName = "Ryerson";
	
	// The name and URL to link to for "click for more information about events"
	$collegiateLinkSiteName = "ConnectRU";
	$schoolURL = "https://connectRU.ryerson.ca";


//******************************************//
//       Daily Summary Tweet Variables      //
//******************************************//

	// Enable the daily summary tweet (true / false)
	$enableDailySummaryTweet = true;
	
	// Which hour of the day (24 hours) do you want this to post at?
	$dailySummaryHour = "17";
	
	// The daily message posted when there are 2+ events; ### is replaced by the count of events
	$dailySummaryMultipleEventsMessage = "Hey Ryerson, there are ### events happening today. Check them out here:";
	
	// The daily message posted when there is only one event
	$dailySummaryOneEventMessage = "Hey Ryerson, there is one event happening today. Check it out here:";
	
	// The daily message posted when there are no events // leave blank to disable
	$dailySummaryNoEventsMessage = "Hey Ryerson, unfortunately there are no events scheduled for today. Check back soon."; 
	
	// The URL to include with the daily summary tweet
	$dailySummaryUrl = "http://ryda.ca/ryerson-events/today"
	
?>

`````

4. Schedule the cron.php file with a cron job, each hour.


## Credits
Special thanks to walkswithme.net for providing the twitter oAuth wrapper. Definitely couldn't have coded that myself.
