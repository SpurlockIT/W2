<?php
//header("Pragma: public");
//header("Expires: 0");
// ... set 'Content-Type' to 'text/calendar' and 'charset=' to 'utf-8'.
header('Content-Description: File Transfer');
header('Content-Type: text/calendar');
//header('Content-Type: application/octet-stream');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

require_once(dirname(__FILE__).'/../db-support/fm/FileMaker.php'); //packaged with filemaker.  Not modified.
require_once(dirname(__FILE__).'/../db-support/check_db.php');
require_once(dirname(__FILE__).'/../db-support/clean.php');
require_once(dirname(__FILE__).'/../db-support/fmREST/fmREST.php');

// set time zone and get current day, month, year and time
date_default_timezone_set('America/Chicago');
$date = time ();
$day = date('d', $date);
$month = date('m', $date);
$year = date('Y', $date);
$currentTime = date('H:i:s', $date);

$upcoming = false;

//if year is set
if (isset($_GET['ID'])) {
  $ID = clean($_GET['ID']);
  if($ID == "upcoming"){
    $upcoming = true;
  }
}

//if(isset($_GET['ID'])){ // JRK: shouldn't this be type? 
if(isset($_GET['type'])){
  $type = clean($_GET['type']);
}

if($upcoming == true){
  $filename='Spurlock-Upcoming-Events-'.$month.'-'.$day.'-'.$year.'.ics';
  header('Content-Disposition: attachment; filename='.$filename);
}
else{
  header('Content-Disposition: attachment; filename=Spurlock-Event-'.$ID.'.ics');
}

if(check_db("Web_Events") == 1){
  $fm = new fmREST('db1.spurlock.illinois.edu', 'Web_Events', "", "", 'Web_Events');

  // find utility article
  $utilityRequest1['PrimaryKey'] = "7ACC916F-7466-BC41-B551-4120C2531DCA";    
  $utilityFindRequests = [
    $utilityRequest1
  ];
  $utilityFindData['query'] = $utilityFindRequests;
  $utilityRecordFind = $fm -> findRecords($utilityFindData, "utility");
  $utilityRecord = $utilityRecordFind['response']['data'][0];

  $types_main = $utilityRecord['fieldData']['c_event_types_main'];
  $types_secondary = $utilityRecord['fieldData']['c_event_types_auxiliary'];

  $types_secondary_array = explode("\r", $types_secondary);

  if($upcoming != true){
    $request['eventid'] = clean($_GET['ID']);
    $findRequests = [
      $request
    ];
  } else {
    $request1['Date_Start_Year'] = '>'.$year;
    $request2['Date_Start_Year'] = $year;
    $request2['Date_Start_Month'] = '>='.$month;
    $request3['Date_End_Year'] = '>'.$year;
    $request4['Date_End_Year']= $year;
    $request4['Date_End_Month']= '>='.$month;

    // only retrieve published events
    $request1['Published'] = 'Yes';
    $request2['Published'] = 'Yes';
    $request3['Published'] = 'Yes';
    $request4['Published']= 'Yes';

    if($type != 'all' && $type != ''){
      #[12/14/2018 DM] add or remove event tags here for multiple event calendar export
      if(in_array($type, $types_secondary_array)){ 
        $db_type = 'Type_Secondary';
      }
      
      else{
        $db_type = 'Type_Primary';
        if($type == 'Special-Event'){
          $type = 'Special Event';
        } 
      }

      $findreq1[$db_type] = $type;
      $findreq2[$db_type] = $type;
      $findreq3[$db_type] = $type;
      $findreq4[$db_type] = $type;
    }

    // combine find requests into a 2D array
    $findRequests = [
      $request1,
      $request2,
      $request3,
      $request4
    ];
  }
  $findData['query'] = $findRequests;
  $result = $fm -> findRecords($findData);
  $records = $result['response']['data'];
}

echo 
'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Spurlock Museum//Spurlock Museum Events//EN
X-WR-CALNAME:Spurlock Museum Events
CALSCALE:GREGORIAN  
METHOD:PUBLISH
BEGIN:VTIMEZONE
TZID:America/Chicago
X-LIC-LOCATION:America/Chicago
BEGIN:DAYLIGHT
TZOFFSETFROM:-0600
TZOFFSETTO:-0500
TZNAME:CDT
DTSTART:19700308T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:-0500
TZOFFSETTO:-0600
TZNAME:CST
DTSTART:19701101T020000
RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU
END:STANDARD
END:VTIMEZONE
';

foreach ($records as $record) {
  $ID = $record['fieldData']['eventid'];
  $eventTitle = strip_tags(html_entity_decode( $record['fieldData']['Title']));
  $dateStart = $record['fieldData']['Date_Start'];
  $dateEnd = $record['fieldData']['Date_End'];
  $timeStart = $record['fieldData']['Time_Start'];
  $timeEnd = $record['fieldData']['Time_End'];  
  $location = strip_tags(html_entity_decode($record['fieldData']['Location']));
  $description = str_replace("\r", "\n", strip_tags(html_entity_decode($record['fieldData']['Event_Description'])));
  $checkm = substr($dateStart,0,2);
  $checky = substr($dateStart,-4,4);
  $checkd = substr($dateStart,3,2);

  if($upcoming == true){
    //if not in the timeframe of upcoming events, ignore and continue to next record
    if(!(( ($checkm==$month) && ($checky==$year) && ($checkd > $day)) || ( ($checkm>$month) && ($checky==$year)) ||( $checky>$year) )){
      continue;
    }
  }

  if($upcoming == false){
    $filename=str_replace(' ', '-', strip_tags($eventTitle)).'.ics';
  }

  if($dateEnd == ""){
    $dateEnd = $dateStart;
  }

  //if end time not specified but start time is, set end time as 2 hours after start
  if($timeStart != "" && $timeEnd == ""){
    $timeEnd = date('His', strtotime('+2 hours', strtotime($timeStart)));
  }

  $start_time = strtotime($dateStart. ' ' .$timeStart);
  $end_time =strtotime($dateEnd. ' ' .$timeEnd);

  $start = date('Ymd\THis', $start_time);
  $end = date('Ymd\THis', $end_time);

  // Since some calendars don't add 0 second events, we need to remove time if there is none...
  if($timeStart == ""){
    $start = date('Ymd', $start_time);
  }

  if($timeEnd == ""){
    $end = date('Ymd', $end_time);
  }

echo
'BEGIN:VEVENT
CLASS:PUBLIC
UID:spurlock-event-'.$ID.'
SUMMARY;LANGUAGE=en-us:'.json_encode(stripslashes(html_entity_decode($eventTitle)), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT).'
DESCRIPTION:'.json_encode(html_entity_decode($description), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT).'
DTSTAMP:'.gmdate("Ymd\THis").'
DTSTART;TZID=America/Chicago:'.$start.'
DTEND;TZID=America/Chicago:'.$end.'
LOCATION:'.addcslashes(json_encode(stripslashes(html_entity_decode($location)), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), ',').'
URL:https://www.spurlock.illinois.edu/events/event.php?ID='.$ID.'
END:VEVENT
';  
}
echo'
END:VCALENDAR';

?>