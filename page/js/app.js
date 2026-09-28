const form = document.getElementById("userForm");

const fields = [
    {id:"first_name", message:"Please enter First Name"},
    {id:"last_name", message:"Please enter Last Name"},
    {id:"street_number", message:"Please enter Street/Number"},
    {id:"city", message:"Please enter City"},
    {id:"country", message:"Please enter Country"}
];
// Going through each field on the form, create an error message and clear the error message when the user enters something
fields.forEach(function (field){
    const input = document.getElementById(field.id);
    const error = document.createElement("span");

    error.id = field.id + "_error";
    error.className = "error-message";
    error.hidden = true;
    
    input.before(error);

    input.addEventListener("input", function() {
        if(input.value.trim()!==""){
            input.classList.remove("input-error");
            error.textContent = "";
            error.hidden = true;
        }
    });
});

//Handles the form submition, async awaits the network respones.
form.addEventListener("submit", async function (event) {
    event.preventDefault();

    const button = form.querySelector('button[type="submit"]');

    if (button.disabled) {
        return;
    }

    let firstInvalidInput = null;
    //Checking for empty boxes
    fields.forEach(function (field) {
        const input = document.getElementById(field.id);
        const error = document.getElementById(field.id + "_error");
        const isEmpty = input.value.trim() === "";

        input.classList.toggle("input-error", isEmpty);
        error.textContent = isEmpty ? field.message : "";
        error.hidden = !isEmpty;

        if (isEmpty && firstInvalidInput === null) {
            firstInvalidInput = input;
        }
    });
    //Focuses the input on the first empty box
    if (firstInvalidInput !== null) {
        firstInvalidInput.focus();
        return;
    }
    //Taking form data for later checks in try/catch and saving it
    const formData = new FormData(form);

    const address = [
        formData.get("street_number").trim(),
        formData.get("city").trim(),
        formData.get("country").trim()
    ].join(", ");
    //dissabling the button so we dont spam click it while submiting the form
    button.disabled = true;

    try {
        //Adding try and catch to check if the adress is located and displayed, if it is not we arent saving anything to database
        try {
            await showAddress(address);
        } catch (error) {
            if (error.code === "ADDRESS_NOT_FOUND") {
                const addressFields = ["street_number", "city", "country"];

                addressFields.forEach(function (id) {
                    const input = document.getElementById(id);
                    const message = document.getElementById(id + "_error");

                    input.classList.add("input-error");
                    message.textContent = "Please check this address field";
                    message.hidden = false;
                });

                document.getElementById("street_number").focus();
            } else {
                alert(
                    "The address lookup could not be completed. Nothing has been saved."
                );
            }

            return;
        }
            //We POST data from the form to the server
            const response = await fetch("save-user.php", {
                method: "POST",
                body: formData
            });
            //We take the server respone and convert it into JS object
            const result = await response.json();

        //Checking response for errorrs and error codes
        if (response.status === 422 && result.errors) {
            fields.forEach(function (field) {
                const message = result.errors[field.id];

                if (!message) {
                    return;
                }

                const input = document.getElementById(field.id);
                const error = document.getElementById(field.id + "_error");

                input.classList.add("input-error");
                error.textContent = message;
                error.hidden = false;

                if (firstInvalidInput === null) {
                    firstInvalidInput = input;
                }
            });

            if (firstInvalidInput !== null) {
                firstInvalidInput.focus();
            }

            return;
        }

        if (!response.ok || result.saved !== true) {
            throw new Error(result.message || "Unable to save the user.");
        }
        //resets form and loads users
        form.reset();
        await loadUsers();
    } catch (error) {
        alert(
            "The save could not be confirmed. Check the database before trying again."
        );
    } finally {
        button.disabled = false;
    }
});
