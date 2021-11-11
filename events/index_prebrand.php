<?php
//Events Index Page
  require_once(dirname(__FILE__).'/../db-support/fm/FileMaker.php'); // packaged with Filemaker.  Not modified.
  require_once(dirname(__FILE__).'/../db-support/check_db.php');
  require_once(dirname(__FILE__).'/../db-support/clean.php');

  //get the preview parameter from the url
  $preview = clean($_GET["preview"]);
  // set time zone and get current day, month, year and time
  date_default_timezone_set('America/Chicago');
  $date = time ();
  $day = date('d', $date);
  $month = date('m', $date);
  $year = date('Y', $date);
  $currentTime = date('H:i:s', $date);
?>

<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Events, Spurlock Museum, U of I</title>
    <link rel="stylesheet" href="/css/app_prebrand.css" />
    <link rel="stylesheet" type="text/css" href="/css/FlatIcons/flaticon.css">
    <script src="/js/modernizr.js"></script>
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
                        <a class="menu-btn right menu-btn-off-canvas-menu--close hide-for-large-up" aria-expanded="false" href="#footer"><span class="icon x" title="Close Site Menu"></span><span class="visuallyhidden">Close site menu</span></a>



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
                          <a class="menu-btn menu-btn-off-canvas-menu button right hide-for-large-up" href="#footer" aria-expanded="false"><span class="icon hamburger" title="Menu"></span><span class="hidden-for-small-only"> Menu</span></a>

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

                        <div id="tab1" class="tab accordian selected button expand onepointfive" aria-selected="false" aria-controls="panel1" aria-expanded="false" role="tab" tabindex="0">
                          Events<span aria-hidden="true" class="icon chevron right"></span><span class="visuallyhidden"> Submenu</span>
                        </div>

                        <div id="panel1"  class="accordian accordion-panel" aria-labelledby="tab1" aria-hidden="true" role="tabpanel">
                          <ul class="controlList side-nav">
                                <li><a href="/events/">Overview</a></li>
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
                                <li><a class="secondary button" href="/events/">Overview</a></li>
                                <li><a class="secondary button" href="/events/past.php">Past Events</a></li>
                              </ul>
                          </div>

                        </div>
                      <!--horizontal menu end-->




                  </div>
                </nav>
               

            </header>
            <!--Navigation End-->

           
      <!-- main content begin-->
  <main>

   <!--Skip to Main Content Target-->
                <a id="maincontent" tabindex="-1"></a>


   <!--breadcrumbs begin-->
              <div class="row">
                    <div class="small-12 columns">
                      <ol class="breadcrumbs" aria-label="breadcrumb navigation">
                       <li><a href="/">Home</a></li>
                        <li class="current">Events</li>
              
                      </ol>
                    </div>
              </div>
   <!--breadcrumbs end-->


<div class="row">
  <div class="small-12 columns">
    <h1>Events</h1>
    <div class="panel callout">
      <p><strong>Covid-19 update:</strong> With the easing of restrictions, <strong>some of our events are now offered at Spurlock, but others are being offered online</strong>. For events offered online, we’ll be using Zoom, <a href="https://www.facebook.com/SpurlockMuseum/" class="external">Facebook<span>(external link)</span></a> video, and other platforms to connect with you safely. See individual event listings below for specific details.</p>
    </div>
  </div>
</div>

     
  <!-- today block begin -->
     <section id="today"  class="full-width-band cream" aria-labelledby="today-heading">
     
           <!-- <div data-interchange="[../includes/_today.html, (small)]"></div> -->
      <div class="row" data-equalizer="today-cards" data-equalizer-mq="medium-up">
        <div class="small-12 medium-10 medium-offset-1 columns">
          <!--<div class="panel full-width-band white">-->
          <div data-equalizer-mq="medium-up">
            <h2 id="today-heading">Today's Events</h2>

            <!--<p>Today is <span id="today-date"></span><span id="today-hours"></span></p>-->
<div class="small-12 columns" data-equalizer="date-and-caption" data-equalizer-mq="medium-up">
<ul class="small-block-grid-1 medium-block-grid-1 large-block-grid-2" data-equalizer="today-tags" data-equalizer-mq="medium-up">
         
      <?php
                //This check_db function will return 1 if database is successfully connected and 0 if error
                if(check_db("web_events") == 1){
            
                    $fm = new FileMaker('Web_Events', $database_server_ip, NULL, NULL);

                    // specify db query
                    $compoundFind = $fm->newCompoundFindCommand('Web_Events');
                    $findreq1 = $fm->newFindRequest('Web_Events');
                    $findreq2 = $fm->newFindRequest('Web_Events');
                    $findreq3 = $fm->newFindRequest('Web_Events');
                    $findreq4 = $fm->newFindRequest('Web_Events');
                    // first find request finds entries that starts in future year and published
                    $findreq1->addFindCriterion('Date_Start_Year',  '>'.$year);

                    if($preview != 1){
                       $findreq1->addFindCriterion('Published', 'Yes');// only retrieve events to be published
                    }
                     
                   
                    //second find request finds events starts in this year but in future month and published

                    $findreq2->addFindCriterion('Date_Start_Year', $year);
                    $findreq2->addFindCriterion('Date_Start_Month', '>='.$month);
                        if($preview != 1){
                       $findreq2->addFindCriterion('Published', 'Yes');// only retrieve events to be published
                    }
                     
                      //third find request finds events ends in future years and published
                    $findreq3->addFindCriterion('Date_End_Year', '>'.$year);
                        if($preview != 1){

                      $findreq3->addFindCriterion('Published', 'Yes');
                      }

                         //fourth find request finds events ends in this year but in future month and published
                     $findreq4->addFindCriterion('Date_End_Year', $year);
                     $findreq4->addFindCriterion('Date_End_Month','>='.$month);
                        if($preview != 1){

                       $findreq4->addFindCriterion('Published', 'Yes');
}
                    // compoundfind AND the above four find requests together
                    $compoundFind->add(1,$findreq1);
                    $compoundFind->add(2,$findreq2);
                    $compoundFind->add(3,$findreq3);
                    $compoundFind->add(4,$findreq4);

                    // add sort rule so events are displayed in ascend order (most recently one is displayed first)
                    $compoundFind->addSortRule('Date_Start', 1, FILEMAKER_SORT_ASCEND);
                    $compoundFind->addSortRule('Date_End', 2, FILEMAKER_SORT_ASCEND);
                    $compoundFind->addSortRule('Time_Start_New', 3, FILEMAKER_SORT_ASCEND);

                    $result = $compoundFind->execute();
                    $records = $result->getRecords();
                        

                    //keeps track of the number of events in today block.
                    $today_count = 0;
                 
                    foreach($records as $record){

                            //set the boolean variable to default, if it is a today event, change to true
                            $show_today = false;
                           
                            $eventTitle = html_entity_decode( $record->getField('Title'));
                            $dateStart = $record->getField('Date_Start');
                            $dateEnd = $record->getField('Date_End');
                            $type = $record->getField('Type');
                           
     
                            $eventID = $record->getField('eventid');
                         

                            global $day, $month, $year;
                            
                            
                            $displayStart = $record->getField('Date_Start_Display');
                            $displayEnd = $record->getField('Date_End_Display');
                            $timeStart = $record->getField('Time_Start');
                            $timeStart2 = $record->getField('Time_Start_New');
                            $timeEnd = $record->getField('Time_End');
                            $timeEnd2 = $record->getField('Time_End_New');
                           
                             if(!empty($dateEnd)){
                            $endm = substr($dateEnd,0,2);
                            $endy = substr($dateEnd,-4,4);
                            $endd = substr($dateEnd,3,2);

                            }


                            $type1 = $record->getField('Type_Primary');
                            $type2 = $record->getField('Type_Secondary');

                       
                          
                            $description = html_entity_decode( $record->getField('Event_Description'));

                            $photo = $record->getField('image_preview_path');
                            $photoAlt = $record->getField('main_alt_text');
                            $photo2 = $record->getField('extra_image_path');
                            $photoAlt2 = $record->getField('extra_image_alt_text');
                            
                            
                            $type = $record->getField('Type');
                            $tags = preg_split("/(\r\n|\n|\r)/", $type2);
 
      
                            $checkm = substr($dateStart,0,2);
                            $checky = substr($dateStart,-4,4);
                            $diff = (time() -  strtotime($timeStart2))/60 ;
                            $checkd = substr($dateStart,3,2);

                            $date_format = $checky . '-' . $checkm . '-' . $checkd;
                            $end_date_format = $endy . '-' . $endm . '-' . $endd;
                            
                            $dayofweek = date('D', strtotime($date_format));
                            $end_day_of_week = date('D', strtotime($end_date_format));
                         
                            # Begin today's events filtering.
                            # If the event occurs in the current month and year...
                            #added to line #370 to check if event occurs in same end month or end year and if it is an exhibit
                            if ( ($checkm==$month || $endm == $month) && ($checky==$year || $endy==$year)) {
                              # If the event is an exhibit...
                              if ($type1 == "Exhibit") {
                                # If the current date is the same as the start date of the exhibit, or current date is the same as the end date of the exhibit, display the event.
                                if (date('m/d/y', strtotime($dateStart)) == date('m/d/y', $date)) {
                                    $show_today = true;
                                } elseif (date('m/d/y', strtotime($dateEnd)) == date('m/d/y', $date)) {
                                    $show_today = true;
                                }
                                # Else (if the event is not an exhibit)...
                                } else {
                                    # If the event has an end date...
                                    if (strlen($dateEnd) != 0) {
                                    # If the event start date has passed or the event starts today, and the event end date is today or a future day (i.e., the event is ongoing), show the event.
                                    if (date('m/d/y', $date) >= date('m/d/y', strtotime($dateStart)) && date('m/d/y', $date) <= date('m/d/y', strtotime($dateEnd))) {
                                      $show_today = true;
                                    }
                                  # Else (if the event start date is the same as the current date, show the event).
                                    } else {
                                      if (date('m/d/y', $date) == date('m/d/y', strtotime($dateStart))) {
                                        $show_today = true;
                                    }
                                }
                              }
                            }
                            # End today's events filtering.

                            # Display test events within test files only.
                            $test_event = $record->getField('test_flag');
                            if ($test_event == "Test Event") {
                              if (strpos(basename(__FILE__, '.php'), 'test') !== false) {

                              } else{
                                $show_today = false;
                              }
                            }


                              if($show_today == true){
                                $today_count++;
                                echo"<li class=\"filter_option ";
                 //add all type2 tags (with echoed leading space) and any two-word type1 tags.
                                  if ($type1 == "Special Event")
                                    {
                                      echo"Special-Event";
                                    }else{
                                      echo $type1;

                                    }

                                  if (strpos($type2,'Family-Friendly') !== false)
                                      {
                                      echo" Family-Friendly";
                                      }

                                  if (strpos($type2, 'Exhibit-Related') !== false)
                                      {
                                      echo" Exhibit-Related";
                                      }
//                                  if (strpos($type2, 'InHerCloset') !== false)
//                                      {
//                                      echo" InHerCloset";
//                                      }
                           

                            echo "\">

                            <div class='card'>

                            ";



                            $month_char=$checkm;
                            switch ($checkm) {
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

                          echo "
                          <div data-equalizer-watch='date-and-caption' class='card'>
                            <div class='date-plaque-alt'>";

                                if (!empty($dateEnd) && ($dateStart != $dateEnd)) { # add condition to display start and end dates next to times as per sketch
                                 echo  "<div>
                                          <span class='month'>"  . $month_char . "</span><span class='day'>" . ltrim((substr($dateStart,3,2)), '0') .  "</span>
                                          <div class='right'>
                                            <span class='dayofweek'>".$dayofweek."</span>
                                              <span class='year'>". $checky.   "</span>
                                          </div>
                                        </div>
                                        <div>
                                          <span class='month '>" . "<span class='date-separator'>TO </span>". $end_month_char . "</span><span class='day'>" . ltrim((substr($dateEnd,3,2)), '0') . "</span>
                                          <div class='right'>
                                            <span class='dayofweek'>".$end_day_of_week."</span>
                                            <span class='year'>". $endy.  "</span>
                                          </div>
                                        </div>";
                                } else {
                                  echo "<div>
                                          <span class='month'>"  . $month_char . "</span>
                                          <span class='day'>" . ltrim((substr($dateStart,3,2)), '0') . "</span>
                                          <div class='right'>
                                            <span class='dayofweek'>".$dayofweek."</span>
                                            <span class='year'>". $checky.  "</span>
                                          </div>
                                        </div>";
                                }
                                
                      echo "</div>
                            <div class='card-content image-caption'>
                              <div class='card-maininfo'>
                          ";
                          echo'<span class="title"><a href="event.php?ID='.$eventID.'">';
                          echo $eventTitle . '</a></span>';
                                  
                          if (date('I', strtotime($dateStart))) {
                              $zoneStart = '(CDT)';
                            } else {
                              $zoneStart = '(CST)';
                            }
                            if (empty($dateEnd)) {
                              $zoneEnd = $zoneStart;
                            } else {
                                if (date('I', strtotime($dateEnd))) {
                                    $zoneEnd = '(CDT)';
                                } else {
                                    $zoneEnd = '(CST)';
                                }
                            }
                            //checks to see if start and end time are in the same timezone
                            if ($dateStart == $dateEnd || empty($dateEnd)) {
                                if ($zoneEnd == $zoneStart) {
                                    $zoneStartDisplay = '';
                                    $zoneEndDisplay = $zoneStart;
                                }
                            } else {
                                $zoneStartDisplay = $zoneStart;
                                $zoneEndDisplay = $zoneEnd;
                            }
                                  
                          if(!empty($timeStart)){
                            if (!empty($dateEnd) && ($dateStart != $dateEnd)) {
                              echo "<span><span aria-hidden='true' class='icon time prepend' title='Event Time'></span>".$timeStart . " " . $zoneStartDisplay . " " . date('n/j/Y',strtotime($dateStart)) . "&ndash;";
                            } else {
                              echo "<span><span aria-hidden='true' class='icon time prepend' title='Event Time'></span>
                              <span class='visuallyhidden'>Event Time: </span>". $timeStart . " " . $zoneEndDisplay;
                            }
                            
                            if (!empty($timeEnd) || !empty($dateEnd)) {
                              if(!empty($timeEnd)){
                                if (!empty($dateEnd) && ($dateStart != $dateEnd)) {
                                  echo $timeEnd . " " . $zoneEndDisplay . " ". date('n/j/Y', strtotime($dateEnd));
                                } else {
                                  echo "&ndash;".$timeEnd . " " . $zoneEndDisplay;
                                }
                              } else {
                                echo date('n/j/Y', strtotime($dateEnd));
                              }
                            }
                              echo "</span>";
                          }
                                  
                          echo '</div>';
                          echo "<div class='text-right'>
                                <a class='export-button' style='' title='Save event to iCalendar, Outlook, etc' href='/events/export-event.php?ID=".$eventID."'><span class='icon calendar-plus' aria-hidden='true' title='Save event to iCalendar, Outlook, etc'></span><span class='visuallyhidden'>Save event to iCalendar, Outlook, etc</span></a>
                                </div>";

                          echo '</div>
                          </div>'; //end card content end watchmen
                          echo "<div ><ul class='tag-band' data-equalizer-watch='tags'>";
                          if(!empty($type1)){
                              echo' <li><a href="#'.str_replace(' ', '-', $type1).'"><span class="tag-alt">' . $type1 .'<span class="visuallyhidden"> tag</span></span></a></li> ';
                          }
                          if(!empty($type2)){
                               for ($i = 0; $i < count($tags); ++$i) {
                                  if ($tags[$i] == '#InHerCloset') {
                                    //echo '<li><a href="'. $tags[$i]  .'"><span class="tag-alt">'. $tags[$i] . '<span class="visuallyhidden"> tag</span></span></a></li> ';
                                  } else {
                                     echo '<li><a href="#'. $tags[$i].'"><span class="tag-alt">'. $tags[$i] . '<span class="visuallyhidden"> tag</span></span></a></li> ';
                                  }
                               }
                          }
                          if( (empty($type1)) && (empty($type2)) ){
                                echo' <li><a href="#'. str_replace(' ', '-', $type).'"><span class="tag-alt">' . $type .'<span class="visuallyhidden"> tag</span></span></a></li>';
                          }
                          echo"</ul>";
                          

                           echo'</div></div></li>';
                    } //closed bracket for check date if loop

        }//closed bracket for foreach  loop

        //if there is no event diaplayed in today block, print the message
      echo "</ul></div>";
      if($today_count == 0){
        echo "<div class='callout panel'>There are no events scheduled for today.</div>" ;
      }

      echo '<div class="row">
            <div class="small-12 medium-6 columns">';

          echo "<a class='button expand secondary' href='/exhibits/'>Current Exhibits</a>";

        echo"</div>";

      echo'<div class="small-12 medium-6 columns">';

        echo "<a class='button expand onepointfive' href='#upcoming-event-filter'>Upcoming Events</a>";

        echo '</div></div>';

        echo"</div>
        </div>
        </div>
        </section>

       
        ";

        ?>


                 
<section id="upcoming-events" aria-labelledby="upcoming-events-heading">
<h2 class="visuallyhidden" id="upcoming-events-heading">Upcoming Events</h2>
                 
<div class="filtered-collection full-width-band">

      <div id="upcoming-event-filter" class="filter full-width-band pattern-5">
        <div class="row">
          <div class="small-12 columns text-center">

            <!--filter menu start-->

            <div class="visuallyhidden">Filter by Event Type:</div>

            <ul class="filter-nav" aria-controls="upcoming-events-cards">
              <li class="active"><a href="#all" class="filter">All<span class="visuallyhidden"> Events</span></a></li>
            <li><a href="#Performance" class="filter">Performance<span class="visuallyhidden"> Events</span></a></li>
            <li><a href="#Talk" class="filter">Talk<span class="visuallyhidden"> Events</span></a></li>
            <li><a href="#Film" class="filter">Film<span class="visuallyhidden"> Events</span></a></li>
            <li><a href="#Hands-on" class="filter">Hands-on<span class="visuallyhidden"> Events</span></a></li>

            <li><a href="#Workshop" class="filter">Workshop<span class="visuallyhidden"> Events</span></a></li>
            <li><a href="#Exhibit" class="filter">Exhibits<span class="visuallyhidden"> Events</span></a></li>
            <li><a href="#Special-Event" class="filter">Special Event<span class="visuallyhidden"> Events</span></a></li>
            <li><a href="#Family-Friendly" class="filter">Family-Friendly<span class="visuallyhidden"> Events</span></a></li>
            <li><a href="#Exhibit-Related" class="filter">Exhibit-Related<span class="visuallyhidden"> Events</span></a></li>
            <!--<li><a href="#InHerCloset" class="filter">#InHerCloset<span class="visuallyhidden"> Events</span></a></li>-->
            <li><a href="#Alert" class="filter">ALERT<span class="visuallyhidden"> Events</span></a></li>
            </ul>
            <!--filter menu end-->
          </div>
        </div>
      </div>

      <div id="upcoming-events-cards">
        <div class="row">
          <div class="small-12 medium-7 large-8 columns">
              <h2 id="filtered-cards-heading" tabindex='-1'><span class="showing_all">All Upcoming</span> <span class="header_tag">Events</span></h2>
            </div>

            <div class="small-12 medium-5 large-4 columns">
              <a id="export-button" class="button onepointfive expand tiny" target="_blank" title="Save events to iCalendar, Outlook, etc" href="/events/export-event.php?ID=upcoming">
                <span class="icon calendar-plus prepend" aria-hidden="true"></span><span class="visuallyhidden">Add Event to Calendar</span>Add <span class="showing_all">All Upcoming</span> <span class="header_tag">Events</span> to Calendar
              </a>
            </div>
          </div>

          <div class="row">
          <div class="small-12 columns">
          <div class="hidden panel callout" id="empty_category"><p>There are <strong>no upcoming events in this category</strong>.</p>

          <div id="rental_button"><!--<p>Interested in renting a Museum space for your upcoming event? <a href="/contact/#eventscoordinator">Contact us</a> today!</p>
          <a  class="button onepointfive" href="/visit/facilities/rentals/">Rentals</a>--></div>
          </div>

       

     

                    


   <?php
   $upcoming_count = 0;
  
  echo '<div class="row" data-equalizer-mq="medium-up">
          <div class="small-12 columns" data-equalizer="date-and-caption" data-equalizer-mq="medium-up"> ';
   echo '<ul id="card-list" class="small-block-grid-1 medium-block-grid-2 large-block-grid-3" data-equalizer="tags" data-equalizer-mq="medium-up">';


 

                      foreach($records as $record){
                            $show = false;
                            $maxEventID = $record->getField('max_eventID');
                            $eventTitle = html_entity_decode( $record->getField('Title'));
                            $dateStart = $record->getField('Date_Start');
                            $dateEnd = $record->getField('Date_End');

                            if(!empty($dateEnd)){
                            $endm = substr($dateEnd,0,2);
                            $endy = substr($dateEnd,-4,4);
                            $endd = substr($dateEnd,3,2);
                            }


                            $type = $record->getField('Type');
                           
     
                            $eventID = $record->getField('eventid');
                         

                            global $day, $month, $year;
                            
                      
                            $displayStart = $record->getField('Date_Start_Display');
                            $displayEnd = $record->getField('Date_End_Display');
                            $timeStart = $record->getField('Time_Start');
                            $timeStart2 = $record->getField('Time_Start_New');
                            $timeEnd = $record->getField('Time_End');
                            $cost = $record->getField('Cost');
                            $type1 = $record->getField('Type_Primary');
                            $type2 = $record->getField('Type_Secondary');
                            $timeEnd2 = $record->getField('Time_End_New');

                            $ageRange = $record->getField('Age_Range');
                          
                            $description = html_entity_decode( $record->getField('Event_Description'));

                            
                            $photo = $record->getField('image_preview_path');
                            $photoAlt = $record->getField('main_alt_text');
                            $photo2 = $record->getField('extra_image_path');
                            $photoAlt2 = $record->getField('extra_image_alt_text');
                            
                            
                            $type = $record->getField('Type');
                            $tags = preg_split("/(\r\n|\n|\r)/", $type2);
 
      
                            $checkm = substr($dateStart,0,2);
                            $checky = substr($dateStart,-4,4);
                            $checkd = substr($dateStart,3,2);
                            $diff = (time() -  strtotime($timeStart))/60 ;
                            $a++;

                            $date_format = $checky . '-' . $checkm . '-' . $checkd;
                            
                            $dayofweek = date('D', strtotime($date_format));
                         
                           # Begin upcoming events filtering.
                            # If the event starts in a future month or the current month, and the event starts in a future year or current year...
                            if ( ($checkm >= $month) && ($checky >= $year) ) {
                              # If the event has a start date and a start time...
                              if (strlen($dateStart) != 0 && strlen($timeStart2) != 0) {
                                # If the start date-time is greater than the current time, show the event.
                                if (strtotime($timeStart2." ".$dateStart) > time()) {
                                  $show = true;
                                }
                              # Else (if the event has a start date but and start time)...
                              } else {
                                # If the event starts on a future date, show the event.
                                if (strtotime($dateStart) > date(U)) {
                                  $show=true;
                                }
                              }
                            }
                            # End upoming events filtering.
                              
                            # Display test events within test files only.
                            $test_event = $record->getField('test_flag');
                            if ($test_event == "Test Event") {
                              if (strpos(basename(__FILE__, '.php'), 'test') !== false) {
                                # Do nothing.
                              } else{
                                $show = false;
                              }
                            }

                            if($show ==true){
                              $upcoming_count++;
     
                              echo"<li class=\"filter_option ";
                              # Add all type2 tags (with echoed leading space) and any two-word type1 tags.
                              if ($type1 == "Special Event") {
                                echo"Special-Event";
                              } else{
                                echo $type1;
                              }
                              if (strpos($type2,'Family-Friendly') !== false) {
                                echo" Family-Friendly";
                              }
                              if (strpos($type2, 'Exhibit-Related') !== false) {
                                      echo" Exhibit-Related";
                              }
//                              if (strpos($type2, 'InHerCloset') !== false) {
//                                echo" InHerCloset";
//                              }

                              echo "\"><div class='card'>";

                              # Get month abbreviations for event start date.
                              $month_char=$checkm;
                              switch ($checkm) {
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

                              if($photo){
                              # If the event has a photo in the database, display it.
                                echo '<a href="event.php?ID='.$eventID.'">
                                <img src="/img/' . $photo . '" alt="' . $photoAlt .' '. date('n/j/Y',strtotime($dateStart)).'"></a>';
                              # Else display a placeholder image based on the event's type.
                              }else{
                                echo"<a href='event.php?ID=".$eventID."'>";
                                echo "<div data-equalizer-mq='medium-up' class='crop wide-crop placeholder ".$type1."' >";
                                echo "<div class='icon-image'>";
                                echo "<span class='visuallyhidden'>".$type1."</span>";
                                echo "<span aria-hidden = 'true' class='icon ";
                                if ($type1 == "Special Event") {
                                  echo"event-Special-Event";
                                } else{
                                  echo "event-".$type1;
                                }
                                echo "'><span class=\"visuallyhidden\">"." ". date('n/j/Y',strtotime($dateStart))."</span></span>";
                                echo "</div>";
                                echo "</div>";
                                echo "</a>";
                                    }

                                echo "
                                <div data-equalizer-watch='date-and-caption'>
                                <div class='date-plaque-alt'>";
                                # If the event has both a start date and an end date, display both.
                                if (!empty($dateEnd) && ($dateStart != $dateEnd)) { # add condition to display start and end dates next to times as per sketch
                                  echo  "<div>
                                  <span class='month'>"  . $month_char . "</span><span class='day'>" . ltrim((substr($dateStart,3,2)), '0') .  "</span>
                                  <div class='right'>
                                  <span class='dayofweek'>".$dayofweek."</span>
                                  <span class='year'>". $checky.   "</span>
                                  </div>
                                  </div>
                                  <div>
                                  <span class='month '>" . "<span class='date-separator'>TO </span>". $end_month_char . "</span><span class='day'>" . ltrim((substr($dateEnd,3,2)), '0') . "</span>
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
                                  <span class='year'>". $checky.  "</span>
                                  </div>
                                  </div>";
                                }
                                
                                echo "</div>
                                <div class='card-content image-caption'>
                                <div class='card-maininfo'>
                                ";
                                echo'<span class="title"><a href="event.php?ID='.$eventID.'">';
                                echo $eventTitle . '</a></span>';
                                
                                
                                //new index.php changes here
                                if (date('I', strtotime($dateStart))) {
                                  $zoneStart = '(CDT)';
                                } else {
                                  $zoneStart = '(CST)';
                                }
                                if (empty($dateEnd)) {
                                    $zoneEnd = $zoneStart;
                                } else {
                                    if (date('I', strtotime($dateEnd))) {
                                        $zoneEnd = '(CDT)';
                                    } else {
                                        $zoneEnd = '(CST)';
                                    }
                                }
                                //checks to see if start and end time are in the same timezone
                                if ($dateStart == $dateEnd || empty($dateEnd)) {
                                    if ($zoneEnd == $zoneStart) {
                                        $zoneStartDisplay = '';
                                        $zoneEndDisplay = $zoneStart;
                                    }
                                } else {
                                    $zoneStartDisplay = $zoneStart;
                                    $zoneEndDisplay = $zoneEnd;
                                }
                                
                                //old index.php begins here
                                # If the event has a start time...
                                if(!empty($timeStart)) {
                                  # If the event has an end date that is different from it's start date, display the start time and start date of the event.
                                  if (!empty($dateEnd) && ($dateStart != $dateEnd)) {
                                    echo "<span><span aria-hidden='true' class='icon time prepend' title='Event Time'></span>".$timeStart . " " . $zoneStartDisplay . " " . date('n/j/Y',strtotime($dateStart)) . "&ndash;";
                                  # Else (the event has an end date that is the same as it's start date) display the start time of the event.
                                  } else {
                                    echo "<span><span aria-hidden='true' class='icon time prepend' title='Event Time'></span>
                                    <span class='visuallyhidden'>Event Time: </span>". $timeStart . " " . $zoneEndDisplay;
                                  }
                                  # If the event has an end time or an end date...
                                  if (!empty($timeEnd) || !empty($dateEnd)) {
                                    # If the event has an end time...
                                    if (!empty($timeEnd)) {
                                      # If the event has an end date that is different from its start date...
                                      if (!empty($dateEnd) && ($dateStart != $dateEnd)) {
                                        echo $timeEnd . " " . $zoneEndDisplay. " " . date('n/j/Y', strtotime($dateEnd));
                                      # Else (the event has an end date that is the same as its start date) display the end time of the event
                                      } else {
                                        echo "&ndash;". $timeEnd . " " . $zoneEndDisplay;
                                      }
                                    # Else (the event does not have an end time) display the end date of the event.
                                    } else {
                                      echo date('n/j/Y', strtotime($dateEnd));
                                    }
                                  }

                                  echo "</span>";
                          }
                                
                          echo '</div>';
                          echo "<div class='text-right'>
                                <a class='export-button' style='' title='Save event to iCalendar, Outlook, etc' href='/events/export-event.php?ID=".$eventID."'><span class='icon calendar-plus' aria-hidden='true' title='Save event to iCalendar, Outlook, etc'></span><span class='visuallyhidden'>Save event to iCalendar, Outlook, etc</span></a>
                                </div>";

                          echo '</div>
                          </div>'; //end card content end watchmen

                          echo "<div ><ul class='tag-band' data-equalizer-watch='tags'>";
                          if(!empty($type1)){
                              echo' <li><a href="#'.str_replace(' ', '-', $type1).'"><span class="tag-alt">' . $type1 .'<span class="visuallyhidden"> tag</span></span></a></li> ';
                          }
                          if(!empty($type2)){
                               for ($i = 0; $i < count($tags); ++$i) {
                                  if ($tags[$i] == '#InHerCloset') {
                                    //echo '<li><a href="'. $tags[$i].'"><span class="tag-alt">'. $tags[$i] . '<span class="visuallyhidden"> tag</span></span></a></li> ';
                                  } else {
                                     echo '<li><a href="#'. $tags[$i].'"><span class="tag-alt">'. $tags[$i] . '<span class="visuallyhidden"> tag</span></span></a></li> ';
                                  }
                               }
                          }
                          if( (empty($type1)) && (empty($type2)) ){
                                echo' <li><a href="#'. str_replace(' ', '-', $type).'"><span class="tag-alt">' . $type .'<span class="visuallyhidden"> tag</span></span></a></li>';
                          }
                          echo"</ul>";

                           echo' </div></div></li>';
                    
                    }//closed bracket for check date if loop

        }//closed bracket for foreach  loop

        echo "</ul></div></div>";


        if($upcoming_count == 0){

              echo'<div class="panel callout" id="empty_category"><p>There are <strong>no upcoming events</strong> at this time.</p>

                <div id="rental_button"><!--<p>Interested in renting a Museum space for your upcoming event? <a href="/contact/#eventscoordinator">Contact us</a> today!</p>
                <a  class="button onepointfive" href="/visit/facilities/rentals/">Rentals</a>--></div>
                </div>';
                          
        }

        echo'</div></div></div></div>';
        echo'<div data-interchange="[../includes/_recentAdded.html, (small)]"></div>
              <noscript>
              <div class="row">
                <div class="small-12 columns">
                [Javascript Required] but you can access the {<a href="/includes/_recentAdded.html">RECENTLY ADDED EVENTS</a>} through direct link.<br><br>
                </div>
              </div>
              </noscript>';

       echo '</section>';

        /*
        recently added event blog ends
        */
   }else{
        echo'</div></div></div></div></section><p></p>
        <div class="row"><div class="small-12 medium-10 medium-offset-1 columns"><div class="callout panel text-center">Sorry, the events database is currently under maintenence. Please try again later.</div></div></div></div></section>';

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
                    <!--<div class="small-12 medium-6 columns ">
                      <a class="button expand" href="/visit/facilities/rentals/">Rentals</a>
                    </div>-->

                    <div class="small-12 medium-6 columns small-centered">
                      <a class="button expand" href="/events/past.php">Past Events</a>
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

    <!-- jquery functions starts -->
    <script>


      window.onload = updateH3();

      function updateH3(){
        var href = window.location.href;
        var start_pos = href.indexOf('#') + 1;
        var end_pos = href.indexOf('?',start_pos);
        var hash = end_pos > -1 ? href.substring(start_pos,end_pos) : href.substring(start_pos, href.length);

        var exportURL= '/events/export-event.php?ID=upcoming&type='+hash;
        $('#export-button').attr('href', exportURL);

        if(window.location.hash == "#all" || window.location.hash == "#upcoming-event-filter"){
          $('.showing_all').text('All Upcoming');
        }
        else{
          $('.showing_all').text('Upcoming');
        }

      }
      
    

    // this is a text dictionary that contains key(left) and corresponding texts (right)
    var text = {
       'Exhibit': 'Exhibits',
       'Special-Event':'Special Events',
       'Performance': 'Performances',
       'Talk': 'Talks',
       'Film': 'Films',
       'Hands-on': 'Hands-on Events',
       'Workshop': 'Workshops',
       'Exhibit-Related': 'Exhibit-Related Events',
       'Family-Friendly': 'Family-Friendly Events',
       //'InHerCloset': '#InHerCloset Events',
       'Alert': 'Alerts',
       'all': 'Events'
    };

    var keys = Object.keys(text);

    /*
    function that show six more events on show_more button click
    */
    function show_six(page_num, showing_today){
    var i = page_num;
    if(showing_today >= 1){ // if there are events going on today, add those to the events to count before hiding
        var pages = parseFloat(6 * page_num) + parseFloat(showing_today) - 1; // number of records to display according to the page parameter
    } else {
        var pages = (6 * page_num) - 1; // if there are no events today
    }
        $('.filter_option:gt('+ pages +')').hide().last().after(
          "<div class='small-12 columns'><a id='show-more' class='button expand' href='#' aria-expanded='false'>Show more upcoming events</a></div>");

// http://stackoverflow.com/questions/6148359/determine-focus-event-click-or-tabstop
$('#show-more').mousedown(function(e){
    e.stopPropagation();
    var $this = $(this);

    // Only set the data attribute if it's not already focused, as the
    // focus event wouldn't fire afterwards, leaving the flag set.
    if(!$this.is(':focus')){
        $this.data('mdown',true);
    }
});
         


$( "#show-more" ).click(function(e) {
        e.stopPropagation();
        var $this = $(this);
        var mdown = $this.data('mdown');

        // Remove the flag so we don't have it set next time if the user
        // uses the tab key to come back.
        $this.removeData('mdown');

        i++;
          // if there is no parameter already attached to URL
         if (location.href.indexOf("?") === -1)
         {
          // add ?+parameter after the url
             location.href += "?" + i;}
          // else juest replace the existing parameter with the new one
         else {
          var loc = location.href;
          location.href = loc.split('?')[0] + "?" + i ;
          }

          $('#show-more').attr('aria-expanded', 'true');

        var a = this;
        
          // make keyboard focus on first hidden card
          var first_hidden = $('.filter_option:not(:visible) a').first();
          
        // a fade in effect
        $('.filter_option:not(:visible):lt(6)').fadeIn(function(){
          if(!mdown){
            first_hidden.focus();
          }
        // is no more events is hidden then we can remove the show more button as all the events are showing
         if ($('.filter_option:not(:visible)').length == 0) $(a).parent().remove();
        }); return false;
})

  }

/*$( "a:has(.tag), a.filter" ).keydown(function(e) {
    console.log(this)
      console.log(e.which)
      if(e.which == 13) {
        console.log('hi');
        $('#filtered-cards-heading').focus();
      }
    
  });*/


  $(window).bind('hashchange', function(e) {

      updateH3();
    
      //if there is no parameters in url
      if(window.location.hash.indexOf("?") > -1) {
            var href = window.location.href;
            var start_pos = href.indexOf('#') + 1;
            var end_pos = href.indexOf('?',start_pos);
            var hash = href.substring(start_pos,end_pos);
            var page = href.split("?").pop();
            if (keys.indexOf(hash) >= 0) {
                  evalHash(hash, page);

            }else{
              evalHash('all', page)
            }


       }else{
            //Puts hash in variable, and removes the # character
            var hash = window.location.hash.substring(1);
            if (keys.indexOf(hash) >= 0) {

                  evalHash(hash, 1)
                  

            }else{
              evalHash('all', page)
            }

            
            
        }
        //initialize foundation equalizer again to adjust for any possible change in window width
        $(document).foundation('equalizer', 'reflow');
 
    });


  $(window).bind('load', function() {

      if(!window.location.hash){
     
          $("li.active").removeClass("active");
          $("[href=#all]").parent('li').addClass('active');
          $('#empty_category').addClass('hidden');
          window.location.hash = "all";
          show_six(1);
      }else{
          if(window.location.hash.indexOf("?") > -1) {
              var href = window.location.href;
              var start_pos = href.indexOf('#') + 1;
              var end_pos = href.indexOf('?',start_pos);
              var hash = href.substring(start_pos,end_pos);

              var page = href.split("?").pop();
              if (keys.indexOf(hash) >= 0) {
                evalHash(hash, page)
              }
          }else{
              var hash = window.location.hash.substring(1);
              if (keys.indexOf(hash) >= 0) {
                evalHash(hash, 1)
              }
            
          }
      }

      //initialize foundation equalizer again to adjust for any possible change in window width
        $(document).foundation('equalizer', 'reflow');



  });


  // evaluate the hash and page parameter passed in url when page is loaded or hash changed

  function evalHash(hash_para, page_num){
        var i = page_num;
        $("li.active").removeClass("active");
        $("[href=#" + hash_para +"]").parent('li').addClass('active');
        $('#empty_category').addClass('hidden');

        $("#show-more").parent().remove();
        if (hash_para != 'all'){
            var hashName = "." + hash_para;
            filter(hashName)
            var pages = (6*page_num)-1;
              // alert(hashName);
            $('.filter_option'+hashName + ':gt('+ pages +')').hide().last().after(

             "<div class='small-12 columns'><a id='show-more' class='button expand' href='#' aria-expanded='false'>Show more upcoming events</a></div>"
            );

         // don't scroll to top for "Show More" button
        if(window.location.hash.indexOf("?") == -1){
          $('html, body').animate({
            scrollTop: $("#upcoming-event-filter").offset().top
        }, 800);
        }
        

      // http://stackoverflow.com/questions/6148359/determine-focus-event-click-or-tabstop
      $('#show-more').mousedown(function(e){
          e.stopPropagation();
          var $this = $(this);

          // Only set the data attribute if it's not already focused, as the
          // focus event wouldn't fire afterwards, leaving the flag set.
          if(!$this.is(':focus')){
              $this.data('mdown',true);
          }
      });

    /*$( ".tag" ).click(function(e) {
        e.stopPropagation();
        var $this = $(this);
        var mdown = $this.data('mdown');

        // Remove the flag so we don't have it set next time if the user
        // uses the tab key to come back.
        $this.removeData('mdown');
        console.log(this);
        console.log(mdown);
        if(!mdown){
          $('#filtered-cards-heading').focus();
        }
        
      });*/

    $( "#show-more" ).click(function(e) {
        e.stopPropagation();
        var $this = $(this);
        var mdown = $this.data('mdown');

        // Remove the flag so we don't have it set next time if the user
        // uses the tab key to come back.
        $this.removeData('mdown');

        i++;
          // if there is no parameter already attached to URL
         if (location.href.indexOf("?") === -1)
         {
          // add ?+parameter after the url
             location.href += "?" + i;}
          // else juest replace the existing parameter with the new one
         else {
          var loc = location.href;
          location.href = loc.split('?')[0] + "?" + i ;
          }
          $('#show-more').attr('aria-expanded', 'true');
        var a = this;

          var first_hidden = $('.filter_option'+hashName+':not(:visible) a').first();
        
        
        // a fade in effect
        $('.filter_option'+hashName+':not(:visible):lt(6)').fadeIn(function(){
          if(!mdown){
            first_hidden.focus();
          }
        // is no more events is hidden then we can remove the show more button as all the events are showing
         if ($('.filter_option'+hashName+':not(:visible)').length == 0) $(a).parent().remove();
        }); return false;
    })

        $('.header_tag').text(eval('text["'+hash_para+'"]'));

        }else{

                   $(".filter_option").each(function(){
                      $(this).show();
                  });
                   if (location.href.indexOf("?") === -1)
                   {
                      var page = 1;
                   }else{

                      var href = window.location.href;
                      var page = href.split("?").pop();

                   }

                      var showing_today = "<?php echo $today_count ?>";
                      show_six(page, showing_today);
                      $('.header_tag').text('Events');
      }

      //initialize foundation equalizer again to adjust for any possible change in window width
        $(document).foundation('equalizer', 'reflow');
}

  function filter(tag) {
    
          $(".filter_option").each(function(){
          $(this).attr("aria-hidden","false");
          $(this).show();
          $('#empty_category').addClass('hidden').attr("aria-hidden","true");


          if (!$(this).is(tag) ){
              $(this).hide();
              $(this).attr("aria-hidden","true");
          }

          if ($('li.filter_option:visible').length === 0) {
              $('#empty_category, #rental_button').removeClass('hidden').attr("aria-hidden","false");
              $('.export-button').addClass('hidden').attr("aria-hidden", "true");
              
          }
          else{
            $('.export-button').removeClass('hidden').attr("aria-hidden", "false");
          }

     
        });
         
        if(($('.filter_option.Alert:visible').length === 0) && (tag==".Alert")){
           
                $('#rental_button').addClass('hidden').attr("aria-hidden","true");
              }
     
      

  }

</script>

<script>
  //doesn't work on non-safari browsers on iOS, so just hide export button for those browsers (Chrome, Firefox, Opera)
  if(navigator.userAgent.match(/criOS/i) != null || navigator.userAgent.match(/FxiOS/i) != null || navigator.userAgent.match(/OPiOS/i) != null){
    $(".export-button").remove();
  }
</script>

</body>
</html>



