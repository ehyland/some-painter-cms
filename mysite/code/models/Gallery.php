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

        // galleries field
        $locations = function() {
            return Location::get()->map('ID', 'Title');
        };
        $locationField = DropdownField::create('LocationID', 'Location', $locations());
        $locationField->useAddNew('Location', $locations);
        if ($this->LocationID) {
            $locationField->setDescription($this->Location()->getDataAdminEditAnchorTag());
        }

        // add fields to tab
        $fields->addFieldsToTab('Root.Main', array(
            $locationField
        ));

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
