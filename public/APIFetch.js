import { displayMessage, displayProduits, dom, insertIdsUpdate } from "./helpers/dom.js";
import * as APIFetch from "./helpers/fetch_functions.js";
import { themeSetup } from "./helpers/theme-toggle.js";
console.log("Starting the app...");
// Je n'ai pas créé de fonction displayProduit car displayProduits est capable de gérer un seul produit à afficher.
/**
 *
 * Setup the read all logic
 *
 * Fetch all products
 * Display them in a table (displayProduits)
 * Display an error message if there is an error
 * Display a success message if the fetch is successful
 *
 */
const getProduits = () => {
  console.log("Setting up read all logic...");
  const { btn, output } = dom.read;
  const handleMouseDown = async () => {
    console.log("Fetching data...");
    const [json, response] = await APIFetch.fetchReadAll();
    if (json.error) displayMessage({ ctn: output, text: json.error, code: response.status, error: true });
    if (json.message) displayMessage({ ctn: output, text: json.message, code: response.status });
    if (json.data) {
      const { products } = json.data;
      output.innerHTML = "";
      displayProduits(output, products);
    }
  };
  btn.addEventListener("mousedown", handleMouseDown);
};
/**
 *
 * Setup the read one logic
 *
 * Fetch one product by id
 * Display it in a table (displayProduits)
 * Display an error message if there is an error
 * Display a success message if the fetch is successful
 * Display a message if the id is missing
 *
 */
const getProduit = () => {
  console.log("Setting up read one logic...");
  const { btn, input, output, section } = dom.readOne;
  btn.addEventListener("mousedown", async (e) => {
    e.preventDefault();
    console.log("Fetching data...");
    const id = input.value;
    if (!id) {
      displayMessage({ ctn: output, text: "L'id est obligatoire" });
      return;
    }
    const [json, response] = await APIFetch.fetchReadOne({ id });
    console.log(response.status, json);
    if (json.error) displayMessage({ ctn: output, text: json.error, code: response.status, error: true });
    if (json.message) displayMessage({ ctn: output, text: json.message, code: response.status });
    if (json.data) {
      const { product } = json.data;
      // displayProduits expects an array of products
      displayProduits(output, [product]);
    }
  });
};
/**
 *
 * Setup the delete logic
 *
 * Delete one product by id
 * Display an error message if there is an error
 * Display a success message if the fetch is successful
 * Display the error data if there is an error
 */
const deleteProduit = () => {
  console.log("Setting up delete logic...");
  const { btn, input, outputError, outputData, outputMessage } = dom.deleteOne;
  btn.addEventListener("mousedown", async (e) => {
    e.preventDefault();
    console.log("Deleting data...");
    const id = Math.floor(parseInt(input.value));
    const clientData = { id };
    const [json, response] = await APIFetch.fetchDeleteOne(clientData);
    console.log(json);
    console.log(response);
    if (json.error) displayMessage({ ctn: outputError, text: json.error, code: response.status, error: true });
    if (json.message) displayMessage({ ctn: outputMessage, text: json.message, code: response.status });
    if (json.data) outputData.innerHTML += `<pre>${JSON.stringify(json.data, null, 2)}</pre>`;
  });
};
/**
 *
 * Setup the create logic
 *
 * Create one product
 * Display an error message if there is an error
 * Display a success message if the fetch is successful
 * Display the error data if there is an error
 * Display the created id if the fetch is successful
 */
const createProduit = () => {
  console.log("Setting up create logic...");
  const { btn, inputs, output, outputData, outputError, outputMessage } = dom.create;
  btn.addEventListener("mousedown", async (e) => {
    e.preventDefault();
    console.log("Creating data...");
    const clientData = inputs.reduce((acc, input) => {
      acc[input.name] = input.value;
      if (input.name === "prix") acc[input.name] = parseFloat(input.value);
      return acc;
    }, {});
    const [json, response] = await APIFetch.fetchCreateOne(clientData);
    console.log(json.data);
    if (json.error) displayMessage({ ctn: outputError, text: json.error, code: response.status, error: true });
    if (json.message) displayMessage({ ctn: outputMessage, text: json.message, code: response.status });
    if (json.data) outputData.innerHTML += `<pre>${JSON.stringify(json.data, null, 2)}</pre>`;
  });
};
/**
 *
 * Setup the update logic
 *
 * Fetch all products
 * Display all ids as buttons in the update section to choose which one to update
 * Create an id input to store the current id
 * Fill the update section inputs with the product data
 * Send the update request
 * Display an error message if there is an error
 * Display a success message if the fetch is successful
 * Display the error data if the fetch is successful
 *
 */
const updateProduit = async () => {
  // we fetch all products and display all ids as buttons in the update section to choose which one to update
  const { idsCtn, inputs, btn: submitButton, outputError, outputMessage } = dom.update;
  console.log(outputError, outputMessage);
  const [json, response] = await APIFetch.fetchReadAll();
  if (response.ok === false) {
    displayMessage({ ctn: outputError, text: json.error, code: response.status, error: true });
    displayMessage({ ctn: outputMessage, text: json.message, code: response.status });
    return;
  }

  const ids = json.data.products.map(({ id }) => id);
  const buttons = insertIdsUpdate(idsCtn, ids);
  // we add an event listener to each button to fetch the product data and fill the inputs
  buttons.forEach((btn) => {
    btn.addEventListener("mousedown", async () => {
      const id = btn.dataset.updateIds;
      const [json] = await APIFetch.fetchReadOne({ id });
      if (json.data) {
        const { product } = json.data;
        // create an id input to store the current id
        const idInput = document.createElement("input");
        // idInput.type = "hidden";
        idInput.name = "id";
        idInput.disabled = true;
        idInput.value = product.id;
        inputs.push(idInput);
        // append the id input to the form ( will be used to send the update request and is hidden )
        dom.update.section.appendChild(idInput);
        // fill the update section inputs with the product data
        inputs.forEach((input) => {
          input.value = product[input.name];
        });
      }
    });
  });
  // we add an event listener to the submit button to send the update request
  submitButton.addEventListener("mousedown", async (e) => {
    e.preventDefault();
    console.log("Updating data...");
    const clientData = inputs.reduce((acc, input) => {
      acc[input.name] = input.value;
      if (input.name === "prix") acc[input.name] = parseFloat(input.value);
      if (input.name === "id") acc[input.name] = parseInt(input.value);
      return acc;
    }, {});
    const [json, response] = await APIFetch.fetchUpdateOne(clientData);
    console.log(json);
    if (json.error) displayMessage({ ctn: dom.update.output, text: json.error, code: response.status, error: true });
    if (json.message) displayMessage({ ctn: dom.update.output, text: json.message, code: response.status });
    if (json.data) dom.update.output.innerHTML += `<pre>${JSON.stringify(json.data, null, 2)}</pre>`;
  });
};
/**
 *
 * Run the app
 *
 */
const run = () => {
  themeSetup();
  getProduits();
  getProduit();
  deleteProduit();
  createProduit();
  updateProduit();
};
run();
