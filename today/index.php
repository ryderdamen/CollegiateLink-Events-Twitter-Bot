<?php
	// Daily Feed Page
	// Author: Ryder Damen
	// This page acts as a daily summary of events for the twitter bot to link to. It is fed by a .json file, supplied by the main cron.php file
	
	// File Setup and preventing cache
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	include('../includes/config.php');
		
	
	// Only display the page, if it has been enabled
	if (!$enableDailySummaryPage) {
		echo "This page has been disabled by the network administrator. Sorry about that!";
		die;
	}
	
	date_default_timezone_set($timeZoneSet);
	$events_json = json_decode(file_get_contents('events.json'));	
	$today = date('M j, Y');	
?>

<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<meta name='description' content='A list of events happening today at <?php echo $schoolName; ?>.'>
	<meta name='author' content='Ryder Damen'>
	<title>
		<?php echo $schoolName; ?> - Today's Events
	</title>
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='0'>
	<meta http-equiv='pragma' content='no-cache'>
	<link href='includes/main.css' rel='stylesheet'>
</head>
<body>
    <div class='header'>
	    <img src="includes/logo.png" style="height: 200px; margin: auto; display: block;">
		<h1 class='masthead-brand'>
			<?php echo $schoolName; ?> Events for <?php echo $today; ?>
		</h1>
	</div>
	<div class='tablecontainer'>
		<table class='table'>
			<tr>
				<th>Event</th>
				<th>Host</th>
				<th>Location</th>
				<th>Start / Finish</th>
			</tr>
			<?php 
				// Loop through the JSON to display events
				foreach($events_json->events as $event) {
					// Get variables			
					$event_url = $event->event_url;
					$event_organization = $event->event_organization;
					$event_name = $event->event_name;
					$event_location = $event->event_location;
					$event_start = date('g:i A' ,strtotime( $event->event_start ));
					$event_end = date('g:i A' ,strtotime( $event->event_end ));
					
					echo "
						<tr>
							<td>
								<p>
									<a href='{$event_url}'>
										{$event_name}
									</a>
								</p>
							</td>
							<td>
								<p>
									{$event_organization}
								</p>
							</td>
							<td>
								<p>
									{$event_location}
								</p>
							</td>
							<td>
								<p>
									<nobr>
										{$event_start} - {$event_end}
									</nobr>
								</p>
							</td>
						</tr>
					";
				}
			?>
			</table>
		</div>
		<div class='footer'>
		<div class='inner'>
			<p><a href='https://ryderdamen.com' target="_blank">Created by Ryder Damen</a> | <a href='https://www.behance.net/gallery/55682179/Ryerson-Events-Bot' target="_blank">About This Project</a> | <a href='https://github.com/ryderdamen/Ryerson-Events-Twitter-Bot' target="_blank">Get the Code</a> | <a href='<?php echo $schoolURL; ?>'>For more events, visit  <?php echo $collegiateLinkSiteName; ?></a></p>
		</div>
	</div>
</body>
</html>
