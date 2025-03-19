<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <title>Session</title>
</head>
<body>
    <header>
        <ul>
            <li><a href=<?=BASE_URL?>>Home</a></li>
            <?php if(isset($_SESSION['token'])):?>
                <li><a href=<?=BASE_URL . "/views/profile.php"?>>Profile</a></li>
            <?php else:?>
                <li><a href=<?=BASE_URL . "/views/signup.php"?>>Signup</a></li>
                <li><a href=<?=BASE_URL . "/views/login.php"?>>Login</a></li>
            <?php endif;?>
        </ul>
        <?php if(isset($_SESSION['token'])):?>
            <form action=<?=BASE_URL . '/index.php'?> method="POST">
                <button type="submit">Logout</button>
            </form>
        <?php endif;?>
    </header>