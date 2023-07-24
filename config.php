<?php
//Apsolute-Path of Server WITHOUT "/" at the end
$base_url = "/homepages/u72111/test_feed2mastodon";

//Feed-Url
$feed_url = 'https://www.tagesschau.de/infoservices/alle-meldungen-100~rss2.xml';

//Url of Maastodon instance
$instance_url = "https://mastodon.social";

//Bearer-Token
$bearer_token = '###SECRET TOKEN###';

//Is there an image in rss feed, which should be posted (<image>-Tage is naecassary): 0 false or 1 for true 
$image = 0;

// Template of Mastodon Post. You can use ###title###, ###description### and ###link### and \n for line-break. 
// Image is always shwon and the end of the post. 
$post_template = "###title###\n\n###description###\n\n➡️ ###link###?at_medium=mastodon&at_campaign=tagesschau.de"

?>