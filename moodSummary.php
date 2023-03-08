<?php
include("config.php");
include("session.php");

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["token"])) {
  header("location: login.php");
  exit;
} else {
  $token = $_SESSION["token"]; // Store the token in a variable to enable javascript access
  $userId = $_SESSION["userid"]; // Store the users id
}

header('Access-Control-Allow-Headers: Accept')
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
    <script type="text/javascript">
      var mySessionToken='<?php echo $token;?>';
      var myUserId='<?php echo $userId;?>';

    </script>
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

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Mindful</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
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
    <h2>My Mood Summary</h2>
    <canvas id="myChart" style="width:100%;max-width:700px"></canvas>
  </div>

  <footer class="bg-light text-center text-white">
    <!-- Grid container -->
      <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © 2023 Copyright: Neil Rutherford - Web Development CSC
      </div>
    <!-- Copyright -->
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
  <script src="js/chart.js"></script>
</body>

</html>