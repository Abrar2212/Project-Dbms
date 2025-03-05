
let map, marker;

// Initialize the map
function initMap(lat = 23.8103, lng = 90.4125) { // Default to Dhaka, Bangladesh
    map = L.map('map').setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    // Update coordinates when the marker is dragged
    marker.on('dragend', () => {
        const position = marker.getLatLng();
        updateLocationInput(position.lat, position.lng);
    });
}

// Search location using OpenStreetMap Nominatim API
async function searchLocation() {
    const address = document.getElementById('address').value.trim();
    if (!address) {
        alert("Please enter an address or location.");
        return;
    }

    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`);
        if (!response.ok) {
            throw new Error("Unable to fetch location data. Please try again.");
        }

        const data = await response.json();

        // Handle empty results
        if (!Array.isArray(data) || data.length === 0) {
            alert("Location not found. Please try another address.");
            return;
        }

        const { lat, lon } = data[0];
        map.setView([lat, lon], 13);
        marker.setLatLng([lat, lon]);
        updateLocationInput(lat, lon);

    } catch (error) {
        console.error("Error:", error.message);
        alert("An unexpected error occurred while searching for the location. Please try again.");
    }
}

// Update the latitude and longitude hidden fields
function updateLocationInput(lat, lon) {
    console.log(`Updating location: ${lat}, ${lon}`);
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lon;
}


// Initialize the map on page load
window.onload = () => {
    initMap();
};


