<?php

namespace App\Services;

class GeoLocation
{
    /**
     * Compute the distance between 2 coordinates in miles
     *
     * @param array $coordinateOne
     * @param array $coordinateTwo
     *
     * @return double
     */
    public function computeDistanceInMiles(array $coordinateOne, array $coordinateTwo)
    {
        $long1 = deg2rad($coordinateOne['lng']);
        $long2 = deg2rad($coordinateTwo['lng']);
        $lat1 = deg2rad($coordinateOne['lat']);
        $lat2 = deg2rad($coordinateTwo['lat']);

        //Haversine Formula
        $dlong = $long2 - $long1;
        $dlati = $lat2 - $lat1;

        $val = pow(sin($dlati/2), 2)+cos($lat1)*cos($lat2)*pow(sin($dlong/2), 2);

        $res = 2 * asin(sqrt($val));

        $radius = 3958.756;

        return ($res*$radius);
    }

    /**
     * Compute distance in kilometers
     * @param array $coordinateOne
     * @param array $coordinateTwo
     *
     * @return double
     */
    public function computeDistanceInKilometers(array $coordinateOne, array $coordinateTwo)
    {
        $distance = $this->computeDistanceInMiles($coordinateOne, $coordinateTwo);

        return (1.609344 * $distance);
    }
}
