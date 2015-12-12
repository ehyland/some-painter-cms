<?php
class Gallery extends DataObject{
    private static $db = array(
        "Title" => "Varchar(255)",
        "Subtitle" => "Varchar(255)",
        "WebsiteURL" => "Varchar(255)"
    );

    private static $has_one = array(
        "Location" => "Location"
    );

    private static $has_many = array(
        "Events" => "Event.Gallery"
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        // Add edit location link
        if ($this->LocationID) {
            $fields->dataFieldByName('LocationID')
                ->setDescription($this->Location()->getDataAdminEditAnchorTag());
        }

        return $fields;
    }

    public function forAPI(){
        $data = $this->getBaseAPIFields(array(
            'Events',
            'LocationID'
        ));
        return $data;
    }
}
