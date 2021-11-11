<?php
require_once(dirname(__FILE__).'/../db-support/check_db.php');
require_once(dirname(__FILE__).'/../db-support/clean.php');
require_once(dirname(__FILE__).'/../db-support/fmREST/fmREST.php');
$invalid_id = false;

// if year is set
if (isset($_GET['ID'])) {
  $ID = clean($_GET['ID']);
  // if the event id contains letters thenset boolean to true
  if (preg_match( '/[a-zA-Z]/', $ID) ) { 
    $invalid_id = true;
  }
}

if (isset($_GET['preview'])) {
  $preview = clean($_GET['preview']);
}

if(check_db("web_events") == 1) {

  $fm = new fmREST($database_server_name, 'Web_Events', "", "", 'Web_Events');

  // find utility article
  $utilityRequest1['PrimaryKey'] = "7ACC916F-7466-BC41-B551-4120C2531DCA";    
  $utilityFindRequests = [
    $utilityRequest1
  ];
  $utilityFindData['query'] = $utilityFindRequests;
  $utilityRecordFind = $fm -> findRecords($utilityFindData, "utility");
  $utilityRecord = $utilityRecordFind['response']['data'][0];

  if($preview == 1) { // list of all id numbers to sort
    $publish_dates = $utilityRecord['fieldData']['c_list_Date_Start_all_prepped'];
    $id_numbers = $utilityRecord['fieldData']['c_list_eventid_all_prepped'];
  } else { // list of published id numbers to sort
    $publish_dates = $utilityRecord['fieldData']['c_list_Date_Start_published_prepped'];
    $id_numbers = $utilityRecord['fieldData']['c_list_eventid_published_prepped'];
  }

  // merge into 2D array - merged_array[i][j] where i is each article, [i][0] = publish_date, [i][1] = id_number, and [i][2] = tags
  $publish_date_array = explode("\r", $publish_dates); 
  $id_number_array = explode("\r", $id_numbers);
  $merged_array = array_map(null, $publish_date_array, $id_number_array);
  
  // sort the merged_array based on date (descending order (most recent dates appear first))
  function date_sort($a, $b) {
    return strtotime($b[0]) - strtotime($a[0]);
  }
  usort($merged_array, "date_sort");

 
  // JRK: Field names (delete later)
  /*c_list_Date_Start_published_prepped;
  c_list_eventid_published_prepped;
  c_list_eventid_unpublished_prepped;
  c_list_Date_Start_unpublished_prepped;
  c_list_Date_Start_all_prepped;
  c_list_eventid_all_prepped;*/

  // find next and prev events
  for($i = 0; $i < count($merged_array); $i++){
    if($merged_array[$i][1] == $ID){ // once we find the current event
      if($i == 0){ // newest event
        $prevID = $merged_array[$i+1][1];
        $nextID = ""; 
      } else if($i == count($merged_array) - 1) { // oldest event
        $prevID = "";
        $nextID = $merged_array[$i-1][1];
      } else {
        $prevID = $merged_array[$i+1][1];
        $nextID = $merged_array[$i-1][1];
      }
    }
  }

  $request1['eventid'] = clean($_GET['ID']);
  $request2['eventid'] = $prevID;
  $request3['eventid'] = $nextID;

  // do a search on just the current record to check if it exists or not, before serachign for next and prev records.
  $singleFindReq  = [
  	$request1
  ];

  $singleFindData['query'] = $singleFindReq;
  $single_result = $fm -> findRecords($singleFindData);
  $single_err_code = $single_result['messages'][0]['code'];

  $findRequests = [
    $request1,
    $request2,
    $request3
  ];

  $rule1['fieldName'] = 'Date_Start';
  $rule1['sortOrder'] = 'descend';
  // combine sort rules into 2d array
  $sort = [
    $rule1
  ];

  $findData['query'] = $findRequests;
  $findData['sort'] = $sort;
  $result = $fm -> findRecords($findData);
  $err_code = $result['messages'][0]['code'];
  if(!$err_code && !$single_err_code){
  	$records = $result['response']['data'];
  	if(sizeof($records) == 2) {
		  $record = $result['response']['data'][0];
		  $prevTitle = $result['response']['data'][1]['fieldData']['Title'];
		} else {
			$record = $result['response']['data'][1];
		  $prevTitle = $result['response']['data'][2]['fieldData']['Title'];
		  $nextTitle = $result['response']['data'][0]['fieldData']['Title']; 
		}
	  // find related events
	  $related_record_ids = $record['fieldData']['related_IDs'];
	  $related_id_array = explode(",", $related_record_ids); 

	  for($i = 0; $i < count($related_id_array); $i++){ // for every related record id
	    if($related_id_array[$i] === $ID){ // if the record is the ID
	    	array_splice($related_id_array, $i, 1); // delete from array
	      $i--; // array_splice reindexes the elements, so we need to decrement to see all elements
			}
	  } 

	  $related_requests = []; 
	  for($i = 0; $i < count($related_id_array); $i++){
	  	$related_requests[$i] = [];
	  	$related_requests[$i]['eventid'] = $related_id_array[$i]; 
		}
		
		$related_rule['fieldName'] = 'Date_Start';
		$related_rule['sortOrder'] = 'ascend';

		$related_sort = [
			$related_rule
		];

		$related_find_data['query'] = $related_requests;
		$related_find_data['sort'] = $related_sort;
		$related_result = $fm -> findRecords($related_find_data);
		$related_records = $related_result['response']['data'];
		
		//get some listed count 
	  $num_event_array = explode(" ", $related_records[0]['fieldData']['ListedCount']);
	  $resultCount = $num_event_array[3];

		if(count($related_id_array) == 0 ){ // no related ids
			$resultCount = 0;
		}

		foreach ($related_records as $related_record) {
			$test_event = $related_record['fieldData']['test_flag'];
			if ($test_event == "Test Event" && strpos(basename(__FILE__, '.php'), 'test') == false) {
			  $resultCount = $resultCount - 1;
	  	}
	  }

	  $eventTitle = html_entity_decode($record['fieldData']['Title']);  
	  $eventID = $record['fieldData']['eventid_text'];

	  $dateStart = $record['fieldData']['Date_Start'];
	  $dateEnd = $record['fieldData']['Date_End'];

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

	  $displayStart = $record['fieldData']['Date_Start_Display'];
	  $displayEnd = $record['fieldData']['Date_End_Display'];
	  $timeStart = $record['fieldData']['Time_Start'];
	  $timeEnd = $record['fieldData']['Time_End'];
	  $cost = html_entity_decode($record['fieldData']['Cost']);
	  
	  //added by Jack 12/17/09
	  $ageRange = html_entity_decode($record['fieldData']['Age_Range']);
	  $location = html_entity_decode($record['fieldData']['Location']);
	  $location_external = $record['fieldData']['Location_External'];
	  $contactName = html_entity_decode($record['fieldData']['Contact_Name']);
	  $contactPhone = $record['fieldData']['Contact_phone'];
	  $contactEmail = $record['fieldData']['Contact_email'];
	  $description = html_entity_decode( $record['fieldData']['Event_Description']);
	  $eventid = $record['fieldData']['eventid'];
	  $photo = $record['fieldData']['media_hero_path'];
	  $photoAlt = $record['fieldData']['main_alt_text'];
	  $photo2 = $record['fieldData']['extra_image_path'];
	  $photoAlt2 = $record['fieldData']['extra_image_alt_text'];
	  $infolink1 = $record['fieldData']['Info_Link1'];
	  $namelink1 = html_entity_decode($record['fieldData']['Info_Link1_Name']);
	  $externallink1 = $record['fieldData']['External_Link1'];
	  $infolink2 = $record['fieldData']['Info_Link2'];
	  $namelink2 = html_entity_decode($record['fieldData']['Info_Link2_Name']);
	  $externallink2 = $record['fieldData']['External_Link2'];
	  $infolink3 = $record['fieldData']['Info_Link3'];
	  $namelink3 = html_entity_decode($record['fieldData']['Info_Link3_Name']);
	  $externallink3 = $record['fieldData']['External_Link3'];
	  $type = $record['fieldData']['Type'];
	  $type1 = $record['fieldData']['Type_Primary'];
	  $type2 = $record['fieldData']['Type_Secondary'];
	  $tags = preg_split("/(\r\n|\n|\r)/", $type2);
	  $published = $record['fieldData']['Published'];
	  $contactCustomBlock = $record['fieldData']['Contact_CustomBlock'];   
	}                   
}
?>

<!DOCTYPE html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<?php
  if(check_db("Website CMS") == 1 && $invalid_id == false){
    echo' <title>'. strip_tags( $eventTitle).', Events, Spurlock Museum, U of I</title>';
  } else {
    echo' <title>Event Title, Events, Spurlock Museum, U of I</title>';
  }
?>

    <link rel="stylesheet" href="/css/app.css"/>
    <link rel="stylesheet" type="text/css" href="/css/FlatIcons/flaticon.css">   
  </head>
  
  <body>  
  <!-- Offside Menu -->
  <!-- Entire Site Nav-->
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
            <a class="menu-btn menu-btn-off-canvas-menu button right hide-for-large-up" href="#footer" aria-expanded="false"><span class="icon hamburger" title="Menu"></span><span class="hidden-for-small-only"> Menu</span></a>

            <a href="http://illinois.edu"><img class="i-block left" src="/img/core/icon_illinois-fc_300.png" alt="University of Illinois"></a>

            <div id="site-name">
              <a href="/"><span id="site-name-title">Spurlock<br class="hide-for-medium-up"> Museum</span><br class="hide-for-large-up"><span id="site-name-subtitle" class="show-for-medium-up"> of World Cultures <span id="site-name-illinois">at ILLINOIS</span></span></a>
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
      <!-- Entire Site Nav End-->

      <nav id="section-nav" class="full-width-band" aria-label="Events section navigation">
        <div class="row">
          <!-- Accordion Start -->     
          <div class="small-12 columns">
            <div id="site-accordian" class="hide-for-medium-up tabpanel" role="tablist"> 
              <div id="tab1" class="tab accordian selected button expand onepointfive" aria-selected="false" aria-controls="panel1" aria-expanded="false" role="tab" tabindex="0"> Events<span aria-hidden="true" class="icon chevron right"></span><span class="visuallyhidden"> Submenu</span>
              </div> 
              <div id="panel1"  class="accordian accordion-panel" aria-labelledby="tab1" aria-hidden="true" role="tabpanel"> 
                <ul class="controlList side-nav"> 
                  <li><a href="/events/">Overview</a></li>
                  <li><a href="/events/past.php">Past Events</a></li>
                </ul> 
              </div> 
            </div>
          </div>
          <!-- Accordion End --> 
          <!-- Horizontal menu Start -->
          <div class="small-12 columns">
            <div id="sub-horizontal-menu" class="show-for-medium-up">
              <h2 style="" class="left">Events <span class="visuallyhidden">Submenu</span></h2>
              <ul class="sub-nav no-bullet">
                <li><a class="secondary button" href="/events/">Overview</a></li>
                <li><a class="secondary button" href="/events/past.php">Past Events</a></li>
              </ul>
            </div>
          </div> 
          <!-- Horizontal Menu End --> 
        </div>
      </nav>
    </header>
    <!-- Navigation End -->  
       
    <main>
      <!--Skip to Main Content Target-->
      <a id="maincontent" tabindex="-1"></a>

      <div class="row">
        <div class="small-12 columns">
<?php         
  if($invalid_id == true){
          echo'<p></p><div class="callout panel text-center">You entered an invalid record id. Please try again.</div>
        </div>
      </div>';
  } else {
    if(check_db("web_events") == 1 && (($published == "Yes") || ($preview == 1))){ // JRK TODO: Fix this error code stuff
      if($err_code || $single_err_code) { 
        if($single_err_code == 401){
      		echo'<p></p><div class="callout panel text-center"><p>There are no records matching the record ID. </p></div>';}
        else{
      		echo'<p></p><div class="callout panel text-center">Sorry, the events database is currently under maintenence. Please try again later.</div>';
        }
        echo '</div>
      </div>';
      } else {

		  		echo"<div class='row'>
		        <div class='small-12 medium-10 large-8 medium-offset-1 large-offset-2 columns'>";      
    		if(empty($photo) && empty($photo2)) {
		      		echo "<div class='crop wide-crop placeholder ".$type1."'>
		          	<div class='icon-image'>
		            	<span class='visuallyhidden'>".$type1."</span>
		            	<span aria-hidden = 'true' class='icon ";
        	if ($type1 == "Special Event"){
            				echo"event-Special-Event";
        	} else{       
            				echo "event-".$type1;
        	}
		            	echo "'></span>
		          	</div>
		        	</div>";
    		}
      	if(!empty($photo)){
          		echo '<img src="/img/'.$photo.'" alt="'.$photoAlt.'">';
      	}
		          // [1/21/2019 DGM] moved breadcrumbs below image, as in blogposts   
		          echo'<div class="row">
		            <div class="small-12 columns">
		              <ol class="breadcrumbs" aria-label="breadcrumb navigation">
		                <li><a href="/">Home</a></li>
		                <li><a href="/events/">Events</a></li>
		                <li class="current">'.$eventTitle.'</li>
		              </ol>
		            </div>
		          </div>
		        </div>
		      </div>';
      
		      echo "<div class='row'>
		        <div class='small-12 medium-10 large-8 medium-offset-1 large-offset-2 columns'>
		          <h1>".$eventTitle."</h1>";
      	if($published != "Yes"){
		          echo '<section id="not-published" role="alert" aria-labelledby="not-published-heading">
		          	<h2 class="visuallyhidden" id="not-published-heading">Pre-Publication Draft</h2>
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
            		echo '<li><a href="/events/'.str_replace(' ', '-', $tags[$i]).'"><span class="tag-alt">'.$tags[$i].'<span class="visuallyhidden">tag</span></span></a></li>';
          	} else {
              // [06.02.16] AYX: replaced spaces with dashes so link/anchor works
            		echo '<li><a href="/events/#'.str_replace(' ', '-', $tags[$i]).'"><span class="tag-alt">'. $tags[$i] . '<span class="visuallyhidden"> tag</span></span></a></li> ';
          	}
        	}
      	}
      				echo '</ul>
      				<ul class="icon-bullet">
          			<li class="date" title="Date"><span class="visuallyhidden">Event Date: </span><strong>'.date('l, F j, Y',strtotime($dateStart)).'</strong>';
      	if(!empty($dateEnd)){
            			echo "<strong>&ndash;" .date('l, F j, Y',strtotime($dateEnd)). "</strong>";
    		} 
      					echo"</li>"; // JRK 3/25/2020: used to be within an else, but I think the <li> should end no matter what.

      	if(!empty($timeStart)) { 
          		# [02/13/2019 DM] date & time formatting  
      					echo'<li class="time" title="Time"><span class="visuallyhidden">Time: </span>';
        	if(empty($timeEnd)) {
          			echo  $timeStart."</li>";
        	} else if ($checkdayofweekstart != $checkdayofweekend & $checkdayofweekend != 0) {          
          			echo $timeStart." ".$dayofweekstart."&ndash;".$timeEnd." ".$dayofweekend."</li>";
        	} else {
          			echo $timeStart."&ndash;".$timeEnd."</li>";
        	}
      	}

      	if(!empty($location)){
          			echo '<li class="location" title="Location"><span class="visuallyhidden">Location: </span><a class="external" href="http://maps.google.com/?q='.urlencode($location).'">'.$location.'</a></li>';
      	}

        //Added by Jack 12/17/09
      	if(!empty($ageRange)) {
            		echo '<li class="age" title="Age"><span class="visuallyhidden">Age: </span> '.$ageRange.'</li>';
      	}

      	if(!empty($cost)) {
            		echo '<li class="cost" title="Cost"><span class="visuallyhidden">Cost: </span>'.$cost.'</li>';
      	}
		      		echo'</ul>
		  				<p></p>
		      		<p>'.$description.'</p>';
                    
      	if(!empty($photo2)) {
          // [10.13.10] MTR: Took out <br> @ beginning of next line.
          		echo "<img style='display:block' src='/img/".$photo2."' alt='/img/".$photoAlt2."'><br>";
      	}
        
      	if($location_external == "External Location"){
        			echo '<div class="alert-box alert">Please note that this program does NOT take place at the Spurlock Museum.</div>';
      	}
		     			echo '<a class="button onepointfive tiny export-button" title="Save event to iCalendar, Outlook, etc" href="/events/export-event.php?ID='.$ID.'"><span class="icon calendar-plus prepend" aria-hidden="true" title="Add Event to Calendar"></span><span class="visuallyhidden">Add Event to Calendar</span>Add to Calendar</a>
		   			</div>
		      </div>
		    </div> 
		  </div>'; 
        
      	if(!empty($infolink1)){
  		echo '<section id="related-text" class="full-width-band" aria-labelledby="related-text-heading">
        <div class="row">
          <div class="small-12 medium-10 large-8 medium-offset-1 large-offset-2 columns">
            <div class="panel">
              <h2 class="text-center subheader" id="related-text-heading">Related Links</h2> 
              <div class="text-center">
                <a ';
      		if ($externallink1 != ""){
                  echo "class='external' href='".$infolink1."'>".$namelink1."<span> (external link)</span></a>"; // THN 10-19-2010
        	} else {
                  echo "href='".$infolink1."'>".$namelink1."</a>";
        	}                      
        	if(!empty($infolink2)){
            			echo " | <a ";
          	if ($externallink2 != ""){
	              	echo "class='external' href='".$infolink2."'>".$namelink2."<span> (external link)</span></a>"; // THN 10-19-2010
          	} else {
              		echo "href='".$infolink2."'>".$namelink2."</a>";
          	}
      
          	if(!empty($infolink3)) {
              		echo " | <a ";
            	if ($externallink3 != ""){
                	echo "class='external' href='".$infolink3."'>".$namelink3."<span> (external link)</span></a>"; // THN 10-19-2010
            	} else {
                	echo "href='".$infolink3."'>".$namelink3."</a>";
            	}
          	}
        	}
          			echo'</div>
              </div>
          	</div>
          </div>
        </section>';    
      	}

      	if(!empty($contactName)) {
        echo '<section id="contact" class="full-width-band" aria-labelledby="contact-heading">
          <div class="row">  
            <div class="small-12 medium-10 large-8 medium-offset-1 large-offset-2 columns">
              <h2 id="contact-heading">Contact</h2>';
        	if(empty($contactEmail)) {
          		echo "<p>For further information on this event, contact <strong>" . $contactName . " </strong>at " . $contactPhone . ".</p>";
        	} else if(empty($contactPhone)) {
          		echo "<p>For further information on this event, contact <strong>" . $contactName . " </strong>at <a class='email' href='mailto:" . $contactEmail . "'>" . $contactEmail . "<span> (email link)</span></a></p>";
        	} else {
          		echo "<p>For further information on this event, contact <strong>" . $contactName . " </strong>at " . $contactPhone . " or <a class='email' href='mailto:" . $contactEmail . "'>" . $contactEmail . "<span> (email link)</span></a></p>";
        	}

        	if(!empty($contactCustomBlock)){
          		echo '<br><br>'.html_entity_decode($contactCustomBlock);
        	}

          		echo '<p>To request disability-related accommodations for this event, please contact <a href="/contact/#brian">Brian Cudiamat</a> at (217) 244-5586 or <a href="mailto:cudiamat@illinois.edu">cudiamat@illinois.edu (email link)</a>.</p>
        		</div>
      		</div>
        </section>';  
    		} else if(!empty($contactCustomBlock)){
        echo '<section id="contact" class="full-width-band" aria-labelledby="contact-heading">
          <div class="row">  
            <div class="small-12 medium-10 large-8 medium-offset-1 large-offset-2 columns">
              <h2 id="contact-heading">Contact</h2>'.html_entity_decode($contactCustomBlock).
        			'<p>To request disability-related accommodations for this event, please contact <a href="/contact/#brian">Brian Cudiamat</a> at (217) 244-5586 or <a href="mailto:cudiamat@illinois.edu">cudiamat@illinois.edu (email link)</a>.</p>      
            </div>
          </div>
        </section>';
    		}
    	}                        
 		} else { // if check_db fails
 					echo'<div class="row">
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
  		} else {
      		echo '<div class="callout panel text-center">Sorry, the events database is currently under maintenence. Please try again later.</div>';
    	}
  			echo '</div>
			</div>';
		}
	} // if invalid else 
?> 
 		</main>
		<!--Related Events start-->
<?php
 	
  // if there are related IDs, build related events section.
  if ($resultCount != 0) {
    if ($resultCount > 1) {
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
	  // get data from each related event.
  	foreach ($related_records as $record) {
      $show = true;
      $maxEventID = $record['fieldData']['max_eventID'];
      $eventTitle = html_entity_decode( $record['fieldData']['Title']);
      $dateStart = $record['fieldData']['Date_Start'];
      $dateEnd = $record['fieldData']['Date_End'];
      $type = $record['fieldData']['Type'];
			$eventID = $record['fieldData']['eventid'];

			global $today_day, $today_month, $today_year;

			$dateStart = $record['fieldData']['Date_Start'];
			$dateEnd = $record['fieldData']['Date_End'];
			$displayStart = $record['fieldData']['Date_Start_Display'];
			$displayEnd = $record['fieldData']['Date_End_Display'];
			$timeStart2 = $record['fieldData']['Time_Start_New'];
			$timeEnd = $record['fieldData']['Time_End'];
			$timeEnd2 = $record['fieldData']['Time_End_New'];

			if(!empty($dateEnd)) {
			  $endm = substr($dateEnd,0,2);
			  $endy = substr($dateEnd,-4,4);
			  $endd = substr($dateEnd,3,2);
			}

			$datespan = $record['fieldData']['Date_Span'];

			$type = $record['fieldData']['Type'];
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

			$test_event = $record['fieldData']['test_flag'];

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
			      echo "<li>
			       	<div class='card margin-fix' data-equalizer-watch='rel-card'>
			       		<div class='date-plaque-alt'>";
      	if (!empty($dateEnd) && ($dateStart != $dateEnd)) { # add condition to display start and end dates next to times as per sketch
		             	echo "<div>
		                <span class='month'>".$month_char."</span><span class='day'>".ltrim((substr($dateStart,3,2)), '0')."</span>
                    <div class='right'>
                      <span class='dayofweek'>".$dayofweek."</span>
                      <span class='year'>".$checky."</span>
                    </div>
                  </div>
                  <div> 
                    <span class='month '>" . "<span class='date-separator'>TO </span>". $end_month_char . "</span><span class='day'>" . ltrim((substr($dateEnd,3,2)), '0') . "</span>
                    <div class='right'>
                      <span class='dayofweek'>".$end_day_of_week."</span>
                      <span class='year'>".$endy."</span>
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
				          <div class='card-maininfo'>"; 
				      			echo'<span class="title"><a href="event.php?ID='.$eventID.'">'.$eventTitle .'</a></span>
				       		</div>
				       	</div> 
				      </div>
		        </li>'; // JRK: added two divs just before this li to get elements to match...3/25/2020
      } // if show == true		
    }	 // foreach related record
  } // if resultCount != 0 

	 				echo '</ul>
        </div>
      </div> 
    </section>';                            
?>                       
  	<!--Related Events end-->


		<!-- Adjacent Events Start-->
		<aside id="related-sequence" class="full-width-band lightbrown" <?php if($published != "Yes" || strtotime($date_publish) > time()) echo 'style="display:none" ';?> >
		  <div class="row">
		    <div class="small-12 columns">
		    	<h2 class="visuallyhidden">More Events</h2>
		    </div>
		  </div>
		  <div class="row">
		    <div class="medium-6 large-5 columns">
		      <a class="button expand secondary" <?php if($prevID == "") echo 'style="display:none" ';?> href="/events/event.php?ID=<?php echo $prevID; ?>"><span class="visuallyhidden">Previous Event:</span><span class="icon previous" aria-hidden="true" title="Previous Post"></span>  <?php echo $prevTitle;?></a>
		    </div>
		    <div class="medium-6 large-5 columns">
		      <a class="button expand secondary" <?php if($nextID == "") echo 'style="display:none" ';?> href="/events/event.php?ID=<?php echo $nextID; ?>"><span class="visuallyhidden">Next Event:</span><?php echo 
		        $nextTitle;?> <span class="icon next" aria-hidden="true" title="Next Post"></span></a>
		    </div>
		  </div>
		</aside>
      
		<!-- Recommended Pages Begin-->
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

		<!-- Recommended Pages End -->

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

