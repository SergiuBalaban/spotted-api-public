<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait CoordinationTrait
{
    public function getUrl(float|int $lat, float|int $lng): string
    {
        $apiKey = env('GOOGLE_API_KEY');

        return "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$apiKey}&language=en&result_type=street_address";
    }

    /**
     * @param  array<string, string>  $data
     * @return array<string, string>
     */
    public function parseAddress(array $data): array
    {
        if (! $data['address_line']) {
            return $data;
        }
        $apiKey = env('GOOGLE_API_KEY');
        $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($data['address_line']).'&sensor=false&key='.$apiKey);
        $geo = $geo ? json_decode($geo, true) : [];

        if (isset($geo['status']) && ($geo['status'] == 'OK')) {
            $data['latitude'] = $geo['results'][0]['geometry']['location']['lat'];
            $data['longitude'] = $geo['results'][0]['geometry']['location']['lng'];
        }

        return $data;
    }

    /**
     * @param  array<string, string>  $data
     * @return array<string, string>
     */
    public function parseCoordinates(array $data): array
    {
        if (! isset($data['address_line']) || ! empty($data['address_line']) && ! empty($data['country'])) {
            return $data;
        }
        if (! isset($data['latitude'])) {
            $data = $this->parseAddress($data);
        }
        $lat = (float) $data['latitude'];
        $lng = (float) $data['longitude'];
        $url = $this->getUrl($lat, $lng);
        $response = Http::get($url);

        if ($response->json() && $response->json()['status'] === 'OK' && count($response->json()['results']) > 0) {
            if (isset($response->json()['results'][0])) {
                $data['formatted_address'] = isset($response->json()['results'][0]['formatted_address']) ? $response->json()['results'][0]['formatted_address'] : '';
                $jsonAddressComponent = $response->json()['results'][0]['address_components'];
                if ($jsonAddressComponent) {
                    $data['address_line'] = null;
                    $data['city'] = null;
                    $data['country'] = null;
                    $data['postal_code'] = null;
                    $data['town'] = null;
                    foreach ($jsonAddressComponent as $component) {
                        if (in_array('street_number', $component['types'])) {
                            $data['address_line'] .= $component['long_name'].' ';
                        }
                        if (in_array('route', $component['types'])) {
                            $data['address_line'] .= $component['long_name'];
                        }
                        if (in_array('locality', $component['types'])) {
                            $data['city'] = $component['long_name'];
                        }
                        if (in_array('postal_town', $component['types'])) {
                            $data['town'] = $component['long_name'];
                        }
                        if (in_array('administrative_area_level_2', $component['types'])) {
                            $data['county'] = $component['long_name'];
                        }
                        if (in_array('administrative_area_level_1', $component['types'])) {
                            $data['state_region'] = $component['long_name'];
                        }
                        if (in_array('country', $component['types'])) {
                            $data['country'] = $component['long_name'];
                        }
                        if (in_array('postal_code', $component['types'])) {
                            $data['postal_code'] .= $component['long_name'];
                        }
                    }
                }
            }
        }
        $data['dms_location'] = $this->DECtoDMS($lat, $lng);

        return $data;
    }

    public function getCountryByLatLng(float|int $lat, float|int $lng): string
    {
        $url = $this->getUrl($lat, $lng);
        $response = Http::get($url);
        if ($response->json() && $response->json()['status'] === 'OK' && count($response->json()['results']) > 0) {
            return $response->json()['results'][0]['address_components'][0]['short_name'];
        }

        return '';
    }

    public function DECtoDMS(float|int $latitude, float|int $longitude): string
    {
        $latitudeDirection = $latitude < 0 ? 'S' : 'N';
        $longitudeDirection = $longitude < 0 ? 'W' : 'E';

        $latitudeNotation = $latitude < 0 ? '-' : '';
        $longitudeNotation = $longitude < 0 ? '-' : '';

        $latitudeInDegrees = floor(abs($latitude));
        $longitudeInDegrees = floor(abs($longitude));

        $latitudeDecimal = abs($latitude) - $latitudeInDegrees;
        $longitudeDecimal = abs($longitude) - $longitudeInDegrees;

        $_precision = 3;
        $latitudeMinutes = round($latitudeDecimal * 60, $_precision);
        $longitudeMinutes = round($longitudeDecimal * 60, $_precision);

        return sprintf('%s%s° %s %s %s%s° %s %s',
            $latitudeNotation,
            $latitudeInDegrees,
            $latitudeMinutes,
            $latitudeDirection,
            $longitudeNotation,
            $longitudeInDegrees,
            $longitudeMinutes,
            $longitudeDirection
        );
    }
}
