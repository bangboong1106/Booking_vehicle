<?php

namespace App\Common;

use Exception;
use GuzzleHttp\Client;

class GoogleConstant
{
    /** @var string */
    private $apiDistanceUrl = 'https://maps.googleapis.com/maps/api/distancematrix/json';

    private $apiGeoCodeUrl = "https://maps.googleapis.com/maps/api/geocode/";

    /** @var */
    private $apiKey;

    /** @var */
    private $origins;

    /** @var */
    private $destinations;

    /**
     * DistanceApi constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Get API_KEY.
     *
     * @return mixed
     */
    public function getApiKey()
    {
        if (empty($this->apiKey)) {
            $this->apiKey = env('GOOGLE_MAP_API_KEY', '');
        }
        return $this->apiKey;
    }

    /**
     * Get origins.
     *
     * @return mixed
     */
    public function getOrigins()
    {
        return $this->origins;
    }


    /**
     * @param $origins
     * @return $this
     */
    public function setOrigins($origins)
    {
        $this->origins = $origins;

        return $this;
    }

    /**
     * Get destinations.
     *
     * @return mixed
     */
    public function getDestinations()
    {
        return $this->destinations;
    }


    /**
     * @param $destinations
     * @return $this
     */
    public function setDestinations($destinations)
    {
        $this->destinations = $destinations;

        return $this;
    }

    /**
     * Caculate distance from origins to destinations.
     *
     * @param $origins
     * @param $destinations
     *
     * @return int
     */
    public function calculateDistance($origins, $destinations): int
    {
        $client = new Client();

        try {
            $response = $client->get($this->apiDistanceUrl, [
                'query' => [
                    'units' => 'metric',
                    'origins' => $origins,
                    'destinations' => $destinations,
                    'key' => $this->getApiKey(),
                    'random' => random_int(1, 100),
                ],
                'verify' => false
            ]);

            $statusCode = $response->getStatusCode();

            if (200 === $statusCode) {
                $responseData = json_decode($response->getBody()->getContents());

                if (isset($responseData->rows[0]->elements[0]->distance)) {
                    return $responseData->rows[0]->elements[0]->distance->value;
                }
            }

            return -1;
        } catch (Exception $e) {
            logError($e);
            return -1;
        }
    }

    /**
     * makes request to google maps api to fetch coordinates of location
     * @param  String $location - location to be converted to coordinates
     * @return array            - longitude and latitude of location
     */
    public function getCoordinates($location)
    {
        $queryString = $this->constructQueryString($location, "geocoding");
        $client = new Client();
        $response = $client->get($queryString);
        $results = json_decode($response->getBody(), true, 512);

        if (isset($results['error_message'])) {
            return null;
        }
        if (isset($results['status']) && $results['status'] == 'ZERO_RESULTS') {
            return null;
        }

        return $results['results'][0]['geometry']['location'];
    }

    /**
     * makes request to googlemaps api to fetch address of
     * coordinates using reverse geocoding
     * @param  float $longitude - longitudinal coordinate
     * @param  float $latitude - latitudinal coordinate
     * @return String             - plain address of coordinates
     */
    public function getAddress($longitude, $latitude)
    {
        $queryString = $this->constructQueryString([
            'lng' => $longitude,
            'lat' => $latitude
        ], "reverse_geocoding");

        $client = new Client();
        $response = $client->get($queryString);
        $results = json_decode($response->getBody(), true, 512);

        if (isset($results['error_message'])) {
            return null;
        }

        return $results['results'][0]['formatted_address'];
    }


    /**
     * fetches the distance between to locations (points)
     * @param  array $point1 - coordinates of first location
     * @param  array $point2 - coordinates of second location
     * @param  string $unit - unit of location (km/mi/nmi)
     * @param  integer $decimals - precision
     * @return string             - distance
     */
    public function getDistanceBetween($point1, $point2, $unit = 'm', $decimals = 2)
    {
        // Calculate the distance in degrees using Hervasine formula
        $degrees = $this->calcDistanceWithHervasine($point1, $point2);

        // Convert the distance in degrees to the chosen unit (kilometres, miles or nautical miles)
        switch ($unit) {
            case 'km':
                // 1 degree = 111.13384 km, based on the average diameter of the Earth (12,735 km)
                $distance = $degrees * 111.13384;
                break;
            case 'mi':
                // 1 degree = 69.05482 miles, based on the average diameter of the Earth (7,913.1 miles)
                $distance = $degrees * 69.05482;
                break;
            case 'nmi':
                // 1 degree = 59.97662 nautic miles, based on the average diameter of the Earth (6,876.3 nautical miles)
                $distance = $degrees * 59.97662;
                break;
            case 'm':
                // 1 degree = 111.13384 km, based on the average diameter of the Earth (12,735 km)
                $distance = $degrees * 111.13384 * 1000;

        }

        return round($distance, $decimals);
    }

    /**
     * calculates the distance between two points using
     * Haversine formula
     * @param  float $point1 - coordinates of first point
     * @param  float $point2 - coordinates of second point
     * @return float          - distance between both points
     */
    private function calcDistanceWithHervasine($point1, $point2)
    {
        return rad2deg(acos((sin(deg2rad($point1['lat'])) *
                sin(deg2rad($point2['lat']))) +
            (cos(deg2rad($point1['lat'])) *
                cos(deg2rad($point2['lat'])) *
                cos(deg2rad($point1['lng'] - $point2['lng'])))));
    }

    /**
     * constructs the query string used in the google api request
     * @param  string/array $locationOrCoordinates  - location or coordinates
     * @param  string $type - geocoding request or a reverse geocoding request
     * @return string
     */
    private function constructQueryString($locationOrCoordinates, $type)
    {
        switch ($type) {
            case "geocoding":
                $queryString = 'json?address=' . urlencode($locationOrCoordinates) . '&key=' . $this->getApiKey();
                break;
            case "reverse_geocoding":
                $lat = $locationOrCoordinates['lat'];
                $lng = $locationOrCoordinates['lng'];
                $queryString = 'json?latlng=' . $lat . ',' . $lng . '&sensor=true&key=' . $this->getApiKey();
        }

        return $queryString;
    }

    public function convertDMSToLatLong($DMS)
    {
        try {
            $rexp = '/^(\-?\d+(?:\.\d+)?)(?:\D+(\d+)\D+(\d+)([NS]?))?[^\d\-]+(\-?\d+(?:\.\d+)?)(?:\D+(\d+)\D+(\d+)([EW]?))?$/i';
            preg_match($rexp, $DMS, $matches);

            $latitude = AppConstant::DMS2Decimal((int)$matches[1], (int)$matches[2], (int)$matches[3]);
            $longitude = AppConstant::DMS2Decimal((int)$matches[5], (int)$matches[6], (int)$matches[7]);

            return [
                'latitude' => $latitude,
                'longitude' => $longitude
            ];
        } catch (Exception $e) {
            logError($e);
            return [];
        }
    }
}