<?php
include('config.php');

$media_sleep = false;
$max_entries = 1;
$rawXML = (file_get_contents($feed_url));
$xml = simplexml_load_string($rawXML,'SimpleXMLElement',LIBXML_NOCDATA);
$entries = $xml->channel->item;
$counter = 0;
  foreach($entries as $root) {
    $counter++;
    // Ausgabe nach x Einträgen beenden:
    if($counter > $max_entries) {
      break;
    } 
    $guid = $root->guid;
    $imageUrl = $root->image;
    $description = $root->description;
    
    file_put_contents($base_url.'/images/image.jpg', file_get_contents($imageUrl));  
    $saved_feedentry = file_get_contents($base_url.'/last_entry_mastofeed.txt');
    if ($saved_feedentry != htmlspecialchars(($root->guid))) {
            if (file_put_contents($base_url.'/last_entry_mastofeed.txt', htmlspecialchars($root->guid))){
                $placeholders= array("###title###", "###description###", "###link###");
                $replacers= array($root->title ,$description, $root->link);
                  // the main status update array, this will have media IDs added to it further down
                  $status_data = array(
                    "status" => str_replace($placeholders, $replacers, $post_template),
                    "language" => "de",
                    "visibility" => "public"
                  );

                  // if we are posting an image, send it to Mastodon
                  // using a single image here
                  if ($image == "1") {
          
                  // get location of image on the filesystem
                  
                  // add images to files array, this is a single image for demo
                  $files = array();
                  $files[$image] = file_get_contents($base_url.'/images/image.jpg');

                  // make a multipart-form-data delimiter
                  $boundary = uniqid();
                  $delimiter = '-------------' . $boundary;

                  $post_data = '';
                  $eol = "\r\n";

                  foreach ($files as $name => $content) {
                    $post_data .= "--" . $delimiter . $eol . 'Content-Disposition: form-data; name="file"; filename="' . $name . '"' . $eol . 'Content-Transfer-Encoding: binary' . $eol;
                    $post_data .= $eol;
                    $post_data .= $content . $eol;
                  }
                  $post_data .= "--" . $delimiter . "--".$eol;
                  $media_headers = [
                    "Authorization: Bearer $bearer_token",
                    "Content-Type: multipart/form-data; boundary=$delimiter",
                    "Content-Length: " . strlen($post_data)
                  ];

                  // send the image using a cURL POST
                  $ch_media_status = curl_init();
                  curl_setopt($ch_media_status, CURLOPT_URL, "$instance_url/api/v2/media");
                  curl_setopt($ch_media_status, CURLOPT_POST, 1);
                  curl_setopt($ch_media_status, CURLOPT_RETURNTRANSFER, true);
                  curl_setopt($ch_media_status, CURLOPT_HTTPHEADER, $media_headers);
                  curl_setopt($ch_media_status, CURLOPT_POSTFIELDS, $post_data);
                  $media_response = curl_exec($ch_media_status);
                  $media_output_status = json_decode($media_response);
                  $media_info = curl_getinfo($ch_media_status);
                  curl_close ($ch_media_status);
                  $http_code = $media_info['http_code'];
                
                 // check the return status of the POST request
                  if (($http_code == 200) || ($http_code == 202)) {
                   $status_data['media_ids'] = array($media_output_status->id); // id is a string
                    $post_to_mastodon = true;
                  if ($http_code == 200) {
                    // 200: MediaAttachment was created successfully, and the full-size file was processed synchronously (image)        
                    $media_sleep = false;
                  }
                  else if ($http_code == 202) {
                    // 202: MediaAttachment was created successfully, but the full-size file is still processing (video, gifv, audio)
                    // Note that the MediaAttachment’s url will still be null, as the media is still being processed in the background
                    // However, the preview_url should be available
                    $media_sleep = true;
                  }
                  else {
                    $post_error_message = "Error posting media file";
                  }
                }
                else {
                  $post_error_message = "Error posting media file, error code: " . $http_code;
                }
              }
              // wait for the complex media to finish processing on server
              // this is only so when the status is posted the video can be watched right away
              if ($media_sleep) {
                sleep(5);
              }

              $headers = [
                "Authorization: Bearer $bearer_token",
                'Content-Type: application/json'
              ];

              // JSON-encode the status_data array
              $post_data = json_encode($status_data);

              // Initialize cURL with headers and post data
              $ch_status = curl_init();
              curl_setopt($ch_status, CURLOPT_URL, "$instance_url/api/v1/statuses");
              curl_setopt($ch_status, CURLOPT_POST, 1);
              curl_setopt($ch_status, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch_status, CURLOPT_HTTPHEADER, $headers);
              curl_setopt($ch_status, CURLOPT_POSTFIELDS, $post_data); // send the JSON data

              // Send the JSON data via cURL and receive the response
              $output_status = json_decode(curl_exec($ch_status));

              // Close the cURL connection
              curl_close ($ch_status);
             };                  
    }
}
?>