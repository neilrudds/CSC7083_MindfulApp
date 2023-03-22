<?php
include("config/config.php");
include("session.php");

if(isset($_GET['logout'])) {
  // Clear the session variable, display logged out message
  // Unset all of the session variables.
  $_SESSION = array();

  // Finally, destroy the session.
  session_destroy();
}

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["token"])) {
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <style>
    li {
      list-style-type: none;
    }
  </style>
</head>

<body onload="loadMoodData()">
  <main>

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Mindful</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="#">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="moodSummary.php">Summary</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" aria-current="page" data-bs-toggle="modal" data-bs-target="#add-modal" href="#">Record
                New Mood Log</a>
            </li>
          </ul>
          <div>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                Hello, <?php echo htmlspecialchars($_SESSION["username"]); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Manage Account</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="index.php?logout">Logout</a></li>
              </ul>
            </li>
          </div>
        </div>
      </div>
    </nav>

    <div class="p-5 mb-4 bg-light bg-image rounded-3"
      style="background-image: url('assets/mental_health_banner.jpeg'); height: 250;">
      <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">
          Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?> to Mindful
        </h1>
        <p class="col-md-8 fs-4">
          The mood tracking app, designed to journal your daily mood and support your mental wellbeing.
        </p>
      </div>
    </div>

    <!-- Mood List -->
    <div class="container">
      <div class="row align-items-start">
        <div class="col text-start">
          <h2>My Moods</h2>
        </div>
        <div class="col text-end">
          <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
            data-bs-target="#add-modal">Record New Mood Log</button>
        </div>
      </div>
      <div class="row row-cols-1 row-cols-md-3 g-4" id="mood-data">
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
            <?php 
            if(!empty($_SESSION['insert_msg'])){
                echo '<div class="alert alert-primary">' . $_SESSION['insert_msg'] . '</div>';
            }        
            ?>
              <div class="mb-3">
                <label for="mood" class="form-label">Current mood:</label>
                <select class="form-select form-select-lg mb-3" name="mood" aria-label=".form-select-lg example">
                  <option selected>Please select a mood</option>
                  <?php
                        // Prepare GET request for mood types
                        $endpoint = $link . "/api/v1/mood/";

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
                <select class="form-select form-select-lg mb-3" name="editMood" id="editMood"
                  aria-label=".form-select-lg example">
                  <option selected>Please select a mood</option>
                  <?php
                        // Prepare GET request for mood types
                        $endpoint = $link . "/api/v1/mood/";

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

    <footer class="footer mt-4 py-3 bg-light text-center">
      <div class="container">
        <span class="text-muted">© 2023 Copyright: Neil Rutherford - Web Development CSC</span>
      </div>
    </footer>

  </main>
  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.3.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
  </script>
  <!-- Always remember to call the above files first before calling the bootstrap.min.js file -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"
    integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous">
  </script>
  <script type="text/javascript" src="js/mood.js"></script>
</body>
</html>