<?php

// Include config file
require_once "../config/config.php";

// Initialize the session
session_start();

if (isset($_SESSION["loggedin"]) && isset($_SESSION["userid"])) {
    if (isset($_POST['mood'])) {

        // Set parameters
        $user_id = $_SESSION["userid"];
        $mood_id = strip_tags($_POST['mood']);
        $comment = strip_tags($_POST['comment']);

        // Prepare POST request
        $endpoint = $link . "/api/v1/log";

        // The JSON body to send to the API
        $postData = array(
            'user_id' => $user_id,
            'mood_id' => $mood_id,
            'mood_comments' => $comment
        );

        // Request headers combined with the JSON body
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$_SESSION["token"]
                ),
                'content' => json_encode($postData)
            ),
            'ssl' => [
                'allow_self_signed'=> true
            ]
        );

        // Execute the request
        $context = stream_context_create($opts);
        $resource = file_get_contents($endpoint, false, $context);

        if ($resource === FALSE) {
            exit('Unable to add new log!');
        }
    }
    else {
        exit('Post request is missing data.');
    }
} else {
    exit('User is not logged in, unauthorised access.');
}
?>