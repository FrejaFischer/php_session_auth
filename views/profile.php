<?php
session_start();
require_once '../initialise.php';
require_once ROOT_PATH . '/classes/User.php';


$user = new User();
$token = $_SESSION['token'] ?? '';

// Check if user is logged in
if(!$user->isLoggedIn($token)){
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

include_once ROOT_PATH . '/public/header.php';
?>

<main>
    <p>Welcome to your profile</p>
</main>

<?php include_once ROOT_PATH . '/public/footer.php';?>
