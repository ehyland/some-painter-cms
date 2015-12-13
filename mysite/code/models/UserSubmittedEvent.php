<?php
class UserSubmittedEvent extends EventSource implements EventSourceInterface {

    private static $db = array(

        // User submitted fields
        'UserSubmittedGallery' => 'Varchar(255)',
        'UserSubmittedArtistName' => 'Varchar(255)',
        'UserSubmittedStartDate' => 'SS_Datetime',
        'UserSubmittedHasFreeDrinks' => 'Boolean',

        // Fields that will be used to create models
        'ArtistName' => 'Varchar(255)',
        'StartDate' => 'SS_Datetime',
        'EndDate' => 'SS_Datetime',
        'HasFreeDrinks' => 'Boolean'

    );

    private static $has_one = array(
        'Gallery' => 'Gallery'
    );

    protected function onBeforeWrite () {
        parent::onBeforeWrite();
        if (!$this->ID) {
            $this->onBeforeCreate();
        }

        if ($this->State === 'Approved') {
            $this->createNewEvent();
        }
    }

    private function onBeforeCreate () {
        // Use UserSubmitted fields values as defaults
        foreach (self::$db as $usrField => $type) {
            $target = substr($usrField, strlen('UserSubmitted'));
            if (strpos($usrField, 'UserSubmitted') === 0 && isset(self::$db[$target])) {
                $this->$target = $this->$usrField;
            }
        }
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        if ($this->isLocked()) {
            return $fields;
        }

        $fields->removeByName(array(
            'EventID'
        ));

        // Add user submitted fields as readonly
        foreach (self::$db as $key => $type) {
            if (strpos($key, 'UserSubmitted') !== 0)
                continue;
            $fields->addFieldToTab(
                'Root.Main',
                ReadonlyField::create($key)
            );
        }

        $gallerySource = function () {
            return Gallery::get()->map()->toArray();
        };

        $fields->addFieldsToTab('Root.Main', array(

            // Event Details
            TextField::create('ArtistName'),
            DatetimeField::create('StartDate'),
            DatetimeField::create('EndDate'),
            CheckboxField::create('HasFreeDrinks'),

            // Gallery selector
            DropdownField::create('GalleryID', 'Gallery', $gallerySource())
                ->useAddNew('Gallery', $gallerySource)
        ));

        return $fields;
    }

    /**
     * Return Event and Gallery data
     */
    public function createModels () {

    }

    /**
     * Create a new event based data in this model
     */
    public function createNewEvent () {

    }

    /**
     * Update the linked event with data in this model
     */
    public function updateExistingEvent () {

    }
}
