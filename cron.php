<?php
// Ryerson Events Twitter Bot
// Built by Ryder Damen, 2017
// This bot reads events from the ConnectRU Twitter Feed, and tweets event information the hour before the event begins
// This code is built to be easily adapted for other schools using CollegiateLink software (Brock University, etc)

// File Setup
	// Set up timezone, since my server is in Denver
	
	
// Global Variables
$feedURL = "https://connectru.ryerson.ca/events/events.rss";

// Core code
$eventsArray = getRSSFeed($feedURL);



function getRSSFeed($feedURL) {
	// This function retrieves the RSS feed from the appropriate website, and returns the decoded array
	try { 
		return simplexml_load_string(file_get_contents($feedURL));
		}
	catch (Exception $e) {
		echo "The RSS Feed is down";
		die; // Kill the file
	}
}

function searchEvents($eventsArray) {
	// This functions searches the feed for events starting this particular hour
	$today = ""; // get todays date down to the hour
	
	foreach ($eventsArray->item as $event) {
		
		// Decode this nonsense DOM tree and parse out the date
		
		if ($event_date == $today) {
			// We have a winner!
		}
		
	} // end of foreach
	
	// That's all, folks!
	
} 

function initializeTwitter() {
	// Initialize twitter via oAuth
	
}

function tweetStuff() {
	// Sends tweets
	
}

	
?>
	
	