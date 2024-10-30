<?PHP
/*
Plugin Name: Commentwitter
Plugin URI: http://www.jonbishop.com/downloads/wordpress-plugins/commentwitter-plugin-page/
Description:  Commentwitter is a WordPress plugin that gives commenters the option of Tweeting their comment with a link to your post.
Version: 2.1
Author: Jon Bishop
Author URI: http://www.jonbishop.com
License: GPL2
*/

function create_commentwitter_shorturl($url){
	$login = 'commentwitter';
	$apiKey = 'R_33a8ddf622ec237ac1ab60a4a48ee2bb';
	  
	$ch = curl_init();
	$apiURL = 'http://api.bit.ly/shorten?version=2.0.1&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$apiKey;
	$post_string = 'longUrl='.urlencode($sURL).'&version=2.0.1';
	$options = array(
	  CURLOPT_URL => $apiURL,
	  CURLOPT_RETURNTRANSFER => true
	);
	curl_setopt_array($ch, $options);
	$response = curl_exec($ch);
	curl_close($ch);
	
	$response = json_decode($response);
	if($response->statusCode!='OK') {
	  return '';
	}
	$shortUrl = $response->results->$url->shortUrl;
	return $shortUrl;
}
function create_commentwitter_tweet(){
	$ct_comment_tweet = "";
	$ct_comment_id = get_comment_ID();
	$ct_comment_link = get_comment_link();
	$ct_comment_data = get_comment($ct_comment_id , ARRAY_A);
	$ct_comment_tweet = get_comment_text(stripslashes(trim($ct_comment_id)));
	
	$ct_comment_shortUrl = create_commentwitter_shorturl($ct_comment_link);
	
    if(strlen($ct_comment_tweet)<120){
		$ct_comment_tweet = $ct_comment_tweet . " " . $ct_comment_shortUrl;
	} else {
		$ct_comment_tweet = substr($ct_comment_tweet,0,115) . '... ' . $ct_comment_shortUrl;
	}
	return $ct_comment_tweet;
}
function insert_commentwitter($content) {
	$content = $content . ' <a href="http://twitter.com/home/?status=' . urlencode(create_commentwitter_tweet()) . '" target="_blank" class="comment-reply-link">Tweet</a>';
	return $content;
}

add_filter('comment_reply_link', 'insert_commentwitter');
?>