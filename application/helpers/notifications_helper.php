<?php
 /**
 * This file handles with notifications for Android and iOS users although I
 * don't discard to use it later for email notifications
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */


/**
 * Function name: sendAndroidNotification
 *
 * Description: This function takes a message and a list of tokens to send
 *              the message using Google Cloud Messaging.
 *
 * Parameters:
 * - $message: it's an array of data of a message, it frecuently has a 
 *             message and a title as attributes
 * - $tokens: it's an array of GCM ids stored in our database, it could be
 *            a simple GCM id inside an array
 *
 * Return: An array with the notification status of each token
 **/
if(!function_exists('sendAndroidNotification'))
{
    function sendAndroidNotification($message = array(), $tokens = array()){

        $message['vibrate'] = "true";
        $message['sound'] = 1;

        $fields = array(
            'registration_ids'  => $tokens,
            'data'              => $message
        );

        $headers = array(
            'Authorization: key=' . ANDROID_API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );

        return $result;
    }
}