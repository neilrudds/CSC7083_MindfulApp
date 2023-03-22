<?php

// Include config file
require_once "../config/config.php";

//echo "<pre>";
//print_r($_POST);
//echo "</pre>";

// Initialize the session
session_start();

if (isset($_POST['deleteMoodLogId'])) {

    // Set parameters
	$moodLogId = strip_tags($_POST['deleteMoodLogId']);

    // Prepare POST request
    $endpoint = $link . "/api/v1/log/" . $moodLogId;

    // Request headers combined with the JSON body
    $opts = array(
        'http' => array(
            'method' => 'DELETE',
            'header' => 'Authorization: Bearer '.$_SESSION["token"]
        ),
        'ssl' => [
            'allow_self_signed'=> true
        ]
    );

    // Execute the request
    $context = stream_context_create($opts);
    $resource = file_get_contents($endpoint, false, $context);

    if ($resource === FALSE) {
        exit("Unable to delete the log!");
    }
}
?>