<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script type="module" src="/index.js"></script>
  <link rel="stylesheet" href="/index.css" />
  <title>home</title>
</head>

<body>
  <main>
    <article>
      <h3>controller->method_attribute->route->view</h3>
      <p class="box ">from view file referenced by #[Route(path: '/home', view: "/home.php")] and Router::findController: home.php</p>
    </article>

    <article>
      <h3>controller->method_attribute->injector->html</h3>
      <div class="box indicator" id="home-controller"></div>
    </article>

    <article>
      <h3>router::findAsset->asset(css)</h3>
      <p class="box">
        <span class="test-css-linked">href="/index.css"</span>
      </p>
    </article>

    <article>
      <h3>router::findAsset->asset(img/png)</h3>
      <p class="box">
        <img src="/assets/alien_image.png" alt="image" />
      </p>
    </article>

    <article>
      <h3>router::findAsset->asset(js)</h3>
      <p class="box indicator" id="js-from-file"></p>
    </article>

  </main>
</body>

</html>