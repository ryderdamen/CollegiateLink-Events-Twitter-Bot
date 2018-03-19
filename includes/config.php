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
	
	// Enter your google tracking ID for tracking visitors to the "today" page
	$googleTrackingID = "UA-97296944-6";


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
	