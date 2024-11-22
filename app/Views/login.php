<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=base_url()?>assets/css/style.css">
    <title>Login</title>
</head>
<body>
<div class="login">
    <h3 style="color:red; text-align:center;">
        <?php echo session()->getFlashdata("error"); ?>
    </h3>
    <form action="/toHome" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required> 
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
        <br />
        <p>Don't have an account, <a href="/register">register here</a></p>
    </form>
</div>
</body>
</html>