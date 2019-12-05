<?php
function fnGeocode($address)
{
	$address = urlencode($address);    // url encode the address - can be an address or just a zip
	$url = "https://api.locationiq.com/v1/autocomplete.php?key=d3cf11b0ab734a&q={$address}";		// geocode api url
	$resp_json = file_get_contents($url);		// get the json response
	$resp = json_decode($resp_json, true);		// decode the json
	if($resp['status'] == 'OK')	  // response status will be 'OK', if able to geocode given address 
	{
	        // get the geocode results
		$lat = $resp['lat'];
		$lon = $resp['lon'];
		$formatted_address = $resp['results'][0]['formatted_address'];
       
		if ($lat && $lon && $formatted_address)	// is complete
		{
            // put the results in an array
			$coodinates = array($lat, $lon, $formatted_address);
			return $coodinates;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function renderMap($lat, $lon)
{
    return '
    <div id="googleMap" style="width:100%;height:400px;"></div>

    <script>
    function myMap() {
    var mapProp= {
      center:new google.maps.LatLng(' . $lat . ',' . $lon . '),
      zoom:5,
    };
    var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
    }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCr_VzmwNV3eQSrUjb7H3I09OfejwqSsgY=myMap"></script>
    ';
}
?>