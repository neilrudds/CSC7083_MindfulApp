<?php
/* API Server */
define('API_HOST', 'http://localhost:3000');

$link = API_HOST;

// Check connection
if(is_null($link)) {
    die("ERROR: API Server not defined.");
}
?>