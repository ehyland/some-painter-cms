<?php
class Location extends DataObject{

    private static $db = array(
        'StreetNumber' => 'Varchar(255)',
        'Route' => 'Varchar(255)',
        'Locality' => 'Varchar(255)',
        'State' => 'Varchar(255)',
        'Country' => 'Varchar(255)',
        'PostalCode' => 'Varchar(255)',
        'Latitude' => 'Varchar(255)',
        'Longitude' => 'Varchar(255)',
        'LocationType' => 'Varchar(255)',
        'PlaceID' => 'Varchar(255)',
        'PartialMatch' => 'Boolean',
        'Types' => 'Varchar(255)'
    );

    private static $google_component_type_map = array(
        'street_number' => 'StreetNumber',
        'route' => 'Route',
        'locality' => 'Locality',
        'administrative_area_level_1' => 'State',
        'country' => 'Country',
        'postal_code' => 'PostalCode'
    );

    public function getTitle() {
        return $this->getFormattedAddress();
    }

    function getFormattedAddress(){
        return "$this->StreetNumber $this->Route $this->Locality $this->State $this->Country $this->PostalCode";
    }

    /**
     * Create a new location object from a search string
     */
    public static function create_from_string ($queryString, $melbFocus = true, $ausFocus = true) {
        $googleData = self::get_google_location_data_from_string($queryString, $melbFocus, $ausFocus);
        if (!$googleData) return null;

        // Get 1:1 values
        $data = array(
            'Latitude' => $googleData['geometry']['location']['lat'],
            'Longitude' => $googleData['geometry']['location']['lng'],
            'LocationType' => $googleData['geometry']['location_type'],
            'PlaceID' => $googleData['place_id'],
            'PartialMatch' => (array_key_exists('partial_match', $googleData) && $googleData['partial_match']),
            'Types' => implode(',', $googleData['types'])
        );

        // Extract data from components
        foreach ($googleData['address_components'] as $component) {
            foreach ($component['types'] as $type) {
                if (isset(self::$google_component_type_map[$type])) {
                    $data[self::$google_component_type_map[$type]] = $component['short_name'];
                }
            }
        }

        // Create location object
        return self::create()->update($data);
    }

    /**
     * Fetch location data using google geocoding api
     */
    public static function get_google_location_data_from_string ($queryString, $melbFocus = true, $ausFocus = true) {
        $queryString = trim($queryString);

        // Confirm address supplied
        if (!is_string($queryString) || !strlen($queryString)) {
            return null;
        }

        // Add focus address to melbourne australia
        if ($ausFocus && stripos($queryString, 'australia') === FALSE) {
            if ($melbFocus && stripos($queryString, 'melbourne') === FALSE) {
                $queryString .= ' Melbourne';
            }
            $queryString .= ' Australia';
        }

        return GoogleGeocodingUtil::create()->get($queryString);
    }

    /**
     * Return data for json consumption
     */
    public function forAPI(){
        return $this->getBaseAPIFields(array(
            'LastEdited',
            'Created',
            'PlaceID',
            'PartialMatch'
        ));
    }
}
