const loginFormElement = document.querySelector("form[action='/login/submit']");
const signupFormElement = document.querySelector("form[action='/signup/submit']");
const formControlElement = document.querySelector("div#form-control");

console.log(formControlElement);

function handleFormSubmission(formElement, successRedirectUrl) {
  formElement.addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent default form submission

    const form = event.target;
    const formData = new FormData(form); // Collect form data
    const json = JSON.stringify(Object.fromEntries(formData)); // Convert form data to JSON

    try {
      const response = await fetch(form.action, {
        method: "POST",
        body: json,
        headers: {
          "Content-Type": "application/json",
        },
      });

      if (!response.ok) {
        formControlElement.innerHTML = "";
        const result = await response.json();
        console.log(result.payload);

        if (response.status === 400) {
          if (result.payload) {
            Object.values(result.payload).forEach((errors) => {
              errors.forEach((error) => {
                const small = document.createElement("small");
                small.textContent = error;
                small.style.display = "block";
                formControlElement.appendChild(small);
              });
            });
          }
        } else {
          formControlElement.textContent = result.message;
        }

        formControlElement.style.display = "block";
        return;
      }
      window.location.href = successRedirectUrl;
    } catch (error) {
      console.error("Submission error:", error);
      formControlElement.textContent = `An error occurred while ${
        formElement === loginFormElement ? "logging in" : "signing up"
      }.`;
      formControlElement.style.display = "block";
    }
  });
}

if (signupFormElement) handleFormSubmission(signupFormElement, "/login");
if (loginFormElement) handleFormSubmission(loginFormElement, "/");
