<?php
session_start();
require_once 'functions.php';
 //sec_session_start(); // Our custom secure way of starting a PHP session.
 
if (isset($_POST['email'], $_POST['p'])) {
    $email = $_POST['email'];
    $password = $_POST['p']; // The hashed password.
 
    if (login($email, $password) == true) {
        // Login success 
        header('Location: ../admin');
    } else {
        // Login failed 
        header('Location: ../login?error=1');
    }
} else {
    // The correct POST variables were not sent to this page. 
    echo 'Invalid Request';
}