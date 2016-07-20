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
 * Function name: sendPushNotification
 *
 * Description: This function takes a message and a list of tokens to send
 *              the message using Firebase Cloud Messaging.
 *
 * Parameters:
 * - $message <String>: it's an array of data of a message, it frecuently has a 
 *             message and a title as attributes
 * - $tokens <Array>: it's an array of FCM ids stored in our database, it could be
 *            a simple FCM id inside an array
 *
 * Return: An array with the notification status of each token
 **/
if(!function_exists('sendPushNotification'))
{
    function sendPushNotification($message = array(), $tokens = array(), $auth_key = ""){

        // Android settings
        $message['vibrate'] = "true";
        $message['sound'] = 1;

        // iOS settings
        $message['badge'] = "Increment";
        $message['sound'] = 1;

        $fields = array(
            'registration_ids'  => $tokens,
            'data' => $message
        );

        $headers = array(
            'Authorization: key=' . $auth_key,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
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