<?php
include("config/config.php");
include("session.php");

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
  <title>Mindful | Manage Account</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <style>
    li {
      list-style-type: none;
    }
  </style>
</head>

<body>
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
              <a class="nav-link active" aria-current="page" href="index.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="moodSummary.php">Summary</a>
            </li>
          </ul>
          <div>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                Hello, <?php echo htmlspecialchars($_SESSION["username"]); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="account.php">Manage Account</a></li>
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
          <h2>Account Management</h2>
          <br>
        </div>
      </div>
      <div class="d-flex justify-content-center">
        <div>
        <br>
          <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                data-bs-target="#delete-modal">Delete Account</button>
          <br>
          <br>
        </div>
      </div>
    </div>

    <!-- Delete Account -->
    <div id="delete-modal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Delete Account</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="deleteAccountForm" name="delete-account" role="form" method="post">
            <div class="modal-body">
              <div class="mb-3">
                Are you sure you want to permanently delete your account? This cannot be undone!
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
  <script>
  $(document).ready(function () {
    // When the user submits the delete request
    $("#deleteAccountForm").submit(function (e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.

        $.ajax({
            type: "POST",
            url: "data/deleteAccount.php",
            data: $('form#deleteAccountForm').serialize(),
            cache: false,
            success: function (response) {
              window.location.href = 'index.php?logout';
            },
            error: function (response) {
                alert(response); // show response from the php script.
            }
        });
    });
  });
  </script>
</body>
</html>