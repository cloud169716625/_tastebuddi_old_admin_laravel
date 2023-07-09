<?php
namespace App\Services;

use JWTAuth;
use GooglePlaces;

class GoogleLocationService
{
    public function searchLocation($keyword, $country, string $city = null)
    {
        if (filled($city)) {
            $keyword = "{$keyword} in {$city}";
        }

        $params = ['types' => 'geocode|establishment', 'components' => "country:{$country}"];

        $response = GooglePlaces::placeAutocomplete($keyword, $params, $offset = [3]);

        $places = $response->get('predictions');

        $locations = array();
        $index = 0;
        foreach($places as $place)
        {
            if (in_array('country', $place['types'])) continue;
            if (in_array('natural_feature', $place['types'])) continue;

            $place_id = $place['place_id'];
            $location = $place['structured_formatting']['main_text'];

            $locations[$index]['place_id'] = $place['place_id'];
            $locations[$index]['location'] = $place['structured_formatting']['main_text'];
            $locations[$index]['address'] = $place['structured_formatting']['secondary_text'];
            $index++;
        }

        return $locations;
    }
    
    public function placeDetails($place_id)
    {
        $response = GooglePlaces::placeDetails($place_id,  $params = ['fields' => 'address_components,formatted_address,geometry,name,rating,url,user_ratings_total']);
        $details = $response->get('result');

        $name = $details['name'];
        $address = $details['formatted_address'];
        $latitude = $details['geometry']['location']['lat'];
        $longitude = $details['geometry']['location']['lng']; 
        $url = $details['url'];

        $rating = collect($details)->has('rating') ? $details['rating'] : 0;
        $reviews = $details['user_ratings_total'] ?? 0;

        $city=null;
        foreach ($details['address_components'] as $component) {
            if (in_array('locality', $component['types'])) {
                $city = $component['long_name'];
            }

            if (in_array('country', $component['types'])) {
                $country = $component['long_name'];
            }

            if($city==null) {
                if (in_array('administrative_area_level_1', $component['types'])) {
                    $city = $component['long_name'];
                }
            }
        }

        $data = [
            'place_id' => $place_id,
            'name' => $name,
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'city' => $city,
            'country' => $country,
            'rating' => $rating,
            'reviews' => $reviews,
            'url' => $url
        ];       

        return $data;
    }
}