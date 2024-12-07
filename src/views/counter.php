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
  <style id="indicator-tag"></style>
  <main>
    <article>
      <h3>controller / method_attribute / route / view</h3>
      <p class="box indicator">
        <span>Controller works</span>
      </p>
    </article>

    <article>
      <h3>controller / method_attribute / ContentInjector</h3>
      <div class="box indicator" id="counter-controller"></div>
    </article>

    <article>
      <h3>router::findAsset / asset(css)</h3>
      <p class="box indicator" id="test-asset-linked">
      </p>
    </article>

    <article>
      <h3>router::findAsset / asset(img/png)</h3>
      <p class="box indicator">
        <img id="test-img-linked" src="/assets/alien_image.png" alt="image" />
      </p>
    </article>

    <article>
      <h3>router::findAsset / asset(js)</h3>
      <p class="box indicator" id="js-from-file"></p>
    </article>

  </main>
</body>

</html>