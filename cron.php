<?php
// Ryerson Events Twitter Bot
// Built by Ryder Damen, 2017
// This bot reads events from the ConnectRU Twitter Feed, and tweets event information the hour before the event begins
// This code is built to be easily adapted for other schools using CollegiateLink software (Brock University, etc)

// File Setup
include 'includes/api_keys.php';
include 'includes/config.php';
date_default_timezone_set($timeZoneSet);
$today = date('Y-m-d');

// File Authorization
$access_key = htmlspecialchars($_GET['key']);
if ($file_access_key !== $access_key) { // $file_access_key provided by 
	echo "Unauthorized";
	die;
}	

// Core Code
$eventsArray = getRSSFeed($feedURL);
$results = searchEvents($eventsArray);
echo $results[0] . " events searched, " . $results[1] . " tweets posted.";

// Notify someone if the option is set, and if a tweet is posted
if ($results[1] !== 0 and $notifyEmail !== "") {
	$tweetNotificationMessage .= "<br><br><table>";
	$i = 1;
	foreach ($results[3] as $tweet) {
		$tweetNotificationMessage .= "<tr><td>{$i}.&nbsp;</td><td>{$tweet}</td></tr>";
		$i++;
	}
	$tweetNotificationMessage .= "</table>";
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	mail($notifyEmail, "$results[1] {$schoolName} twitter bot event(s) posted", $tweetNotificationMessage, $headers);
}

// Build the daily schedule
echo '<br>' . buildDailySchedule($results[2], $today);

// Tweet the daily summary
echo '<br>' . tweetDailySummary();



// Functions ----------------------------------------------------------------------------------------------------

function tweetDailySummary() {
	// This function tweets a daily summary with a link to the daily summary page
	include('includes/config.php');
	$currentHour = date('G');
	
	if ( $currentHour == $dailySummaryHour and $enableDailySummaryTweet == true) { // Tweet the daily summary
		$json_events = json_decode(file_get_contents('today/events.json'));
		$count = $json_events->meta->dailyEventCount; 
		
		if ($count == 0) {
			$daily_message = $dailySummaryNoEventsMessage;
			
			if ( $dailySummaryNoEventsMessage == "" ) {
				return "The daily tweet was not posted, since there are no events, and no message is configured.";
			}
			
		}
		elseif ($count == 1) {
			$daily_message = $dailySummaryOneEventMessage;
		}
		else {
			$daily_message = preg_replace("/###/", $count, $dailySummaryMultipleEventsMessage);
		}
		
		if ($enableDailySummaryPage) {
			$daily_message .= " " . $dailySummaryUrl;
		}
		
		
		// Connecting to twitter
		require_once 'includes/twitteroauth.php';
		include 'includes/api_keys.php';
	
		// Initializing twitter connection
	    define("CONSUMER_KEY", $twitter_consumer_key);
	    define("CONSUMER_SECRET", $twitter_consumer_secret);
	    define("OAUTH_TOKEN", $twitter_oauth_token);
	    define("OAUTH_SECRET", $twitter_oauth_secret);
	    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
		    
	    // Sending data to twitter
	    $tweet = $daily_message;
	    $content = $connection->get('account/verify_credentials');
	    $connection->post('statuses/update', array('status' => $tweet));
	    return "The daily tweet was posted";	
	}	
}

function buildDailySchedule($todays_events, $today) {
	// This function builds a daily schedule at a predetermined time, where students can view events in a simple layout
	include('includes/config.php');
	if (!$enableDailySummaryPage) {
		return 'Daily schedule is not enabled.';
	}
	$event_count = count($todays_events);
	$right_now = strtotime("now");
	$meta = array(
		'lastUpdated' => "{$right_now}",
		'dailyEventCount' => "{$event_count}",
	);
	$json_print = array(
		'meta' => $meta,
		'events' => $todays_events
	);

	// Write the data to JSON
	$json_file = fopen("./today/events.json", "w");
	fwrite($json_file, json_encode($json_print, true));
	fclose($json_file);
	return 'The daily schedule was built';
}

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
	$todays_events = array();
	$today = date('Y-m-d');
	$tweets = array();
	
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
		$event_start_ymd = date('Y-m-d', $event_start_unix); // Get the event start time as a day
		
		if ($today == $event_start_ymd) { // If the event is starting today, at it to the today array			
			// Find the event location and end date
			$event_location = findEventLocation($event_raw_description);
			$event_end_time = findEventEndDate($event_raw_description); // Retrieves the start time from the DOM tree, returned as ISO		
			// Add the event to the today array
			$todays_events[] = [
				'event_url' => "{$event_url}",
				'event_organization' => "{$event_organization}",
				'event_name' => "{$event_name}",
				'event_location' => "{$event_location}",
				'event_start' => "{$event_start_time}",
				'event_end' => "{$event_end_time}"
			];
		}			
		if ($now <= $event_start_unix and $event_start_unix <= $later) {
			// We've got a currently happening event!
			$event_location = findEventLocation($event_raw_description); // Using grep to grab the location
			$tweets[] = tweetEvent($event_name, $event_url, $event_location, $event_organization, $event_start_time);
			$i_posted++;
		}
		$i_searched++;
	} // end of foreach
	return array($i_searched, $i_posted, $todays_events, $tweets);
} 



function tweetEvent($event_name, $event_url, $event_location, $event_organization, $event_start_time) {
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
    $event_name = strlen($event_name) > 60 ? substr($event_name,0,57)."..." : $event_name;
    $event_organization = strlen($event_organization) > 60 ? substr($event_organization,0,57)."..." : $event_organization;
    $event_location = strlen($event_location) > 60 ? substr($event_location,0,57)."..." : $event_location;
    
    // Optimizing date for twitter
    $event_start_time = date('g:i A' ,strtotime($event_start_time));
     
    // Sending data to twitter
    $tweet = "{$event_start_time}: {$event_name} hosted by {$event_organization}. 📍{$event_location}. {$event_url}";
    $content = $connection->get('account/verify_credentials');
    $connection->post('statuses/update', array('status' => $tweet)); 
    return $tweet;   
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

function findEventEndDate($description) {
	// Finding the end date
    preg_match("/dtend\" title=\"(.*?)\"/", $description, $dtend);
    if (empty($dtend)){ // If the date is hidden somewhere else in the DOM tree
	    // Get the date
	    preg_match("/span class=\"dtend\" title=\"(.*?)\"/", $description, $dtend_date);	    
	    // Get the time
    	preg_match("/dtend\"	title=\"(.*?)\"/", $description, $dtend_time);
    	return $dtend_date[1] . "T" . $dtend_time[1]; // This RSS feed layout is the stuff of nightmares
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
	
	