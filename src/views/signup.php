<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.classless.fuchsia.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="https://img.icons8.com/?size=512w&id=13910&format=png">
    <link rel="stylesheet" href="../styles/index.css">

</head>

<body style="padding: 2rem;">
    <?php include("header.php"); ?>
    <h1>Sign up</h1>
    <article style="width : auto;">
        <form action="/signup/submit" method="post" style="display: flex; flex-grow : 1; gap : 1rem; flex-wrap : wrap;">
            <!-- username -->
            <input type="text" name="username" placeholder="Username" style="margin: 0; width : auto; flex-grow : 1;">
            <!-- email -->
            <input style="margin: 0; width : auto; flex-grow : 1;" type="email" name="email" placeholder="Email">
            <!-- password -->
            <input style="margin: 0; width : auto; flex-grow : 1;" type="password" name="password" placeholder="Password">
            <button style="margin: 0;" type="submit">Signup</button>

        </form>
    </article>

</body>

</html>