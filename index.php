<?php

require 'vendor/autoload.php';

// Setup geocoder.
$adapter = new \Geocoder\HttpAdapter\CurlHttpAdapter();
$chain = new \Geocoder\Provider\ChainProvider(
    array(
        new \Geocoder\Provider\GoogleMapsProvider($adapter),
    )
);
$geocoder = new \Geocoder\Geocoder();
$geocoder->registerProvider($chain);

// Demo locations
$locations = array(
    array(
        'address' => '3324 N California Ave, Chicago, IL, 60618',
        'title' => 'Hot Dougs',
    ),
    array(
        'address' => '11 S White, Frankfort, IL, 60423',
        'title' => 'Museum',
    ),
    array(
        'address' => '1000 Sterling Ave, , Flossmoor, IL, 60422',
        'title' => 'Library',
    ),
    array(
        'address' => '2053 Ridge Rd, Homewood, IL, 60430',
        'title' => 'Twisted Q',
    )
);

$mapdata = $marker_group = array();

foreach ($locations as $key => $value) {
    // Try to geocode.
    try {
        $geocode = $geocoder->geocode($value['address']);
        $longitude = $geocode->getLongitude();
        $latitude = $geocode->getLatitude();

        // Create map data array
        $mapdata[] = markerCreator($latitude, $longitude, $value['title'], $key);

        // Marker grouping array
        $marker_group[] = "marker{$key}";

    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function markerCreator($lat, $long, $label, $key) {
    return "var marker{$key} = L.marker([{$lat}, {$long}]).addTo(map);
    marker{$key}.bindPopup(\"{$label}\");";
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>A simple map for with Geocoder PHP and Leaflet.js</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.leafletjs.com/leaflet-0.6.4/leaflet.css" />
    <script src="//cdn.leafletjs.com/leaflet-0.6.4/leaflet.js"></script>
    <style>
        #map {
            width: 800px;
            height: 400px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="row">

        <div class="col-lg-12 page-header"><h1 id="header">A simple map with Geocoder PHP and Leaflet.js</h1></div>
        <div class="row-fluid">
            <div class="col-lg-8">

                <div id="map"></div>

            </div>
        </div>

    </div>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script>
    var map = L.map('map');

    L.tileLayer('//{s}.tile.cloudmade.com/41339be4c5064686b781a5a00678de62/998/256/{z}/{x}/{y}.png', {
        maxZoom: 18
    }).addTo(map);

    <?php print implode('', $mapdata); ?>

    var group = new L.featureGroup([<?php print implode(', ', $marker_group); ?>]);
    map.fitBounds(group.getBounds());
</script>
</body>

</html>