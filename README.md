# rss2mastodon

rss2mastodon is a PHP-script to publish a mastodon-post out of standard rss2-feed. 

## Installation
Copy files in a Folder on your server. The files do not have to be public accessible.
Make your changes in config.php 

```bash
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
```
Add a cronjob in your crontab

```bash
* * * * * php /homepages/uXXXX/rss2mastodon/getFeedAndPostToMastodon.php
```

## Usage
Feel free to change the script however you like, so that it fits to your feeds. getFeedAndPostToMastodon_tsde-special.php is an example for a special feed from tagesschau.de, which appends hashtags to the standard-post. 
The last guid of the posted feed-item is stored in a text-file last_entry_mastofeed.txt. 

## Contributing
Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.