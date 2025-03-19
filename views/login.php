<?php
session_start();

require_once '../initialise.php';
require_once ROOT_PATH . '/classes/User.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();

    $validationErrors = $user->validateLogin($_POST);

    if(!empty($validationErrors)) {
        $validationFailure = true;
        $errorMessage = join(', ', $validationErrors);
    } else {
        if(!$user->authenticateUser($_POST)){
            $errorMessage = 'Incorrect login information';
        } else {
            if(!$user->setTokens($_POST)){
                $errorMessage = 'Failed to set token - try to login again';
            } else {
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            }
        }
    }
    
}

include_once '../public/header.php'
?>

<main>
    <?php if(isset($errorMessage)):?>
            <section>
                <p class="error"><?=$errorMessage?></p>
            </section>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="pwd">Password</label>
            <input type="password" name="pwd" id="pwd" required>
        </div>

        <button type="submit">Login</button>
    </form>
</main>

    <?php include_once '../public/footer.php';?>
