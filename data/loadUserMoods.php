<?php

// Include config file
require_once "../config/config.php";

// Initialize the session
session_start();

// If the user hasn't created a mood log for today, we will inform them in the add new mood modal.
$_SESSION['insert_msg'] = "";

if (isset($_SESSION["loggedin"]) && isset($_SESSION["userid"])) {
    
    $user_id = $_SESSION["userid"];

    // Prepare GET request for users logs
    $endpoint = $link . "/api/v1/log/";
    $endpoint = $endpoint . $user_id;

    $options = array(
      'http' => array(
          'method'  => 'GET',
          'header' => 'Authorization: Bearer '.$_SESSION["token"]
      ),
      'ssl' => [
        'allow_self_signed'=> true
      ]
    );

    // Execute the request
    $context  = stream_context_create($options);
    $resource = file_get_contents($endpoint, false, $context);
    $userlogdata = json_decode($resource, true);

    // Order data by date
    usort($userlogdata, function($a, $b) {
      return (strtotime($a['entry_timestamp']) < strtotime($b['entry_timestamp']) ? -1 : 1);
    });
    
    $today = new DateTime("today");
    
    foreach ($userlogdata as $item) { //foreach element in $arr
        $mood_log_id    = $item['mood_log_id'];
        $mood_id        = $item['mood_id'];
        $mooddata       = $item['mood_description'];
        $moodhtmlcolour = $item['html_colour'];
        $moodcomment    = $item['mood_comments']; //etc
        $whendata       = $item['entry_timestamp'];
        
        $formatdate = date("l jS F Y", strtotime($whendata));
        
        // is there an entry for today
        $diff     = $today->diff(new DateTime($formatdate));
        $diffDays = (int)$diff->format("%R%a");
        
        if ($diffDays === 0) {
            $moodLoggedToday = true;
        }

        $commentdata = htmlspecialchars($moodcomment);
        
        echo "<div class='col'>
          <div class='card' style='border-color: #$moodhtmlcolour!important'>
            <div class='card-header' style='background-color: #$moodhtmlcolour; color: #ffffff'>$formatdate</div>
            <div class='card-body'>
              <h5 class='card-text'>$mooddata</h5>
              <p class='card-text'><small class='text-muted'>$moodcomment</small></p>
            </div>
            <div class='card-footer text-muted' style='text-align: right'>
              <a href='#!' class='bi-pencil-square edit' data-id='$mood_log_id' data-comment='$commentdata'
                data-moodId='$mood_id' data-bs-toggle='modal' data-bs-target='#edit-modal'></a>
              <a href='#!' class='bi-trash delete' data-id='$mood_log_id' data-bs-toggle='modal'
                data-bs-target='#delete-modal'></a>
            </div>
          </div>
        </div>";
    }

    // Present the add new mood modal
    if (!isset($moodLoggedToday) || $moodLoggedToday !== true){
      $_SESSION['insert_msg'] = "Please submit your mood log for today.";
      echo '<script type="text/javascript">';
      echo '$("#add-modal").modal("show")';
      echo '</script>';
    }
    
} else {
    exit('User is not logged in, unauthorised access.');
}
?>