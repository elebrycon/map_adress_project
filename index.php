<?php
//Pulling the API key from map-config.php
$config = require __DIR__ . '/config.php';
$googleMapsKey = $config['GOOGLE_MAPS_API_KEY'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Details</title>

    <link rel="stylesheet" href="page/css/style.css">
</head>

<body>

    <main class="container">
        <!--Leftside Container-->
        <section class="form-container">

            <h1>User Details</h1>

            <form id="userForm" method="POST">

                <div class="name-row">
                    <div class="field">
                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            placeholder="First Name"
                        >    
                    </div>
                    <div class="field">
                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            placeholder="Last Name"   
                        >
                    </div>
                </div>

                <input
                    type="text"
                    id="street_number"
                    name="street_number"
                    placeholder="Street / Number"
                >

                <input
                    type="text"
                    id="city"
                    name="city"
                    placeholder="City"
                >

                <input
                    type="text"
                    id="country"
                    name="country"
                    placeholder="Country"
                >

                <button type="submit">
                    Add User
                </button>

            </form>

        </section>

        <!--rightside container-->
        <section class="map-container">

            <div id="map"></div>

        </section>

    </main>
    <!--Saved users from database container-->
    <section class="users-container">
        <h2>Saved Users</h2>
        <p id="usersStatus">Loading saved users...</p>
        <div class="users-table-scroll">
            <table class="users-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Street / Number</th>
                        <th scope="col">City</th>
                        <th scope="col">Country</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody"></tbody>
            </table>
        </div>
    </section>
    <!--Connection to JS files-->
    <script src="page/js/users.js"></script>
    <script src="page/js/app.js"></script>
    <script src="page/js/map.js"></script>
    <!--Connection to Google maps api-->
    <script async src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($googleMapsKey, ENT_QUOTES, 'UTF-8') ?>&amp;loading=async&amp;callback=initMap"></script>
</body>
</html>
