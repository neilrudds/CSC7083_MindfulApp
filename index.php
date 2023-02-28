<?php
include("config.php");
include("session.php");

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["loggedin"])) {
  header("location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mindful</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.3.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <script type="text/javascript" src="js/mood.js"></script>
    <style>
      body {
        font: 14px sans-serif;
      }

      .wrapper {
        width: 360px;
        padding: 20px;
      }
    </style>
  </head>

<body onload="loadMoodData()">
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Mindful</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="moodSummary.php">Summary</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" aria-current="page" data-bs-toggle="modal" data-bs-target="#add-modal" href="#">Add log!</a>
          </li>
        </ul>
        <ul class="navbar-nav ml-auto">
          <span class="navbar-text">
            Hello, <?php echo htmlspecialchars($_SESSION["username"]); ?>
          </span>
        </ul>
      </div>
    </div>
  </nav>

  <div class="jumbotron jumbotron-fluid">
    <div class="container">
      <h1 class="display-4">Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?> to Mindful</h1>
      <p class="lead">The mood tracking app, designed to journal your daily mood and support your mental wellbeing.</p>
    </div>
  </div>

  <!-- Mood List -->
  <div class="container">
    <h2>My Moods</h2>
    <div class="card-columns" id="mood-data">
    </div>
  </div>

  <!-- Insert Modal -->
  <div id="add-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add a new mood entry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="addMoodForm" name="add-mood" role="form">
          <div class="modal-body">				
          <div class="mb-3">
                <label for="mood" class="form-label">Current mood:</label>
                <select class="form-select form-select-lg mb-3" name="mood" aria-label=".form-select-lg example" >
                  <option selected>Please select a mood</option>
                  <?php
                    // Prepare GET request for mood types
                    $endpoint = $link . "/api/v1/mood/";

                    $options = array('http' => array(
                      'method'  => 'GET',
                      'header' => 'Authorization: Bearer '.$_SESSION["token"]
                    ));

                    // Execute the request
                    $context  = stream_context_create($options);
                    $resource = file_get_contents($endpoint, false, $context);
                    $mooddata = json_decode($resource, true);

                    foreach ($mooddata as $item) { //foreach element in $arr
                      $moodid = $item['mood_id'];
                      $mooddescription = $item['description'];

                      echo "<option value='$moodid'>$mooddescription</option>";
                    }
                  ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="formComment" class="form-label">Trigger comments:</label>
                <textarea class="form-control" name="comment" id="formComment" rows="3"></textarea>
              </div>				
          </div>
          <div class="modal-footer">					
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-success" id="submit" value="Save">
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="edit-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit mood entry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editMoodForm" name="edit-mood" role="form">
          <div class="modal-body">
          <input type="hidden" id="editMoodLogId" name="editMoodLogId">			
          <div class="mb-3">
                <label for="mood" class="form-label">Current mood:</label>
                <select class="form-select form-select-lg mb-3" name="editMood" id="editMood" aria-label=".form-select-lg example" >
                  <option selected>Please select a mood</option>
                  <?php
                    // Prepare GET request for mood types
                    $endpoint = $link . "/api/v1/mood/";

                    $options = array('http' => array(
                      'method'  => 'GET',
                      'header' => 'Authorization: Bearer '.$_SESSION["token"]
                    ));

                    // Execute the request
                    $context  = stream_context_create($options);
                    $resource = file_get_contents($endpoint, false, $context);
                    $mooddata = json_decode($resource, true);

                    foreach ($mooddata as $item) { //foreach element in $arr
                      $moodid = $item['mood_id'];
                      $mooddescription = $item['description'];

                      echo "<option value='$moodid'>$mooddescription</option>";
                    }
                  ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="formComment" class="form-label">Trigger Comments:</label>
                <textarea class="form-control" name="editComment" id="editComment" rows="3"></textarea>
              </div>				
          </div>
          <div class="modal-footer">					
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-success" id="submit" value="Update">
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete Modal -->
  <div id="delete-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Delete mood record</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="deleteMoodForm" name="delete-mood" role="form" method="post">
          <div class="modal-body">
          <input type="hidden" id="deleteMoodLogId" name="deleteMoodLogId">			
            <div class="mb-3">
                Are you sure you want to delete this mood record?
            </div>
          </div>
          <div class="modal-footer">					
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-danger" id="submit" value="Delete">
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <footer class="bg-light text-center text-white">
    <!-- Grid container -->
      <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © 2023 Copyright: Neil Rutherford - Web Development CSC
      </div>
    <!-- Copyright -->
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>