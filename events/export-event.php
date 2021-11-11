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
// set time zone and get current day, month, year and time
date_default_timezone_set('America/Chicago');
$date = time () ;
$day = date('d', $date) ;
$month = date('m', $date) ;
$year = date('Y', $date) ;
$currentTime = date('H:i:s', $date);


$upcoming = false;


//if year is set
if (isset($_GET['ID'])) {
     $ID = clean($_GET['ID']);

     if($ID == "upcoming"){

       $upcoming = true;
     }
}

if(isset($_GET['ID'])){
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

  $fm1 = new FileMaker('Web_Events', $database_server_ip, NULL, NUll);
  $CGIlayout = $fm1->getLayout('Web_Events'); 

  $compoundFind = $fm1->newCompoundFindCommand('Web_Events');

  if($upcoming != true){
    $request = $fm1->newFindRequest('web_events');
    $request->addFindCriterion('eventid', clean($_GET['ID']));
    $compoundFind->add(1,$request);
  }
  else{
    $findreq1 = $fm1->newFindRequest('Web_Events');
    $findreq2 = $fm1->newFindRequest('Web_Events');
    $findreq3 = $fm1->newFindRequest('Web_Events');
    $findreq4 = $fm1->newFindRequest('Web_Events');


    // first find request finds entries that starts in future year and published
    $findreq1->addFindCriterion('Date_Start_Year',  '>'.$year);
    $findreq1->addFindCriterion('Published', 'Yes');// only retrieve events to be published

    //second find request finds events starts in this year but in future month and published
    $findreq2->addFindCriterion('Date_Start_Year', $year);
    $findreq2->addFindCriterion('Date_Start_Month', '>='.$month);
    $findreq2->addFindCriterion('Published', 'Yes');// only retrieve events to be published

     
      //third find request finds events ends in future years and published
    $findreq3->addFindCriterion('Date_End_Year', '>'.$year);
    $findreq3->addFindCriterion('Published', 'Yes');

         //fourth find request finds events ends in this year but in future month and published
    $findreq4->addFindCriterion('Date_End_Year', $year);
    $findreq4->addFindCriterion('Date_End_Month','>='.$month);
    $findreq4->addFindCriterion('Published', 'Yes');

    if($type != 'all' && $type != ''){
      #[12/14/2018 DM] add or remove event tags here for multiple event calendar export
      if(in_array($type, array('Family-Friendly', 'Exhibit-Related', 'BigReadCU'))){
        $db_type = 'Type_Secondary';
      }
      
      else{
        $db_type = 'Type_Primary';
        if($type == 'Special-Event'){
          $type = 'Special Event';
        } 
      }

      $findreq1->addFindCriterion($db_type, $type);
      $findreq2->addFindCriterion($db_type, $type);
      $findreq3->addFindCriterion($db_type, $type);
      $findreq4->addFindCriterion($db_type, $type);
    
    }

    // compoundfind AND the above four find reuqests together
    $compoundFind->add(1,$findreq1);
    $compoundFind->add(2,$findreq2);
    $compoundFind->add(3,$findreq3);
    $compoundFind->add(4,$findreq4);

  }


  $result = $compoundFind->execute(); 
}
  if (!FileMaker::isError($result)) {
    $records = $result->getRecords(); 

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


    foreach ($records as $record){

        $ID = $record->getField('eventid');

        $eventTitle = strip_tags(html_entity_decode( $record->getField('Title')));

        $dateStart = $record->getField('Date_Start');
        $dateEnd = $record->getField('Date_End');
        $timeStart = $record->getField('Time_Start');
        $timeEnd = $record->getField('Time_End');
            
        $location = strip_tags(html_entity_decode($record->getField('Location')));
        
        $description = strip_tags(html_entity_decode($record->getField('Event_Description')));

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

//$description = str_replace("\n", "\\n", $description);

        echo
'BEGIN:VEVENT
CLASS:PUBLIC
UID:spurlock-event-'.$ID.'
SUMMARY;LANGUAGE=en-us:'.json_encode(stripslashes(html_entity_decode($eventTitle)), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT).'
DESCRIPTION:'.json_encode(html_entity_decode($description), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT).'
DTSTAMP:'.gmdate("Ymd\THis").'
DTSTART;TZID=America/Chicago:'.$start.'
DTEND;TZID=America/Chicago:'.$end.'
LOCATION:'.addcslashes(json_encode(html_entity_decode($location), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), ',').'
URL:https://www.spurlock.illinois.edu/events/event.php?ID='.$ID.'
END:VEVENT
';
  
    }

echo'
END:VCALENDAR';
}


?>