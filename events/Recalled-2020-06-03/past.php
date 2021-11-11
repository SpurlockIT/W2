<?php 
  require_once(dirname(__FILE__).'/../db-support/check_db.php');
  require_once(dirname(__FILE__).'/../db-support/clean.php');
  require_once(dirname(__FILE__).'/../db-support/fmREST/fmREST.php');

  $preview = clean($_GET["preview"]);
  date_default_timezone_set('America/Chicago');

  //get current date
  $date = time();
  $day = date('d', $date);
  $thismonth = date('m', $date);
  $thisyear = date('Y', $date);
  //checking if passed parameter in url is valid or not, and set year and month parameter
  $invalid_parameter = false;
  
  if(empty($_GET)){ //if there are no parameters set, set it to current month and year 
    $year = $thisyear; 
    $month = $thismonth;
  } else {
    if (isset($_GET['year'])) { // if the year is set
      $year_url = clean($_GET['year']);
      //if year is out of range or contain letters, the parameter is invalid set boolean to true
      if (($year_url<1995) || ($year_url>$thisyear) || (preg_match( '/[a-zA-Z]/', $year_url) )) { 
        $invalid_parameter = true;
      }    
      $year = $year_url;  
    } else { //if year parameter is not set
      //set year to equal to current year
      $invalid_parameter = true;
      $year = $thisyear;
    }

    if (isset($_GET['month'])) {
      $month_url = clean($_GET['month']);
      //if month is out of range or contain letters, the momth parameter is invalid and set boolean to true
      if (($month_url>12) || ($month_url<1) || ($month_url > $thismonth) && ($year_url >= $thisyear)||(preg_match( '/[a-zA-Z]/', $month_url))) { 
        $invalid_parameter = true;
      }
      $month = $month_url;
    } else {
      $invalid_parameter = true;
      $month = $thismonth;
    }
  }

  //filemaker setup
  $fm = new fmREST('db1.spurlock.illinois.edu', 'Web_Events', "", "", 'Web_Events');

  $request1['Date_Start_Year'] = $year;
  $request1['Date_Start_Month'] = $month;

  if($preview == 0) { 
    $request1['Published'] = 'Yes';
  }
  
  $findRequests = [
    $request1
  ];

  $rule1['fieldName'] = 'Date_Start';
  $rule1['sortOrder'] = 'ascend';
  $rule2['fieldName'] = 'Date_End';
  $rule2['sortOrder'] = 'ascend';
  $rule3['fieldName'] = 'Time_Start_New';
  $rule3['sortOrder'] = 'ascend';
  // combine sort rules into 2d array
  $sort = [
    $rule1,
    $rule2,
    $rule3
  ];

  $findData['query'] = $findRequests;
  $findData['sort'] = $sort;
  $result = $fm -> findRecords($findData);
  $records = $result['response']['data'];
  $err_code = $result['messages'][0]['code'];
?>

<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Past Events, Events, Spurlock Museum, U of I</title>

    <link rel="stylesheet" href="/css/app.css" />
    <link rel="stylesheet" type="text/css" href="/css/FlatIcons/flaticon.css"> 
    <!--<link rel="stylesheet" href="/css/off-pushy.css" />-->
    <!--added jquery for events-->
    <!-- original pushy jQuery location -->

  </head>
  <body>
    <!-- Offside Menu -->
    <!--entire site nav-->
    <aside aria-label="off-canvas site menu">
      <a id="top" tabindex="-1"></a>
      <div id="skiptocontent" tabindex="-1"><a href="#maincontent">skip to main content</a></div>
      <div id="off-canvas-menu" class="offside hide-for-large-up">
        <div class="row">
          <div class="small-12 columns">
            <a class="menu-btn right menu-btn-off-canvas-menu--close hide-for-large-up" href="#footer"><span class="icon x" title="Close Site Menu"></span><span class="visuallyhidden">Close site menu</span></a>
          </div>
        </div>
        <div class="row">
          <div class="small-12 columns">
            <ul class="no-bullet">
              <li><a class="button secondary" href="/"><span aria-hidden="true" class="icon home prepend"></span>Home</a></li>
              <li><a class="button secondary" href="/visit/"><strong>Visit</strong></a></li>
              <li><a class="button secondary" href="/events/"><span aria-hidden="true" class="icon events prepend"></span>Events</a></li>
              <li><a class="button secondary" href="/exhibits/">Exhibits</a></li>
              <li><a class="button secondary" href="/collections/">Collections</a></li>
              <li><a class="button secondary" href="/blog/"><span aria-hidden="true" class="icon blog prepend"></span>Blog</a></li>
            </ul>
            <hr>                    
            <ul class="no-bullet">
              <li><a class="button secondary small"  href="/educators/">Educators</a></li>
              <li><a class="button secondary small"  href="/giving/">Giving</a></li>
              <li><a class="button secondary small"  href="/contact/"><span aria-hidden="true" class="icon email prepend"></span>Contact</a></li>
              <li><a class="button secondary small"  href="/about/">About</a></li>
            </ul>
            <hr>
            <ul class="no-bullet">	
              <li><a class="button secondary small"  href="/search.html"><span aria-hidden="true" class="icon search prepend"></span>Search</a></li>
            </ul>
          </div>
        </div>
      </div>
      <!-- Site Overlay -->
      <div class="site-overlay"></div>
    </aside>

    <!-- Page Content -->
    <div id="container">
      <!--Navigation start-->
      <header id="site-header">
        <nav id="secondary-nav"  aria-label="secondary navigation" class="full-width-band show-for-medium-up">
          <div class="row">
            <div class="medium-12 columns"> 
              <a href="/search.html" class="menu-btn left"><span class="icon search" title="Search"></span><span class="visuallyhidden">Submit Search</span></a>
              <div class="show-for-large-up"> 
                <ul class="no-bullet inline-list right" style="padding-bottom:5px; padding-top:3px; margin-bottom:0px;">
                  <li><a href="/educators/">Educators</a></li>
                  <li><a href="/giving/">Giving</a></li>
                  <li><a href="/contact/">Contact</a></li>
                  <li><a href="/about/">About</a></li>
                </ul>
              </div>
            </div>
          </div>
        </nav>

        <div id="masthead" class="full-width-band">
          <div class="row">
            <div class="small-12 columns">
              <!-- Menu Button -->
              <a class="menu-btn menu-btn-off-canvas-menu button right hide-for-large-up" href="#footer" aria-expanded="false"><span class="icon hamburger" title="Menu"></span><span class="hidden-for-small-only">Menu</span></a>

              <a href="http://illinois.edu"><img class="i-block left" src="/img/core/icon_illinois-fc_300.png" alt="University of Illinois"></a>

              <div id="site-name">
                <a href="/"><span id="site-name-title">Spurlock<br class="hide-for-medium-up"> Museum</span><br class="hide-for-large-up">
                <span id="site-name-subtitle" class="show-for-medium-up"> of World Cultures <span id="site-name-illinois">at ILLINOIS</span></span></a>
              </div>

              <nav id="site-horizontal-menu" aria-label="main site menu" class="small-12 columns show-for-large-up">
                <ul class="inline-list left">
                  <li><a href="/"><span class="visuallyhidden"><strong>Home</strong></span><span aria-hidden="true" class="icon home" title="Home"></span></a></li>
                  <li><a href="/visit/"><strong>Visit</strong></a></li>
                  <li><a href="/events/"><strong>Events</strong></a></li>
                  <li><a href="/exhibits/"><strong>Exhibits</strong></a></li>
                  <li><a href="/collections/"><strong>Collections</strong></a></li>
                  <li><a href="/blog/"><strong>Blog</strong></a></li>
                </ul>                    
              </nav>
            </div>
          </div>
        </div>
        <!--entire site nav end-->
        <nav id="section-nav" class="full-width-band" aria-label="Events section navigation">
          <div class="row">
            <!--accordion start-->     
            <div class="small-12 columns">
              <div id="site-accordian" class="hide-for-medium-up tabpanel" role="tablist"> 
                <div id="tab1" class="tab accordian selected button expand onepointfive" aria-selected="false" aria-controls="panel1" aria-expanded="false" role="tab" tabindex="0">Events<span aria-hidden="true" class="icon chevron right"></span><span class="visuallyhidden"> Submenu</span></div> 
                <div id="panel1"  class="accordian accordion-panel" aria-labelledby="tab1" aria-hidden="true" role="tabpanel"> 
                  <ul class="controlList side-nav"> 
                    <li><a href="/events/">Events Overview</a></li>
                    <li><a href="/events/past.php">Past Events</a></li>
                  </ul> 
                </div> 
              </div>
            </div> 
            <!--accordion end--> 
            <!--horizontal menu start-->
            <div class="small-12 columns">
              <div id="sub-horizontal-menu" class="show-for-medium-up">
                <h2 style="" class="left">Events <span class="visuallyhidden">Submenu</span></h2>
                <ul class="sub-nav no-bullet">
                  <li><a class="secondary button" href="/events/">Events Overview</a></li>
                  <li><a class="secondary button" href="/events/past.php">Past Events</a></li>
                </ul>
              </div>
            </div> 
            <!--horizontal menu end--> 

          </div>
        </nav>
      </header>
      <!--Navigation End-->    
      <main>

        <!--Skip to Main Content Target-->
        <a id="maincontent" tabindex="-1"></a>

        <!--breadcrumbs begin-->
        <div class="row">
          <div class="small-12 columns">
            <ol class="breadcrumbs" aria-label="breadcrumb navigation">
              <li><a href="/">Home</a></li>
              <li><a href="/events/">Events</a></li>
              <li class="current">Past Events</li>
            </ol>
          </div>
        </div>
        <!--breadcrumbs end-->
        <div class="row">
          <div class="small-12 columns">
            <h1>Past Events</h1>
          </div>
        </div>

        <?php

          echo'<section id="past_events" class="full-width-band card-collection" aria-labelledby="past_events-heading">
            <div class="row">
              <div class=" small-12 columns" data-equalizer="content" data-equalizer-mq="medium-up">
                <h2 class="visuallyhidden" id="past_events-heading">Event Lists</h2>';

                $int = (int)$month;

                echo'<div class="row">
                  <div class="small-12 medium-8 medium-push-2 columns">
                    <h2 class="text-center">';
                      $month_char = date('F', mktime(0, 0, 0, $month, 10));
                      echo $month_char.  ' ' . $year;
                      echo"
                    </h2>
                  </div>";

                  $linkmonth = ltrim($month,0);
                  echo'<div class="small-6 medium-2 medium-pull-8 columns">';
                    if($linkmonth == 1) {
                      $previous_month = 12;
                      $previous_year = $year - 1;
                    } else {
                      $previous_month = $linkmonth - 1;
                      $previous_year = $year;
                    }
                    if($invalid_parameter == true){
                      echo "<a class='button disabled left'>&lt;</a></div><div class='small-6 medium-2 columns'>";
                    } else {
                      if(($linkmonth == 2) && ( $year == 2003) ){
                        echo "<a class='button disabled left'>".get_month_char($previous_month)." ".$previous_year."</a></div><div class='small-6 medium-2 columns'>";
                      } else if(($linkmonth > trim($thismonth) && trim($year) == trim($thisyear)) || trim($year) > trim($thisyear)){ 
                        echo "<a href='?month=" . $thismonth . "&amp;year=" . $year . "' class='button left'>".get_month_char($thismonth)." ".$year."</a></div><div class='small-6 medium-2 columns'>";
                      } else {
                        echo "<a href='?month=" . $previous_month . "&amp;year=" . $previous_year . "' class='button left'>".get_month_char($previous_month)." ".$previous_year."</a></div><div class='small-6 medium-2 columns'>";
                      }
                    }

                    if($linkmonth == 12) {
                      $upcoming_month = 1;
                      $upcoming_year = $year+1;
                    } else {
                      $upcoming_month = $linkmonth+1;
                      $upcoming_year = $year;
                    }

                    if($invalid_parameter == true){
                      echo "<a class='button disabled right'>&gt;</a></div><div class='small-6 medium-2 columns'> ";
                    } else {
                      if(($linkmonth >= ltrim($thismonth,0) && $year == $thisyear) || $year > $thisyear ){
                        echo "<a class='button disabled right'>".get_month_char($upcoming_month)." ".$upcoming_year."</a></div><div class='small-6 medium-2 columns'> ";
                      } else {
                        echo "<a href='?month=" . $upcoming_month . "&amp;year=" . $upcoming_year . "' class='button right'>".get_month_char($upcoming_month)." ".$upcoming_year."</a></div><div class='small-6 medium-2 columns'> ";
                      }
                    }
                  echo"</div>
                </div>";

                // JRK: 1/23/2020 formatting (indenting) for html gets really silly beyond this point for the rest of the PHP section, so I mostly ignored it. Would not recommend arbitrarily changing the indenting - everything lines up as of the aforementioned date. 
                $previous_month_char = date('F', mktime(0, 0, 0, $previous_month, 10));
                $next_month_char = date('F', mktime(0, 0, 0, $upcoming_month, 10));

                if($invalid_parameter == true){
                  echo'<div class="callout panel text-center">Invalid date. Try <a href = "?month='.$thismonth.'&amp;year='.$thisyear.'">'.date('F', mktime(0, 0, 0, $thismonth, 10)).' '.$thisyear.'</a>.</div>';
                } else {
                  // Displays the next event with legacy code
                  if($err_code == 401){
                    if(($linkmonth > trim($thismonth) && trim($year) == trim($thisyear)) || trim($year) > trim($thisyear)){
                        echo'<div class="callout panel text-center" id="empty_month"><p>There are no past events this month. Try <a href = "?month='.$thismonth.'&amp;year='.$year.'">'.date('F', mktime(0, 0, 0, $thismonth, 10)).' '.$year.'</a>.</p></div>';
                      } else {
                          echo'<div class="callout panel text-center" id="empty_month"><p>There are no past events this month. Try <a href = "?month='.$previous_month.'&amp;year='.$previous_year.'">'.$previous_month_char.' '.$previous_year.'</a>.</p></div>';
                      }
                  } else if($err_code != 0){
                    echo'<div class="callout panel text-center">Sorry, our database is currently under maintenence. Please try again later.</div>';
                  } else {   
                    echo '<div class="small-12 columns" data-equalizer="date-and-caption" data-equalizer-mq="medium-up"><ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-3" data-equalizer="tags" data-equalizer-mq="medium-up">';
                    $event_count = 0;

                    foreach($records as $record) {
                      $show = false;
                      $maxEventID = $record['fieldData']['max_eventID'];
                      $eventTitle = html_entity_decode( $record['fieldData']['Title']);
                      $dateStart = $record['fieldData']['Date_Start'];
                      $dateEnd = $record['fieldData']['Date_End'];
                      $type = $record['fieldData']['Type'];

                      if(!empty($dateEnd)) {
                        $endm = substr($dateEnd,0,2);
                        $endy = substr($dateEnd,-4,4);
                        $endd = substr($dateEnd,3,2);
                      }

                      //Added by MTR [7.15.11]
                      $eventID = $record['fieldData']['eventid'];
                      global $day, $thismonth, $thisyear, $month, $year;
                      $dateStart = $record['fieldData']['Date_Start'];
                      $dateEnd = $record['fieldData']['Date_End'];
                      $displayStart = $record['fieldData']['Date_Start_Display'];
                      $displayEnd = $record['fieldData']['Date_End_Display'];
                      $timeStart = $record['fieldData']['Time_Start'];
                      $timeStart2 = $record['fieldData']['Time_Start_New'];
                      $timeEnd2 = $record['fieldData']['Time_End_New'];
                      $timeEnd = $record['fieldData']['Time_End'];
                      $cost = $record['fieldData']['Cost'];
                      $type1 = $record['fieldData']['Type_Primary'];
                      $type2 = $record['fieldData']['Type_Secondary'];
                      $ageRange = $record['fieldData']['Age_Range'];
                      $description = html_entity_decode( $record['fieldData']['Event_Description']);
                      $photo = $record['fieldData']['image_preview_path'];
                      $photoAlt = $record['fieldData']['main_alt_text'];
                      $photo2 = $record['fieldData']['extra_image_path'];
                      $photoAlt2 = $record['fieldData']['extra_image_alt_text'];
                      $type = $record['fieldData']['Type'];
                      $tags = preg_split("/(\r\n|\n|\r)/", $type2);
                      $diff = (time() -  strtotime($timeStart2))/60 ;

                      $startm = substr($dateStart,0,2);
                      $starty = substr($dateStart,-4,4);
                      $startd = substr($dateStart,3,2);
                      $start_date_format = $starty.'-'.$startm.'-'.$startd;
                      $end_date_format = $endy.'-'.$endm.'-'.$endd;

                      $startdate = strtotime($start_date_format);      
                      $startDateTomorrow = strtotime('+1 day', $startdate);
                      $enddate = strtotime($end_date_format);
                      $endDateTomorrow = strtotime('+1 day', $enddate);
                      $now = time();
                      //$time= date("h:i a");

                      $dayofweek = date('D', strtotime($start_date_format));
                      $end_day_of_week = date('D', strtotime($end_date_format));

                      # Filter past events.
                      # If event month is the same as current month and event year is the same as current year...                                  
                      if (($startm==$month) && ($starty==$year) ) {
                        # If the event has an end date...
                        if (strlen($dateEnd) != 0) {
                          # If the event end date has passed, show the event.
                          if ($endDateTomorrow < $now){
                            $show = true;
                          }
                        # Else (the event does not have an end date)...
                        } else {
                          # If the event start date has passed, show the event
                          if ($startDateTomorrow < $now){
                            $show = true;
                          }
                        }
                      }
                      # End filter past events.

                      # Checks whether event is a test event.
                      $test_event = $record['fieldData']['test_flag'];
                      if ($test_event == "Test Event") {
                        if (strpos(basename(__FILE__, '.php'), 'test') == false) {
                          $show = false;
                        }
                      } 

                      # Checks whether event is an exhibit.
                      if ($type1 == "Exhibit") {
                        $show = false;
                      }

                      if ($show == true) {
                        $event_count++;  
                        echo "<li class=\"filter_option ";
                        # Add all type2 tags (with echoed leading space) and any two-word type1 tags.
                        echo $type1;

                        if (strpos($type2,'Family-Friendly') !== false) {
                          echo "Family-Friendly";
                        }
                        if (strpos($type2, 'Exhibit-Related') !== false) {
                          echo "Exhibit-Related";
                        }

                        if (strpos($type2, 'ALERT') !== false) {
                          echo "ALERT";
                        }
                        echo "\"><div class='card'>";

                        //convert numeric month to month character
                        $month_char = get_month_char($month);

                        # Get month abbreviations for event start date.
                        $month_char=$startm;
                        switch ($startm) {
                          case "1":
                            $month_char="JAN";
                            break;
                          case "2":
                            $month_char="FEB";
                            break;
                          case "3":
                            $month_char="MAR";
                            break;
                          case "4":
                            $month_char="APR";
                            break;
                          case "5":
                            $month_char="MAY";
                            break;
                          case "6":
                            $month_char="JUN";
                            break;
                          case "7":
                            $month_char="JUL";
                            break;
                          case "8":
                            $month_char="AUG";
                            break;
                          case "9":
                            $month_char="SEP";
                            break;
                          case "10":
                            $month_char="OCT";
                            break;
                          case "11":
                            $month_char="NOV";
                            break;
                          case "12":
                            $month_char="DEC";
                            break;                    
                        }

                        # Get month abbreviations for event end date, if applicable.
                        $end_month_char = $endm;
                        switch ($endm) {
                          case "1":
                            $end_month_char="JAN";
                            break;
                          case "2":
                            $end_month_char="FEB";
                            break;
                          case "3":
                            $end_month_char="MAR";
                            break;
                          case "4":
                            $end_month_char="APR";
                            break;
                          case "5":
                            $end_month_char="MAY";
                            break;
                          case "6":
                            $end_month_char="JUN";
                            break;
                          case "7":
                            $end_month_char="JUL";
                            break;
                          case "8":
                            $end_month_char="AUG";
                            break;
                          case "9":
                            $end_month_char="SEP";
                            break;
                          case "10":
                            $end_month_char="OCT";
                            break;
                          case "11":
                            $end_month_char="NOV";
                            break;
                          case "12":
                            $end_month_char="DEC";
                            break;
                        }

                        if($photo) {
                          # If the event has a photo in the database, display it.
                          echo "<a href='/events/event.php?ID=".$eventID."'><img src='/img/" . $photo . "' alt='" . $photoAlt ." ". date('n/j/Y',strtotime($dateStart))."'></a>";
                        } else { # Else display a placeholder image based on the event's type.
                          echo"<a href='/events/event.php?ID=".$eventID."' style='text-decoration:none;'>";
                          echo "<div class='crop wide-crop placeholder ".$type1."'>";
                          echo "<div class='icon-image'>";
                          echo "<span class='visuallyhidden'>".$type1."</span>";
                          echo "<span aria-hidden = 'true' class='icon ";

                          if ($type1 == "Special Event") {
                            echo"event-Special-Event";
                          } else {
                            echo "event-".$type1;
                          }

                          echo "'><span class=\"visuallyhidden\">"." ". date('n/j/Y',strtotime($dateStart))."</span></span>";
                          echo "</div>";
                          echo "</div>";
                          echo "</a>"; 
                        }
                        echo "<div data-equalizer-watch='date-and-caption'>
                        <div class='date-plaque-alt'>";

                        # If the event has both a start date and an end date, display both.
                        if (!empty($dateEnd) && ($dateStart != $dateEnd)) {
                          echo  "<div>
                          <span class='month'>".$month_char."</span><span class='day'>".ltrim((substr($dateStart,3,2)), '0')."</span>
                          <div class='right'>
                          <span class='dayofweek'>".$dayofweek."</span>
                          <span class='year'>". $starty.   "</span>
                          </div>
                          </div>
                          <div> 
                          <span class='month '>" . "<span class='date-separator'>TO </span>".$end_month_char."</span><span class='day'>".ltrim((substr($dateEnd,3,2)), '0')."</span>
                          <div class='right'>
                          <span class='dayofweek'>".$end_day_of_week."</span>
                          <span class='year'>". $endy.  "</span>
                          </div>
                          </div>";
                          # Else (the event does not have an end date) display the start date only.
                        } else {
                          echo "<div>
                          <span class='month'>"  . $month_char . "</span>
                          <span class='day'>" . ltrim((substr($dateStart,3,2)), '0') . "</span>
                          <div class='right'>
                          <span class='dayofweek'>".$dayofweek."</span>
                          <span class='year'>". $starty.  "</span>
                          </div>
                          </div>";
                        }        

                        echo "</div>
                        <div class='card-content image-caption'>
                        <div class='card-maininfo'>"; 
                        echo'<span class="title"><a href="event.php?ID='.$eventID.'">';
                        echo $eventTitle . '</a></span>';
                        # If the event has a start time...
                        if(!empty($timeStart)) { 
                          # If the event has an end date that is different from it's start date, display the start time and start date of the event.
                          if (!empty($dateEnd) && ($dateStart != $dateEnd)) {
                            echo "<span><span aria-hidden='true' class='icon time prepend' title='Event Time'></span>".$timeStart . " ". date('n/j/Y',strtotime($dateStart)) . "&ndash;";
                          } else { # Else (the event has an end date that is the same as it's start date) display the start time of the event.
                            echo "<span><span aria-hidden='true' class='icon time prepend' title='Event Time'></span>
                            <span class='visuallyhidden'>Event Time: </span>".$timeStart;
                          }
                          # If the event has an end time or an end date...
                          if (!empty($timeEnd) || !empty($dateEnd)) {
                            # If the event has an end time...
                            if(!empty($timeEnd)){
                              # If the event has an end date that is different from its start date...
                              if (!empty($dateEnd) && ($dateStart != $dateEnd)) {
                                # Display the end time and end date of the event.
                                echo $timeEnd . " ". date('n/j/Y', strtotime($dateEnd)); 
                              } else { # Else (the event has an end date that is the same as its start date) display the end time of the event
                                echo "&ndash;".$timeEnd; 
                              }    
                            } else {  # Else (the event does not have an end time) display the end date of the event.
                              echo date('n/j/Y', strtotime($dateEnd));
                            }
                          }  
                          echo "</span>"; 
                        }
                        echo '</div>
                        </div>
                        </div>';

                        //event tag
                        echo "<div><ul class='tag-band' data-equalizer-watch='tags'>";
                        if(!empty($type1)){
                          // [06.02.16] AYX: replaced spaces with dashes so link/anchor works 
                          echo' <li><a href="/events/#'.str_replace(' ', '-', $type1).'"><span class="tag-alt">' . $type1 .'<span class="visuallyhidden"> tag</span></span></a></li> ';
                        }

                        if(!empty($type2)){
                          for ($i = 0; $i < count($tags); ++$i) {
                            // [06.02.16] AYX: replaced spaces with dashes so link/anchor works 
                            if ($tags[$i] == '#InHerCloset') {
                              echo '<li><a href="/events/'.str_replace(' ', '-', $tags[$i]).'"><span class="tag-alt">'. $tags[$i] . '<span class="visuallyhidden"> tag</span></span></a></li> ';
                            } else {
                              echo '<li><a href="/events/#'.str_replace(' ', '-', $tags[$i]).'"><span class="tag-alt">'. $tags[$i] . '<span class="visuallyhidden"> tag</span></span></a></li> ';
                            }
                          }
                        }

                        if((empty($type1)) && (empty($type2))){
                          echo' <li><a href="/events/#'.$type.'"><span class="tag-alt">' . $type .'<span class="visuallyhidden"> tag</span></span></a></li>';
                        }   
                        echo"</ul></div></div></li>"; 
                      } 
                    }
                    //close the event list
                    echo "</ul></div>";

                    // AYX [12.01.16]: Display a message if current month doesn't have past events to display yet
                    if($event_count < 1 && $month == $thismonth){
                      echo'<div class="callout panel text-center" id="empty_month"><p>There are no past events yet this month. Try 
                    <a href = "?month=' . $previous_month .'&amp;year=' . $previous_year. '">'. $previous_month_char .' ' . $previous_year. '</a>.</p>';
                      echo'</div>';
                    }
                  }
                }

                echo'<div class="row">
                <div class="small-6 medium-4 large-3 columns">';

                //if the parameter from url is invalid, button leads to default past event page
                if($invalid_parameter == true){
                  echo "<a class='button disabled left'>&lt;</a></div><div class='small-6 medium-4 large-3 columns'>";
                } else {
                  if(($linkmonth == 2) && ( $year == 2003) ){
                    echo "<a class='button disabled left'>".get_month_char($previous_month)." ".$previous_year."</a></div><div class='small-6 medium-4 large-3 columns'>";
                  } else if(($linkmonth > trim($thismonth) && trim($year) == trim($thisyear)) || trim($year) > trim($thisyear)){ 
                    echo "<a href='?month=" . $thismonth . "&amp;year=" . $year . "' class='button left'>".get_month_char($thismonth)." ".$year."</a></div><div class='small-6 medium-4 large-3 columns'>";
                  } else {
                  echo "<a href='?month=" . $previous_month . "&amp;year=" . $previous_year . "' class='button left'>".get_month_char($previous_month)." ".$previous_year."</a></div><div class='small-6 medium-4 large-3 columns'>";
                  }
                }

                if($linkmonth == 12) {
                  $upcoming_month = 1;
                  $upcoming_year = $year+1;
                } else {
                  $upcoming_month = $linkmonth+1;
                  $upcoming_year = $year;
                }

                if($invalid_parameter == true){
                  echo "<a class='button disabled right'>&gt;</a> ";
                } else {
                  if(($linkmonth >= ltrim($thismonth,0) && $year == $thisyear) || $year > $thisyear ){
                    echo "<a class='button disabled right'>".get_month_char($upcoming_month)." ".$upcoming_year."</a> ";
                  } else {
                    echo "<a href='?month=" . $upcoming_month . "&amp;year=" . $upcoming_year . "' class='button right'>".get_month_char($upcoming_month)." ".$upcoming_year."</a> ";
                  }
                }
                  echo'</div>
                </div>
              </div>
            </div>
          </section>';

          function get_month_char($month){
            switch ($month) {
              case "1":
                return "JAN";
                break;
              case "2":
                return "FEB";
                break;
              case "3":
                return "MAR";
                break;
              case "4":
                return "APR";
                break;
              case "5":
                return "MAY";
                break;
              case "6":
                return "JUN";
                break;
              case "7":
                return "JUL";
                break;
              case "8":
                return "AUG";
                break;
              case "9":
                return "SEP";
                break;
              case "10":
                return "OCT";
                break;
              case "11":
                return "NOV";
                break;
              case "12":
                return "DEC";
                break;
            }
          }

        ?>
      </main>

      <!--recommended pages begin-->  
      <aside id="related-recommended-pages" class="full-width-band">
        <div class="row">
          <div class="small-12 columns">
            <div class="panel">
              <h2 class="text-center subheader">Recommended Pages</h2>

              <div class="row">  
                <div class="small-12 medium-6 medium-centered large-centered columns ">
                  <a class='button expand' href='/visit/facilities/rentals/'>Rentals</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </aside>
      <!--recommended pages end-->

      <!-- {Footer} -->
      <a id="footer" tabindex="-1"></a>
      <div data-interchange="[/_footer.html, (small)]"></div>
      <noscript>
        <footer>
          <div class="row">
            <div class="small-12 columns">
              [Javascript Required] but you can access the {<a href="/_footer.html">FOOTER</a>} through direct link.<br><br>
            </div>
          </div>
        </footer>
      </noscript>

    </div>

    <script src="/js/min/app-min.js"></script>
    <script type="text/javascript" src="https://emergency.webservices.illinois.edu/illinois.js"></script>
    <script>
      $(document).foundation({
      equalizer : {
        // Specify if Equalizer should make elements equal height once they become stacked.
        equalize_on_stack: true,
        }
      });
    </script>  

  </body>
</html>
