<?php
require_once '../initialise.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();

    // $validationErrors = $user->validate($_POST);

    // if(!empty($validationErrors)) {
    //     $validationFailure = true;
    //     $errorMessage = join(', ', $validationErrors);
    // } else {
    //     if (!$user->insert($_POST)) {
    //         $errorMessage = 'It was not possible to insert the new user';
    //     }
    // }
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
            <input type="email" name="email" id="email">
        </div>
        <div>
            <label for="pwd">Password</label>
            <input type="password" name="pwd" id="pwd">
        </div>

        <button type="submit">Login</button>
    </form>
</main>

    <?php include_once '../public/footer.php';?>
