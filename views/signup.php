<?php
require_once '../initialise.php';
require_once ROOT_PATH . '/classes/User.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();

    // Saving values from form to ensure inputs value stays if error accures
    $name = htmlspecialchars($_POST['name']) ?? '';
    $email = htmlspecialchars($_POST['email']) ?? '';

    $validationErrors = $user->validateSignup($_POST);

    if(!empty($validationErrors)) {
        $validationFailure = true;
        $errorMessage = join(', ', $validationErrors);
    } else {
        if (!$user->insert($_POST)) {
            $errorMessage = 'It was not possible to insert the new user';
        } else {
            header('Location: ' . BASE_URL . '/index.php');
        }
    }
}


include_once '../public/header.php';
?>

<main>
    <?php if(isset($errorMessage)):?>
            <section>
                <p class="error"><?=$errorMessage?></p>
            </section>
    <?php endif; ?>
    <form action="signup.php" method="POST">
        <div>
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value=<?=isset($name)? $name :''?>>
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value=<?=isset($email)? $email :''?>>
        </div>
        <div>
            <label for="pwd">Password</label>
            <input type="password" name="pwd" id="pwd">
        </div>
        <div>
            <label for="repeatPwd">Repeat Password</label>
            <input type="password" name="repeatPwd" id="repeatPwd">
        </div>

        <button type="submit">Create user</button>
    </form>
</main>

<?php include_once '../public/footer.php';?>