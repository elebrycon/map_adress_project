// Load saved users into the table.
async function loadUsers() {
    const tableBody = document.getElementById("usersTableBody");
    const status = document.getElementById("usersStatus");

    status.hidden = false;
    status.textContent = "Loading saved users...";

    // Try to load and display the records. Any errors here go to catch below.
    try {
        // We are requesting infromation from get-users.php
        const response = await fetch("get-users.php", {
            method: "GET",
            cache: "no-store"
        });
        const result = await response.json();

        // If the response throws an error, or user reasults are not an array go to catch
        if (!response.ok || !Array.isArray(result.users)) {
            throw new Error("Unable to load users.");
        }

        // Build the rows in a temporary container before putting them into the page.
        const rows = document.createDocumentFragment();
        const columns = ["id", "first_name", "last_name", "street_number", "city", "country"];

        // Repeat the following steps for every saved user returned by PHP.
        result.users.forEach(function (user) {
            // Create one table row for this user.
            const row = document.createElement("tr");

            // Create one cell for each field listed in columns.
            columns.forEach(function (column) {
                // Create an empty table cell.
                const cell = document.createElement("td");
                // Read this user's field and display it as text.
                cell.textContent = user[column];
                // Add the completed cell to this user's row.
                row.appendChild(cell);
            });

            // Add the completed user row to our temporary collection.
            rows.appendChild(row);
        });

        // Remove the previous table rows and insert the freshly built rows without duplicates.
        tableBody.replaceChildren(rows);
        status.textContent = result.users.length === 0 ? "No users saved yet." : "";
        status.hidden = result.users.length > 0;
    } catch (error) {
        status.textContent = "Unable to refresh saved users. Refresh the page to try again.";
    }
}

loadUsers();
