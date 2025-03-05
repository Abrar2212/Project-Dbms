<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="home.css">
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
    <script src="map.js"></script>
</head>

<body>

    <?php
    include('searchAndnav.php');  // Mahbub 
    ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Add New House</h1>
        <form action="process_form.php" method="POST" enctype="multipart/form-data">
            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label">House Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter house name" required>
            </div>
            <!-- Available FROM -->
            <div class="mb-3">
                <label for="available" class="form-label">Available From</label>
                <input type="text" class="form-control" id="available" name="available" placeholder="Enter available date" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter house description" required></textarea>
            </div>

            <!-- Main Image -->
            <div class="mb-3">
                <label for="main_image" class="form-label">Main Image</label>
                <input type="file" class="form-control" id="main_image" name="main_image" required>
            </div>

            <!-- Small Images -->
            <div class="mb-3">
                <label for="small_image_1" class="form-label">Small Images</label>
                <input type="file" class="form-control" id="small_image_1" name="small_image_1[]" multiple required>
            </div>

            <!-- Bedrooms -->
            <div class="mb-3">
                <label for="bedrooms" class="form-label">Bedrooms</label>
                <input type="number" class="form-control" id="bedrooms" name="bedrooms" placeholder="Enter number of bedrooms" required>
            </div>

            <!-- Bathrooms -->
            <div class="mb-3">
                <label for="bathrooms" class="form-label">Bathrooms</label>
                <input type="number" class="form-control" id="bathrooms" name="bathrooms" placeholder="Enter number of bathrooms" required>
            </div>

            <!-- Service Charge -->
            <div class="mb-3">
                <label for="service_charge" class="form-label">Service Charge</label>
                <input type="text" class="form-control" id="service_charge" name="service_charge" placeholder="Enter service charge" required>
            </div>

            <!-- Floors -->
            <div class="mb-3">
                <label for="floors" class="form-label">Floors</label>
                <input type="number" class="form-control" id="floors" name="floors" placeholder="Enter number of floors" required>
            </div>

            <!-- Restrictions -->
            <div class="mb-3">
                <label for="restrictions" class="form-label">Restrictions</label>
                <input type="text" class="form-control" id="restrictions" name="restrictions" placeholder="Enter any restrictions" required>
            </div>

            <!-- Essentials -->
            <div class="mb-3">
                <label for="essential" class="form-label">Essentials</label>
                <input type="text" class="form-control" id="essential" name="essential" placeholder="Enter essential items" required>
            </div>

            <!-- Features -->
            <!-- Garage -->
            <div class="mb-3">
                <label for="garage" class="form-label">Garage</label>
                <select class="form-control" id="garage" name="garage" required>
                    <option value="" disabled selected>Select</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="feature" class="form-label">Features</label>
                <input type="text" class="form-control" id="feature" name="feature" placeholder="Enter any additional features" required>
            </div>

            <div class="form-group">
                <label for="gas">Gas:</label>
                <select class="form-control" id="gas" name="gas" required>
                    <option value="" disabled selected>Select</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="wifi">Wi-Fi:</label>
                <select class="form-control" id="wifi" name="wifi" required>
                    <option value="" disabled selected>Select</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="cctv">CCTV:</label>
                <select class="form-control" id="cctv" name="cctv" required>
                    <option value="" disabled selected>Select</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <!-- Renter Type -->
            <div class="mb-3">
                <label for="renter_type" class="form-label">Renter Type</label>
                <select class="form-control" id="renter_type" name="renter_type" required>
                    <option value="" disabled selected>Select</option>
                    <option value="1">Family</option>
                    <option value="2">Bachelor</option>
                    <option value="3">Office</option>
                    <option value="4">All</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="rent" class="form-label">Rent</label>
                <input type="text" class="form-control" id="rent" name="rent" placeholder="Enter rent amount" required>
            </div>

            <!-- City Field -->
            <div class="mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="Enter city name" required>
            </div>

            <!-- Address Field -->
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Enter address" required>
            </div>

            <button type="button" class="btn btn-secondary btn-success mb-3" onclick="searchLocation()">Search Location</button>

            <!-- Location Coordinates -->
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">

            <div id="map"></div>
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-success" style="margin-bottom: 10px; margin-top:10px">Submit</button>
        </form>
    </div>

    <?php
    include('footer.php');  // Mahbub 
    ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
