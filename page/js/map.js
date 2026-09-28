let map = null;
let geocoder = null;
//This will hold Google's marker constructor.
let AdvancedMarker = null;

//Google calls this global function through callback=initMap in index.php.
window.initMap = async function () {
    try {
        // Load the tools for drawing maps, adding pins, and looking up adresses.
        const mapsLibrary = await google.maps.importLibrary("maps");
        const markerLibrary = await google.maps.importLibrary("marker");
        const geocodingLibrary = await google.maps.importLibrary("geocoding");

        //Save the constructor so showAddress can create pins later.
        AdvancedMarker = markerLibrary.AdvancedMarkerElement;

        //Create the map inside the existing div with id="map".
        map = new mapsLibrary.Map(document.getElementById("map"), {
            center: { lat: 45.8081, lng: 9.0852 },
            zoom: 10,
            mapId: "DEMO_MAP_ID"
        });

        //Create the tool that requests coordinates for a written address.
        geocoder = new geocodingLibrary.Geocoder();
    } catch {
        map = null;
        geocoder = null;
        document.getElementById("map").textContent =
            "Unable to load the map. Please refresh the page to try again.";
    }
};

//app.js awaits this function before it attempts to save the submitted user.
async function showAddress(address) {
    if (map === null || geocoder === null) {
        throw new Error("The map is not ready. Please wait or refresh the page.");
    }

    //Send the combined street, city, and country to Google and wait for its result.
    const response = await geocoder.geocode({
        address: address,
        fulfillOnZeroResults: true
    });

    if (response.results.length === 0) {
        const error = new Error("Address not found. Please check the address fields.");
        error.code = "ADDRESS_NOT_FOUND";
        throw error;
    }

    //Use the first match.
    const result = response.results[0];


    //Add a new pin without removing previous ones; these pins last until the page reloads.
    new AdvancedMarker({
    map: map,
    //Use the coordinates returned by Google.
    position: result.geometry.location,
    title: result.formatted_address
    });

    //Move to show result area.
    map.fitBounds(result.geometry.viewport);
}
