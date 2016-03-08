<?php
class NewEventForm extends Form {
    public function __construct($controller, $name) {
        $fields = new FieldList(array(
            TextField::create('UserSubmittedGallery'),
            TextField::create('UserSubmittedArtistName'),
            DatetimeField::create('UserSubmittedStartDate'),
            CheckboxField::create('UserSubmittedHasFreeDrinks')
        ));
        $actions = new FieldList();
        $validator = new RequiredFields(array(
            'UserSubmittedGallery',
            'UserSubmittedArtistName',
            'UserSubmittedStartDate'
        ));
        parent::__construct($controller, $name, $fields, $actions, $validator);
    }
}
