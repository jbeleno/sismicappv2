<?php
 /**
 * This file handles with publications in social networks, nowadays it's 
 * available Twitter
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */


/**
 * Function name: writeTweet
 *
 * Description: This function takes a message and publish it in the @sismicapp
 *				timeline as status.
 *
 * Parameters:
 * - $msg <String>: it's a String with a message to write as a tweet in @sismicapp
 *
 * Return: NULL
 **/
if(!function_exists('writeTweet'))
{
    function writeTweet($msg = ""){
    	if($msg != ""){

    		require(APPPATH.'third_party/twitter_api_exchange.php');

    		$settings = array(
			    'oauth_access_token' => TW_ACCESS_TOKEN,
			    'oauth_access_token_secret' => TW_ACCESS_TOKEN_SECRET,
			    'consumer_key' => TW_CONSUMER_KEY,
			    'consumer_secret' => TW_CONSUMER_SECRET
			);

			$twitter = new TwitterAPIExchange($settings);

			$result = $twitter->buildOauth(TW_URL_API, HTTP_REQUEST_POST)
			    			  ->setPostfields(array('status' => $msg))
			    			  ->performRequest();			
    	}
    }
}