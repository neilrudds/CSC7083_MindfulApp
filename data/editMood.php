<?php

// Include config file
require_once "../config/config.php";

// Initialize the session
session_start();

// If the deleteMoodLogId is set in the POST request
if (isset($_SESSION["loggedin"]) && isset($_SESSION["userid"])) {
    if (isset($_POST['editMoodLogId'])) {

        // Set parameters
        $mood_log_id = strip_tags($_POST['editMoodLogId']);
        $moodid = strip_tags($_POST['editMood']);
        $comment = strip_tags($_POST['editComment']);

        // Prepare POST request
        $endpoint = $link . "/api/v1/log/" . $mood_log_id;

        // The JSON body to send to the API
        $postData = array(
            'mood_id' => $moodid,
            'mood_comments' => $comment
        );

        // Request headers combined with the JSON body
        $opts = array(
            'http' => array(
                'method' => 'PATCH',
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
            exit("Unable to update log!");
        }
    }
} else {
    exit('User is not logged in, unauthorised access.');
}
?>