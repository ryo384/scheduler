<?php

if(!empty($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    foreach($errors as $errorMessage) {
        echo "<p>$errorMessage</p>";
    }
    unset($_SESSION['errors']);
}

if(!empty($_SESSION['messages'])) {
    $messages = $_SESSION['messages'];
    foreach($messages as $message) {
        echo "<p>$message</p>";
    }
    unset($_SESSION['messages']);
}

?>










