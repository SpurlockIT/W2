<?php
require_once(dirname(__FILE__).'/../db-support/fmREST/fmREST.php');
require_once(dirname(__FILE__).'/../db-support/clean.php');

    
//check DB FILE HERE
    
    define('BASE_URL', 'http://www.spurlock.illinois.edu/db-support/');

    $database_server_ip = '128.174.89.153';
    $database_server_name = 'db1.spurlock.illinois.edu';

    $fm = new fmREST($database_server_name, 'Web_Events', "", "", 'Web_Events');
    //$fm = new fmREST($database_server_name, "web_events", "", "");
    
//Useful Information test
    
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

    //echo $single_err_code;
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
//echo $err_code;
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

        echo $location;
      }
        echo "Hello World";
    
        echo' <title>'. strip_tags( $eventTitle).', Events, Spurlock Museum, U of I</title>';
    
    
        echo $type1;



echo " <html class='no-js' lang='en'>
    <head>
    <meta charset='utf-8' />
    </head>
    <body>
        
     Hello World
    </body>

    </html>"
   ?>