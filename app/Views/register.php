<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=base_url()?>assets/css/style.css">
    <title>Register</title>
</head>
<body>
    <div class="register">
        <h1>Register</h1>
        <h3 style="color:red; text-align:center;">
            <?php echo session()->getFlashdata("error"); ?>
        </h3>
        <form action="/toLogin" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required> 
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required> 
            <label for="confirmpassword">Confirm Password:</label>
            <input type="password" id="confirmpassword" name="confirmpassword" required> 
            <button type="submit">Register</button>
            <br />
            <p>Already have an account, <a href="/">login</a></p>
        </form>
    </div>
</body>
</html>