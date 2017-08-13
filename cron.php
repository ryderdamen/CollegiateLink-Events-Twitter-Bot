<?php
// Ryerson Events Twitter Bot
// Built by Ryder Damen, 2017
// This bot reads events from the ConnectRU Twitter Feed, and tweets event information the hour before the event begins
// This code is built to be easily adapted for other schools using CollegiateLink software (Brock University, etc)

// File Setup
date_default_timezone_set('America/Toronto');	// Set up timezone, since Ryder's server is in Denver
include 'includes/api_keys.php';

// File Authorization
$access_key = htmlspecialchars($_GET['key']);
if ($file_access_key !== $access_key) { // $file_access_key provided by 
	echo "Unauthorized";
	die;
}
	
// Global Variables
$feedURL = "https://connectru.ryerson.ca/events/events.rss";
$notifyEmail = ""; // Enter an email here if you wish to be notified when a tweet is posted

// Core Code
$eventsArray = getRSSFeed($feedURL);
$results = searchEvents($eventsArray);
echo $results[0] . " events searched, " . $results[1] . " tweets posted.";

// Notify someone if the option is set, and if a tweet is posted
if ($results[1] !== 0 and $notifyEmail !== "") {
	error_log("CollegiateLink Event Bot: A tweet has been posted.", 1, $notifyEmail);
}



// Functions ----------------------------------------------------------------------------------------------------

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
	
	$now =  strtotime( date('Y-m-d H:i:s') ); // Returns the current date, for processing
	$later = strtotime( date('Y-m-d H:i:s', strtotime('+ 65 minutes') ) ); // Returns the date 1 hour 5 mins later
	$i_searched = 0; // Setting integer count for events searched
	$i_posted = 0; // Setting integer count for events posted
	
	foreach ($eventsArray->channel->item as $event) {
		
		// Setting basic variables
		$event_url = $event->link;
		$event_organization = $event->author;
		$event_name = $event->title;
		$event_raw_description = $event->description;
		
		// Do not include cancelled events
		if (strpos($event_name, '(Cancelled)') !== false) { continue; }		
		
		// Instead of assigning it an individual variable, the start / end dates have been hidden in the description
		// In addition to that nonsense, the date also seems to randomly move around within the description tag as it pleases
		// This bit of code sorts that nonsense out and finds the date
		
		$event_start_time = findEventStartDate($event_raw_description); // Retrieves the start time from the DOM tree, returned as ISO
		$event_start_unix = strtotime($event_start_time);
				
		if ($now <= $event_start_unix and $event_start_unix <= $later) {
			// We've got a currently happening event!
			$event_location = findEventLocation($event_raw_description); // Using grep to grab the location
			tweetStuff($event_name, $event_url, $event_location, $event_organization, $event_start_time);
			$i_posted++;
		}
		$i_searched++;
	} // end of foreach
	return array($i_searched, $i_posted);
} 



function tweetStuff($event_name, $event_url, $event_location, $event_organization, $event_start_time) {
	// Limits string lengths, and posts to twitter
	
	require_once 'includes/twitteroauth.php';
	include 'includes/api_keys.php';
	
	// Initializing twitter connection
    define("CONSUMER_KEY", $twitter_consumer_key);
    define("CONSUMER_SECRET", $twitter_consumer_secret);
    define("OAUTH_TOKEN", $twitter_oauth_token);
    define("OAUTH_SECRET", $twitter_oauth_secret);
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
	
	// Limiting the string lengths for twitter with elvis operators (similar to if else statements)
    $event_name = strlen($event_name) > 30 ? substr($event_name,0,27)."..." : $event_name;
    $event_organization = strlen($event_organization) > 30 ? substr($event_organization,0,27)."..." : $event_organization;
    $event_location = strlen($event_location) > 30 ? substr($event_location,0,27)."..." : $event_location;
    
    // Optimizing date for twitter
    $event_start_time = date('g:i A' ,strtotime($event_start_time));
        
    // Sending data to twitter
    $tweet = "{$event_start_time}: {$event_name} hosted by {$event_organization}. 📍 {$event_location}. {$event_url}";
    $content = $connection->get('account/verify_credentials');
    $connection->post('statuses/update', array('status' => $tweet));
    
}



function findEventStartDate($description) {
	
	// Finding the start date
    preg_match("/dtstart\" title=\"(.*?)\"/", $description, $dtstart);
    
    if (empty($dtstart)){ // If the date is hidden somewhere else in the DOM tree
	    // Get the date
	    preg_match("/span class=\"dtstart\"><span class=\"value\" title=\"(.*?)\"/", $description, $dtstart_date);	    
	    // Get the time
    	preg_match("/class=\"value\"	title=\"(.*?)\"/", $description, $dtstart_time);
    	return $dtstart_date[1] . "T" . $dtstart_time[1]; // This RSS feed layout is the stuff of nightmares
    };
    
    // Return in ISO format for multi-use processing
    return $dtstart[1];
	
}

function findEventEndDate() {
	
	// Finding the end date (Not currently used)
    preg_match("/dtend\" title=\"(.*?)\"/", $description, $dtend);  
    
    if ( empty($dtend) ) { // If the date is hidden somewhere else in the DOM tree
        preg_match("/class=\"dtend\"	title=\"(.*?)\"/", $description, $dtend);
    };
    
    // Return in ISO format for multi-use processing
    return $dtend[1];
	
}

function findEventLocation($description) {
	
	// Finds the event location hidden within the description tag
    $description = htmlspecialchars_decode($description);
    preg_match("/location\">(.*?)</", $description, $location);
    return $location[1];	
    
}

	
?>
	
	