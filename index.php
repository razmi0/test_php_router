<?php

try {
    //code...
    define("BASE_DIR", __DIR__);
    require_once BASE_DIR . '/utils.php';

    print_that("[INDEX]",  "start");
    $time1 = mark();
    require_once BASE_DIR . '/src/router/starter.php';
    print_that("[INDEX]", "end");
    print_that("[INDEX]", "duration " . measure($time1) . " ms");
} catch (Throwable $th) {
    throw $th;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src="/index.js"></script>
    <title>Document</title>
</head>

<body>
    <h1>Index</h1>
    <img src="/assets/alien_image.png" alt="image">
</body>

</html>