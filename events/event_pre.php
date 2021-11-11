<?php 
require_once(dirname(__FILE__).'/../db-support/fm/FileMaker.php'); //packaged with filemaker.  Not modified.
require_once(dirname(__FILE__).'/../db-support/check_db_pre_data_api.php');
require_once(dirname(__FILE__).'/../db-support/clean.php');
//define database address
$invalid_id = false;


//if year is set
if (isset($_GET['ID'])) {
     $ID = clean($_GET['ID']);
     //if the event id contains letters thenset boolean to true
     if (preg_match( '/[a-zA-Z]/', $ID) ) { 
            $invalid_id = true;
         }
}

if (isset($_GET['preview'])) {
  $preview = clean($_GET['preview']);
}

 if(check_db("web_events") == 1){

              $fm1 = new FileMaker('Web_Events', $database_server_ip, NULL, NULL);

              $request = $fm1->newFindRequest('web_events');
              $request->addFindCriterion('eventid', clean($_GET['ID']));

              // [07.01.16] AYX: for whatever reason, it needs compound find to only return 1 result
              $compoundFind = $fm1->newCompoundFindCommand('web_events');
              $compoundFind->add(1, $request);
              $result = $compoundFind->execute();

              //Get the Next/Previous button data
              $phpSupportRequest = $fm1->newFindRequest('PHP Support');
              $phpSupportRequest->addFindCriterion('eventid', $ID);

              $compoundFind2 = $fm1->newCompoundFindCommand('PHP Support');
              $compoundFind2->add(1, $phpSupportRequest);

              $compoundFind2->setRelatedSetsFilters('none', '10');

              $phpSupportResult = $compoundFind2->execute();

              if (!(FileMaker::isError($result))) { 
                $records = $result->getRecords();
                $phpSupportRecords = $phpSupportResult->getRecords();
              

                //get info for previous/next buttons
              foreach ($phpSupportRecords as $record){
                 // access portal to get next and previous post ids

                $prev_portal= $record->getRelatedSet('Web_Events_older');
                $next_portal= $record->getRelatedSet('Web_Events_newer');

                $eventStartTime = strtotime($record->getField('Time_Start'));
                if($eventStartTime == ""){
                  $eventStartTime = strtotime('12:00 am');
                }
                $eventID = $record->getField('eventid');

                if(!(FileMaker::isError($prev_portal))){
                  foreach ($prev_portal as $row){
                    if($row->getField('Web_Events_older::Date_Start') == $record->getField('Date_Start')){
                      $portalRowStartTime = strtotime($row->getField('Web_Events_older::Time_Start'));

                      if($portalRowStartTime == ""){
                        $portalRowStartTime = strtotime('12:00 am');
                      }
                        

                      if($portalRowStartTime < $eventStartTime){
                        $prevID = $row->getField('Web_Events_older::eventid');
                        $prevTitle = strip_tags(html_entity_decode($row->getField('Web_Events_older::Title')));
                        break;
                      }
                      if(($portalRowStartTime == $eventStartTime) && ($row->getField('Web_Events_older::eventid') < $eventID)){
                          $prevID = $row->getField('Web_Events_older::eventid');
                          $prevTitle = strip_tags(html_entity_decode($row->getField('Web_Events_older::Title')));
                          break;
                      }
                      else{
                        continue;
                      }

                    }
                    $prevID = $row->getField('Web_Events_older::eventid');
                    $prevTitle = strip_tags(html_entity_decode($row->getField('Web_Events_older::Title')));
                    break;
                  }
                }


               if(!(FileMaker::isError($next_portal))){
                   foreach($next_portal as $row){
                    if($row->getField('Web_Events_newer::Date_Start') == $record->getField('Date_Start')){
                      $portalRowStartTime = strtotime($row->getField('Web_Events_newer::Time_Start'));

                      if($portalRowStartTime == ""){
                        $portalRowStartTime = strtotime('12:00 am');
                      }

                      if($portalRowStartTime > $eventStartTime){
                        $nextID = $row->getField('Web_Events_newer::eventid');
                        $nextTitle = strip_tags(html_entity_decode($row->getField('Web_Events_newer::Title')));
                        break;
                      }
                      if(($portalRowStartTime == $eventStartTime) && ($row->getField('Web_Events_newer::eventid') > $eventID)){
                          $nextID = $row->getField('Web_Events_newer::eventid');
                          $nextTitle = strip_tags(html_entity_decode($row->getField('Web_Events_newer::Title')));
                          break;
                      }
                      else {
                        continue;
                      }

                    }
                    $nextID = $row->getField('Web_Events_newer::eventid');
                    $nextTitle = strip_tags(html_entity_decode($row->getField('Web_Events_newer::Title')));
                    break;
                    
                  }
                }

              }
               
              foreach ($records as $record){
                  $eventTitle = html_entity_decode( $record->getField('Title') );  

                  $eventID = $record->getField('eventid_text');

                  $dateStart = $record->getField('Date_Start');
                  $dateEnd = $record->getField('Date_End');

                  #[2/11/2019 DM] For date formatting for date & time display below hero image
                  $checkmstart = substr($dateStart,0,2);
                  $checkystart = substr($dateStart,-4,4);
                  $checkdstart = substr($dateStart,3,2);
                  $checkmend = substr($dateEnd,0,2);
                  $checkyend = substr($dateEnd,-4,4);
                  $checkdend = substr($dateEnd,3,2);
                  $date_formatstart = $checkystart . '-' . $checkmstart . '-' . $checkdstart;
                  $date_formatend = $checkyend . '-' . $checkmend . '-' . $checkdend;
                  $checkdayofweekstart = substr($dateStart,3,2);
                  $dayofweekstart = date('l', strtotime($date_formatstart));;
                  $checkdayofweekend = substr($dateEnd,3,2);
                  $dayofweekend = date('l', strtotime($date_formatend));

                  $displayStart = $record->getField('Date_Start_Display');
                  $displayEnd = $record->getField('Date_End_Display');
                  $timeStart = $record->getField('Time_Start');
                  $timeEnd = $record->getField('Time_End');
                  $cost = html_entity_decode($record->getField('Cost'));
                  
                  //added by Jack 12/17/09
                  $ageRange = html_entity_decode($record->getField('Age_Range'));
                  
                  $location = html_entity_decode($record->getField('Location'));
                  $location_external = $record->getField('Location_External');
                  $contactName = html_entity_decode($record->getField('Contact_Name'));
                  $contactPhone = $record->getField('Contact_phone');
                  $contactEmail = $record->getField('Contact_email');
                  $description = html_entity_decode( $record->getField('Event_Description'));
                  $eventid = $record->getField('eventid');
                  $photo = $record->getField('media_hero_path');
                  $photoAlt = $record->getField('main_alt_text');
                  $photo2 = $record->getField('extra_image_path');
                  $photoAlt2 = $record->getField('extra_image_alt_text');
                  $infolink1 = $record->getField('Info_Link1');
                  $namelink1 = html_entity_decode($record->getField('Info_Link1_Name'));
                  $externallink1 = $record->getField('External_Link1');
                  $infolink2 = $record->getField('Info_Link2');
                  $namelink2 = html_entity_decode($record->getField('Info_Link2_Name'));
                  $externallink2 = $record->getField('External_Link2');
                  $infolink3 = $record->getField('Info_Link3');
                  $namelink3 = html_entity_decode($record->getField('Info_Link3_Name'));
                  $externallink3 = $record->getField('External_Link3');
                  
                  $type = $record->getField('Type');
                  $type1 = $record->getField('Type_Primary');
                  $type2 = $record->getField('Type_Secondary');
                  $tags = preg_split("/(\r\n|\n|\r)/", $type2);

                  $published = $record->getField('Published');
                 

                  $contactCustomBlock = $record->getField('Contact_CustomBlock');                
                }
            }    
  }

?>

<!DOCTYPE html>
<html class="no-js" lang="en">
    <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php

  
     if(check_db("Website CMS") == 1 && $invalid_id == false){

         echo' <title>'. strip_tags( $eventTitle). ', Events, Spurlock Museum, U of I</title>';
     }else{
        echo' <title>Event Title, Events, Spurlock Museum, U of I</title>';
     }

      ?>

    <link rel="stylesheet" href="/css/app.css" />
    <link rel="stylesheet" type="text/css" href="/css/FlatIcons/flaticon.css"> 
    
    
  </head>
  
  <body>
    
        <!-- Offside Menu -->
        <!--entire site nav-->
        <aside aria-label="off-canvas site menu" >
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

           
            
  <main>

   <!--Skip to Main Content Target-->
<a id="maincontent" tabindex="-1"></a>

<div class="row">
<div class="small-12 columns">

  
 
<?php         
if($invalid_id == true){
    echo'<p></p><div class="callout panel text-center">You entered an invalid record id. Please try again.</div></div></div>';

          }else{

              if(check_db("web_events") == 1 && (($published == "Yes") || ($preview == 1))){

              
              if (FileMaker::isError($result)) { 
                    if( $result->getCode()=='401'){
                      echo'<p></p><div class="callout panel text-center"><p>There are no records matching the record ID. </p></div>';}
                    else{
                    echo'<p></p><div class="callout panel text-center">Sorry, the events database is currently under maintenence. Please try again later.</div>';     
                    }

                    echo '</div></div>';

                }

 
                foreach ($records as $record){
                

      
      echo"<div class='row'><div class='small-12 medium-10 large-8 medium-offset-1 large-offset-2 columns'>";
              
                  if(empty($photo) && empty($photo2)) {

                      echo "<div class='crop wide-crop placeholder ".$type1."'>";
                      echo "<div class='icon-image'>";
                      echo "<span class='visuallyhidden'>".$type1."</span>";
                      echo "<span aria-hidden = 'true' class='icon ";
                      if ($type1 == "Special Event"){
                          echo"event-Special-Event";
                      }
                      else{
                          echo "event-".$type1;
                      }
                      echo "'></span>";
                      echo "</div>";
                      echo "</div>";
                      }

                  if(!empty($photo)){
                      echo '<img src="/img/' . $photo . '" alt="' . $photoAlt . '">';
                  }

                  // [1/21/2019 DGM] moved breadcrumbs below image, as in blogposts   
                    echo'   <div class="row">
                      <div class="small-12 columns">
                        <ol class="breadcrumbs" aria-label="breadcrumb navigation">
                          <li><a href="/">Home</a></li>
                          <li><a href="/events/">Events</a></li>
                          <li class="current">'.$eventTitle.'</li>
                        </ol>
                      </div>
                    </div>';
                      
                       echo"</div></div>";

             
                       echo"
                          <div class='row'>
                          <div class='small-12 medium-10 large-8 medium-offset-1 large-offset-2 columns'>
                              <h1>" .$eventTitle. "</h1>";

                      if($published != "Yes"){
                        echo '
                            <section id="not-published" role="alert" aria-labelledby="not-published-heading"><h2 class="visuallyhidden" id="not-published-heading">Pre-Publication Draft</h2>

                              <div class="alert-box alert">This is a pre-publication draft. Content has not been fully approved for publication and may change.</div>

                            </section>';
                      }

                       // [02/06/2019 DGM] moved tags above icons, as in blogposts 
                      echo "<ul class='tag-list'>";

                      // [06.02.16] AYX: replaced spaces with dashes so link/anchor works
                      echo'<li><a href="/events/#'.str_replace(' ', '-', $type1).'"><span class="tag-alt">'. $type1 .'</span></a></li> ';

                      if(!empty($type2)){
                          for ($i = 0; $i < count($tags); ++$i) {
                            if ($tags[$i] == '#InHerCloset') {
                              echo '<li><a href="/events/'.str_replace(' ', '-', $tags[$i]).'"><span class="tag-alt">'.$tags[$i].'<span class="visuallyhidden">tag</span></span></a></li> ';
                            } else {
                            // [06.02.16] AYX: replaced spaces with dashes so link/anchor works
                              echo '<li><a href="/events/#'.str_replace(' ', '-', $tags[$i]).'"><span class="tag-alt">'. $tags[$i] . '<span class="visuallyhidden"> tag</span></span></a></li> ';
                            }
                          }
                      }
                      

                      echo "</ul>";

                      echo'<ul class="icon-bullet">';
                         
                      echo' <li class="date" title="Date"><span class="visuallyhidden">Event Date: </span><strong>' .date('l, F j, Y',strtotime($dateStart)). '</strong>';

                      if(!empty($dateEnd)){
                          echo "<strong>&ndash;" .date('l, F j, Y',strtotime($dateEnd)). "</strong>";
                      }else{
                          echo"</li>";
                      }

                      if(!empty($timeStart)) { 
                        # [02/13/2019 DM] date & time formatting  
                        echo'<li class="time" title="Time"><span class="visuallyhidden">Time: </span>';
                       
                        if(empty($timeEnd)) {
                                  echo  $timeStart . "</li>";
                        } else if ($checkdayofweekstart != $checkdayofweekend & $checkdayofweekend != 0) {          
                                  echo $timeStart . " " . $dayofweekstart ."&ndash;". $timeEnd . " " . $dayofweekend. "</li>";
                        } else {
                                  echo $timeStart ."&ndash;". $timeEnd . "</li>";
                        }
                      }

                      if(!empty($location)){
                                echo '<li class="location" title="Location"><span class="visuallyhidden">Location: </span><a class="external" href="http://maps.google.com/?q='.urlencode($location).'">' . $location . '</a></li>';
                      }

                      
                      //Added by Jack 12/17/09
                      if(!empty($ageRange)) {
                                echo '<li class="age" title="Age"><span class="visuallyhidden">Age: </span> ' . $ageRange . '</li>';
                      }

                      if(!empty($cost)) {
                                echo '<li class="cost" title="Cost"><span class="visuallyhidden">Cost: </span>' . $cost . '</li>';
                      }
                                               
                               
                      echo'</ul><p></p>'; 

                      echo "<p>";

                      echo $description;

                      echo"</p>";

                     
                      if(!empty($photo2)) {
                          // [10.13.10] MTR: Took out <br> @ beginning of next line.
                          echo "<img style='display:block' src='/img/" . $photo2 . "' alt='/img/" . $photoAlt2 . "'><br>";
                      }
                      
                      if($location_external == "External Location"){
                        echo '<div class="alert-box alert">Please note that this program does NOT take place at the Spurlock Museum.</div>';
                      }

                     echo '
                      <a class="button onepointfive tiny export-button" title="Save event to iCalendar, Outlook, etc" href="/events/export-event.php?ID='.$ID.'"><span class="icon calendar-plus prepend" aria-hidden="true" title="Add Event to Calendar"></span><span class="visuallyhidden">Add Event to Calendar</span>Add to Calendar</a>
                      ';
                        
                                
                      /*echo'
                            
                          
                        </div>
                      </div>';*/

                                

                    echo '</div></div></div></div>';
                    if(!empty($infolink1)){
                                  echo '
                                      <section id="related-text" class="full-width-band" aria-labelledby="related-text-heading">
                                          <div class="row">
                                            <div class="small-12 medium-10 large-8 medium-offset-1 large-offset-2 columns">
                                              <div class="panel">
                                                <h2 class="text-center subheader" id="related-text-heading">Related Links</h2> 
                                                    <div class="text-center">
                                                      <a ';

                                  if ($externallink1 != ""){
                                    echo "class='external' "; // THN 10-19-2010
                                    echo "href='" . $infolink1 . "'>" . $namelink1 . "<span> (external link)</span></a>";
                                  } 
                                  else{
                                    echo "href='" . $infolink1 . "'>" . $namelink1 . "</a>";
                                  }
                                
                                  if(!empty($infolink2)){
                                    echo " | <a ";
                                    if ($externallink2 != ""){
                                      echo "class='external' "; // THN 10-19-2010
                                      echo "href='" . $infolink2 . "'>" . $namelink2 . "<span> (external link)</span></a>";
                                    }
                                    else{
                                      echo "href='" . $infolink2 . "'>" . $namelink2 . "</a>";
                                    }
                              
                                    if(!empty($infolink3)) {
                                      echo " | <a ";
                                      if ($externallink3 != ""){
                                        echo "class='external' "; // THN 10-19-2010
                                        echo "href='" . $infolink3 . "'>" . $namelink3 . "<span> (external link)</span></a>";
                                      }
                                      else{
                                        echo "href='" . $infolink3 . "'>" . $namelink3 . "</a>";
                                      }
                                    }
                                  }
                                  echo'       
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </section>';    
                                }

                                if(!empty($contactName)) {
                                  echo '
                                  
                                    <section id="contact" class="full-width-band" aria-labelledby="contact-heading">
                                          <div class="row">  
                                            <div class="small-12 medium-10 large-8 medium-offset-1 large-offset-2 columns">
                                              <h2 id="contact-heading">Contact</h2>';
                                  if(empty($contactEmail)) {
                                    echo "<p>For further information on this event, contact <strong>" . $contactName . " </strong>at " . $contactPhone . ".</p>";
                                  }
                                  elseif(empty($contactPhone)) {
                                    echo "<p>For further information on this event, contact <strong>" . $contactName . " </strong>at <a class='email' href='mailto:" . $contactEmail . "'>" . $contactEmail . "<span> (email link)</span></a></p>";

                                  }
                                  else{
                                  echo "<p>For further information on this event, contact <strong>" . $contactName . " </strong>at " . $contactPhone . " or <a class='email' href='mailto:" . $contactEmail . "'>" . $contactEmail . "<span> (email link)</span></a></p>";
                                  }

                                  if(!empty($contactCustomBlock)){
                                    echo '<br><br>';
                                    echo html_entity_decode($contactCustomBlock);
                                  }

                                  echo'   <p>To request disability-related accommodations for this event, please contact <a href="/contact/#brian">Brian Cudiamat</a> at (217) 244-5586 or <a href="mailto:cudiamat@illinois.edu">cudiamat@illinois.edu (email link)</a>.</p>
                                        </div>
                                      </div>
                                    </section>';  
                              }

                              elseif(!empty($contactCustomBlock)){
                               echo '
                                  
                                    <section id="contact" class="full-width-band" aria-labelledby="contact-heading">
                                      <div class="row">  
                                        <div class="small-12 medium-10 large-8 medium-offset-1 large-offset-2 columns">
                                          <h2 id="contact-heading">Contact</h2>';

                                          echo html_entity_decode($contactCustomBlock);

                                echo' 
                                        <p>To request disability-related accommodations for this event, please contact <a href="/contact/#brian">Brian Cudiamat</a> at (217) 244-5586 or <a href="mailto:cudiamat@illinois.edu">cudiamat@illinois.edu (email link)</a>.</p>      
                                        </div>
                                      </div>
                                    </section>'; 


                                }


                               
        }
                              
        
}else{
   echo'   <div class="row">
                     <div class="small-12 columns">
                      <ol class="breadcrumbs" aria-label="breadcrumb navigation">
                       <li><a href="/">Home</a></li>
                        <li><a href="/events/">Events</a></li>
                    <li class="current">Event Page</li>
              
                      </ol>
                    </div>
              </div>';

    if ($published != "Yes" && $preview !=1){
      echo'<div class="callout panel text-center">Sorry, this event has not been published yet. Please try again later.</div>';
    }
    else{
      echo '<div class="callout panel text-center">Sorry, the events database is currently under maintenence. Please try again later.</div>';
    }
  echo '</div></div>';
}
}
 ?> 

 </main>

<!--Related Events start-->
  <?php
                            #get ids of related events
                            foreach ($records as $related_record){
                              $related_IDs = $related_record->getField('related_IDs');
                              $indiv_ids = explode(",", $related_IDs);
                              # If related IDs are found, build related events section.
                              if ($related_IDs != '') {
                                
                                # Filter IDs for incorrect entries.
                                $find_rec_IDs = array();
                                foreach ($indiv_ids as $id) {
                                  if (is_numeric($id) && $id != $ID) {
                                    # Filter FileMaker operators.
                                    if (strpos($id, '*' && "$" && '=' && '==' && '!' && '<' && '<=' && '>' && '=>' && '...' && '//' && '?' && '@' && '#' && '"\"' && '""' && '*""' && '~') == false) {
                                      $id = trim($id);
                                    array_push($find_rec_IDs, $id);
                                    }
                                  }
                                }
                                
                                # Initiate related records compound find.
                                $id_compoundFind = $fm1->newCompoundFindCommand('Web_Events'); 
                                # Create a find request for each related event and add each one to the compound find.
                                $find_num = 1;
                                foreach ($find_rec_IDs as $id) {
                                  $find_rec = $fm1->newFindRequest('Web_Events');
                                  $find_rec->addFindCriterion('eventid', $id);
                                  $find_rec->addFindCriterion('Published', 'Yes');
                                  $id_compoundFind->add($find_num, $find_rec);
                                  $find_num = $find_num + 1;
                                }
                                # Sort records by date.
                                $id_compoundFind->addSortRule('Date_Start', 1, FILEMAKER_SORT_ASCEND);
                                $id_result = $id_compoundFind->execute();

                                if (!FileMaker::isError($id_result)) {
                                  
                                $related_records = $id_result->getRecords();
                                $resultCount = $id_result->getFoundSetCount();
                                foreach ($related_records as $related_record_found) {
                                  $eventID = $related_record_found->getField('eventid');
                                  $test_event = $related_record_found->getField('test_flag');
                                  if ($test_event == "Test Event" && strpos(basename(__FILE__, '.php'), 'test') == false) {
                                    $resultCount = $resultCount - 1;
                                  }
                                }
                                if ($resultCount != 0) {
                                  if (sizeof($related_records) > 1) {
                                    echo '<section id="related-events" class="full-width-band" aria-labelledby="related-events-heading"> 
                                      <div class="row">
                                        <div class="small-12 medium-10 large-8 medium-offset-1 large-offset-2 columns">
                                          <h2 id="related-events-heading" class="text-center">Related Events</h2>
                                        </div>
                                      </div>
                                      <div class="row">
                                      <div class="small-12 columns" data-equalizer="rel-card" data-equalizer-mq="medium-up">
                                        <ul class="small-block-grid-1 medium-block-grid-2">';
                                  } else {
                                    echo '<section id="related-events" class="full-width-band" aria-labelledby="related-events-heading"> 
                                      <div class="row">
                                        <div class="small-12 medium-10 large-8 medium-offset-1 large-offset-2 columns">
                                          <h2 id="related-events-heading" class="text-center">Related Event</h2>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="small-12 medium-6 medium-centered column" data-equalizer data-equalizer-mq="medium-up">
                                          <ul class="small-block-grid-1">';
                                  }
                                    # Pull data from each related event.
                                    foreach ($related_records as $record) {
                                      $show = true;
                                      $maxEventID = $record->getField('max_eventID');
                                      $eventTitle = html_entity_decode( $record->getField('Title'));
                                      $dateStart = $record->getField('Date_Start');
                                      $dateEnd = $record->getField('Date_End');
                                      $type = $record->getField('Type');
                           
     
                            $eventID = $record->getField('eventid');
                         

                            global $today_day, $today_month, $today_year;
                            
                            $dateStart = $record->getField('Date_Start');
                            $dateEnd = $record->getField('Date_End');
                            $displayStart = $record->getField('Date_Start_Display');
                            $displayEnd = $record->getField('Date_End_Display');
                            $timeStart2 = $record->getField('Time_Start_New');
                            $timeEnd = $record->getField('Time_End');
                            $timeEnd2 = $record->getField('Time_End_New');
                            
                            if(!empty($dateEnd)) {
                              $endm = substr($dateEnd,0,2);
                              $endy = substr($dateEnd,-4,4);
                              $endd = substr($dateEnd,3,2);
                            }

                            $datespan = $record->getField('Date_Span');
                            
                            $type = $record->getField('Type');
                            $tags = preg_split("/(\r\n|\n|\r)/", $type2);
      
                            $checkm = substr($dateStart,0,2);
                            $checky = substr($dateStart,-4,4);

                            //calculate the diff btw event start time and current time to determine if it is an upcoming event 
                            $diff = (time() -  strtotime($timeStart2))/60 ;
                            $checkd = substr($dateStart,3,2);

                            //change the data format to be yy-mm-dd
                            $date_format = $checky . '-' . $checkm . '-' . $checkd;
                            $end_date_format = $endy . '-' . $endm . '-' . $endd;
                            $dayofweek = date('D', strtotime($date_format));
                            $end_day_of_week = date('D', strtotime($end_date_format));

                            $test_event = $record->getField('test_flag');

                                       if ($test_event == "Test Event" && strpos(basename(__FILE__, '.php'), 'test') == false) {
                                        $show = false;
                                       }

                                      if($show ==true) {    
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

                          // display event date plate

                          echo "
                           <li><div class='card margin-fix' data-equalizer-watch='rel-card'>";

                           echo "
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
                          echo $eventTitle . '</a></span>

                                                </div>
                                              </li>';
                                      }  
                                    } 
                                  }
                                }
                                
                                
                              }     
                            }       
                          echo '</ul>
                              </div>
                            </div> 
                          </section>';
                                 
  ?>                       
  <!--Related Events end-->


<!--adjacent events start-->
<aside id="related-sequence" class="full-width-band lightbrown" <?php if($published != "Yes" || strtotime($date_publish) > time()) echo 'style="display:none" ';?> >
  <div class="row">
    <div class="small-12 columns">
    <h2 class="visuallyhidden">More Events</h2>
    </div>
  </div>
  <div class="row">
    <div class="medium-6 large-5 columns ">

      <a class="button expand secondary"  <?php if($prevID == "") echo 'style="display:none" ';?> href="/events/event.php?ID=<?php echo $prevID; ?>"><span class="visuallyhidden">Previous Event:</span><span class="icon previous" aria-hidden="true" title="Previous Post"></span>  <?php echo $prevTitle;?></a>

    </div>
    <div class="medium-6 large-5 columns">

      <a class="button expand secondary" <?php if($nextID == "") echo 'style="display:none" ';?> href="/events/event.php?ID=<?php echo $nextID; ?>"><span class="visuallyhidden">Next Event:</span><?php echo 
        $nextTitle;?> <span class="icon next" aria-hidden="true" title="Next Post"></span></a>

    </div>
  </div>
</aside>
      
<!--recommended pages begin-->
          <aside aria-label="recommended pages" id="related-recommended-pages" class="full-width-band">
            <div class="row">
              <div class="small-12 columns">
                <div class="panel">
                  <h2 class="text-center subheader">Recommended Pages</h2>
                  <div class="row">  
                    <div class="small-12 medium-6 medium-centered large-centered columns">
                      <a class="button expand" href="/events/">Current &amp; Upcoming Events</a>
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
  if(navigator.userAgent.match(/criOS/i) != null || navigator.userAgent.match(/FxiOS/i) != null || navigator.userAgent.match(/OPiOS/i) != null){
    $(".export-button").remove();
  }
</script>

<script>
  $(document).foundation({
  equalizer : {
    // Specify if Equalizer should make elements equal height once they become stacked.
    equalize_on_stack: true,

  }
});

  $(document).foundation('equalizer', 'reflow');
  

</script>

</body>
</html>

