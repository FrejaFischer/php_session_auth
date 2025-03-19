<?php
session_start();
require_once 'initialise.php';
require_once 'classes/User.php';

$user = new User();

$token = $_SESSION['token'] ?? '';


// Logout POST
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $user->logout();
    $token = '';
}

// Check if Logged in (token is present)
if($token === ''){
    $message = 'You are not logged in';
} else {
    //$token = '1234'; // Check with fake token

    // Check if token is valid
    if($user->checkToken($token)) {
        $message = 'You are logged in - and the token valid';
    } else {
        $message = 'Your Token is invalid';
    }
}

include_once ROOT_PATH . '/public/header.php';
?>

<main>
    <p><?=$message?></p>
</main>

<?php include_once ROOT_PATH . '/public/footer.php';?>