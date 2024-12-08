<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.classless.fuchsia.min.css">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.colors.min.css">
    <link rel="stylesheet" href="/index.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="https://img.icons8.com/?size=512w&id=13910&format=png">
    <script type="module" src="/form.js" defer></script>

</head>

<body style="padding: 2rem;">
    <?php include("header.php"); ?>
    <main>
        <h1>Login</h1>
        <article>
            <div id="form-control"></div>

            <form action="/login/submit" method="post" class="signup-form" style="display: flex; flex-grow : 1; gap : 1rem; flex-wrap : wrap;">
                <!-- username -->
                <input type="text" name="username" placeholder="Username" style="margin: 0; width : auto; flex-grow : 1;">
                <!-- email -->
                <input type="email" name="email" placeholder="Email" style="margin: 0; width : auto; flex-grow : 1;">
                <!-- password -->
                <input type="password" name="password" placeholder="Password" style="margin: 0; width : auto; flex-grow : 1;">
                <button type="submit" style="margin: 0;">Login</button>
            </form>
    </main>
</body>

</html>