console.log("Hello from index.js");

// js asset linked indicator
const root = document.querySelector("#js-from-file");
root.innerHTML += "<p>JS Asset linked</p>";

// css asset linked indicator
const testAssetLinked = document.querySelector("#test-asset-linked");
testAssetLinked.innerHTML = "<p>Asset linked</p>";

// img asset linked indicator
const testImgLinked = document.querySelector("#test-img-linked");
if (!testImgLinked.complete || (typeof testImgLinked.naturalWidth != "undefined" && testImgLinked.naturalWidth == 0)) {
  testImgLinked.remove();
}
