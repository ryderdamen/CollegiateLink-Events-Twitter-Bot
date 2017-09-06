<?php
// Config File
// Use this file to configure the bot to your particular school.

// Global Variables
$feedURL = "https://connectru.ryerson.ca/events/events.rss"; // Enter the URL of the collegiatelink RSS feed
$notifyEmail = ""; // Enter an email here if you wish to be notified when a tweet is posted
$tweetNotificationMessage = "CollegiateLink Event Bot: A tweet has been posted."; // Email message to be sent to you (see above)
$timeZoneSet = "America/Toronto";

// Daily Summary PAGE Variables
$enableDailySummaryPage = true; // Enables a daily summary page to be generated
$schoolName = "Ryerson"; // Enter your school's name for the daily summary page
$schoolURL = "https://connectRU.ryerson.ca"; // Enter your school's URL for the daily summary page
$collegiateLinkSiteName = "ConnectRU";

// Daily Summary Tweet Variables
$enableDailySummaryTweet = true;
$dailySummaryHour = "17"; // Which hour of the day (24 hours) do you want this to post at?
$dailySummaryMultipleEventsMessage = "Hey Ryerson, there are ### events happening today. Check them out here:"; ### is replaced by a count
$dailySummaryOneEventMessage = "Hey Ryerson, there is one event happening today. Check them out here:";
$dailySummaryNoEventsMessage = "Hey Ryerson, unfortunately there are no events scheduled for today. Check back soon.";
$dailySummaryUrl = "http://ryda.ca/ryerson-events/today"

?>
	