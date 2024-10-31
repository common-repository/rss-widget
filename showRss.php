<?php
/*
Plugin Name: Show RSS FEED
Plugin URI: http://www.contentmanufaktur.net
Description: Shows RSS Feeds.
Version: 1.0.0
Author: CodersX
Author URI: http://www.codersx.net
*/


function show_rssFeed($args) {

extract($args);

$data = get_option('cxRssFeed');

echo $before_widget."<br /><br />"; 

echo "<h3>". $data['title'] ."</h3>";

$xmlfile = $data['feed'];
$xml = simplexml_load_file(rawurlencode($xmlfile));
if ($xml) {
	$counter = count($xml->channel->item);
	if($counter > $data['postCount']) { $counter = $data['postCount']; }
	for ($i=0; $i<$counter; $i++) {
		$link = $xml->channel->item[$i]->link;
		$title = $xml->channel->item[$i]->title;
		$description = $xml->channel->item[$i]->description;
		echo '<p><a href="'. $link .'">'. $title .'</a><br />';
		if($data['wordcount'] > 0) {
			$array=explode(" ",strip_tags($description));
			$array=array_slice($array,0,$data['wordcount']);
			$description=implode(" ", $array);
			$description .= "...";
			echo $description;
		}	
		echo '</p>';
	}
}

echo $after_widget;
}

function control_rssFeed() {

  $data = get_option('cxRssFeed');
  ?>
  <p><label>Feed URL:<input name="feed"
type="text" value="<?php echo $data['feed']; ?>" /></label></p>
  <p><label>Anzahl der Posts:<input name="postCount"
type="text" value="<?php echo $data['postCount']; ?>" /></label></p>
<p><label>Title:<input name="title"
type="text" value="<?php echo $data['title']; ?>" /></label></p>
<p><label>W&ouml;rteranzahl (0 wenn der Post nicht angeteasert werden soll):<input name="wordcount"
type="text" value="<?php echo $data['wordcount']; ?>" /></label></p>
  <?php
   if (isset($_POST['feed'])){
    $data['feed'] = attribute_escape($_POST['feed']);
    $data['postCount'] = attribute_escape($_POST['postCount']);
    $data['title'] = attribute_escape($_POST['title']);
    $data['wordcount'] = attribute_escape($_POST['wordcount']);
    update_option('cxRssFeed', $data);
  }
}

register_activation_hook( __FILE__, 'activate');
register_deactivation_hook( __FILE__, 'deactivate');

  function activate(){
    $data = array( 'feed' => 'http://www.seo-book.de/feed' ,'postCount' => 5, 'title' => 'SEO-Book', 'wordcount' => 0);
    if ( ! get_option('widget_name')){
      add_option('cxRssFeed' , $data);
    } else {
      update_option('cxRssFeed' , $data);
    }
  }
  function deactivate(){
    delete_option('cxRssFeed');
  }


function init_weather_widget() {
register_sidebar_widget("RSS Widget", "show_rssFeed");
register_widget_control("RSS Widget", "control_rssFeed");
}

add_action("plugins_loaded", "init_weather_widget");

?>
