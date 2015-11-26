<?php
class Location extends DataObject{

    private static $db = array(
        'StreetNumber' => 'Varchar(255)',
        'Route' => 'Varchar(255)',
        'Suburb' => 'Varchar(255)',
        'State' => 'Varchar(255)',
        'Country' => 'Varchar(255)',
        'PostalCode' => 'Varchar(255)',
        'Latitude' => 'Varchar(255)',
        'Longitude' => 'Varchar(255)',
        'PlaceID' => 'Varchar(255)',
        'PartialMatch' => 'Boolean'
    );

    private static $belongs_to = array(
        'Gallery' => 'Gallery.Location'
    );

    public static $address_components_map = array(
        'street_number' => 'StreetNumber',
        'route' => 'Route',
        'locality' => 'Suburb',
        'administrative_area_level_1' => 'State',
        'country' => 'Country',
        'postal_code' => 'PostalCode'
    );

    public static function create_or_update_with_google_data($data){
        $locationData = $data['location'];
        $update = array();

        if (!array_key_exists('place_id', $locationData)) {
            return 0;
        }

        // Find or create location
        $location = Location::get()->filter('PlaceID', $locationData['place_id'])->first();
        if (!$location) {
            $location = Location::create();
        }

        // Add address components to update
        $componentsExist = (
            array_key_exists('address_components', $locationData) &&
            is_array($locationData['address_components'])
        );
        if ($componentsExist){
            foreach ($locationData['address_components'] as $component) {
                foreach ($component['types'] as $type) {
                    if (isset(self::$address_components_map[$type])) {
                        $field = self::$address_components_map[$type];
                        $update[$field] = $component['short_name'];
                    }
                }
            }
        }

        // Confirm match is in australia
        if (!array_key_exists('Country', $update) || $update['Country'] !== 'AU') {
            return 0;
        }

        // Add other details
        $partialMatch = (
            array_key_exists('partial_match', $locationData)
            && $locationData['partial_match']
        );
        $update['PartialMatch'] = $partialMatch;
        $update['Latitude'] = $locationData['geometry']['location']['lat'];
        $update['Longitude'] = $locationData['geometry']['location']['lng'];
        $update['PlaceID'] = $locationData['place_id'];

        $location->update($update)->write();

        return $location->ID;
    }

    function getFormattedAddress(){
        return "$this->StreetNumber $this->Route $this->Suburb $this->State $this->Country $this->PostalCode";
    }

    public function forAPI(){
        return $this->getBaseAPIFields(array(
            'ClassName',
            'LastEdited',
            'Created',
            'PlaceID',
            'PartialMatch'
        ));
    }
}
