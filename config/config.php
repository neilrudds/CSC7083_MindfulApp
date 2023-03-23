<?php
// API Server
define('API_HOST', 'https://localhost:3000');

$link = API_HOST;

// Check connection
if(is_null($link)) {
    exit("ERROR: API Server not defined.");
}
?>