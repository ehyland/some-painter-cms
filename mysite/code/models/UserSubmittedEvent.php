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

    protected function validate () {
         $result = parent::validate();

         if ($this->State === 'Approved') {
             if (!$this->GalleryID) {
                 $result->error('You must set a gallery before approving the event');
             }

             if (!$this->StartDate) {
                 $result->error('You must set a start time before approving the event');
             }
         }

         return $result;
    }

    protected function onBeforeWrite () {
        parent::onBeforeWrite();
        if (!$this->ID) {
            $this->onBeforeCreate();
        }

        if ($this->State === 'Approved') {
            $this->createNewEvent(false);
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

        $gallerySource = function () {
            return Gallery::get()->map()->toArray();
        };
        $fields->addFieldsToTab('Root.Main', array(

            // Instractions
            LiteralField::create('Instructions', '<p>To create the the event, set the status to approved and click save</p>'),

            // Event Details
            TextField::create('ArtistName'),
            $this->obj('StartDate')->scaffoldFormField(),
            $this->obj('EndDate')->scaffoldFormField(),
            CheckboxField::create('HasFreeDrinks'),

            // Gallery selector
            DropdownField::create('GalleryID', 'Gallery', $gallerySource())
                ->setEmptyString('(Find or create gallery)')
                ->useAddNew('Gallery', $gallerySource)
        ));

        // Add user submitted fields as readonly
        $fields->addFieldToTab('Root.Main', HeaderField::create('UserSubmittedHeader', 'User Submitted Data'));
        foreach (self::$db as $key => $type) {
            if (strpos($key, 'UserSubmitted') !== 0)
                continue;
            $fields->addFieldToTab(
                'Root.Main',
                ReadonlyField::create($key)
            );
        }

        return $fields;
    }

    /**
     * Create a new event based data in this model
     */
    public function createNewEvent ($writeUpdate = true) {
        $event = Event::create()
            ->update(array(
                'Title' => "{$this->Gallery()->Title} - {$this->ArtistName}",
                'ArtistName' => $this->ArtistName,
                'StartDate' => $this->StartDate,
                'EndDate' => $this->EndDate,
                'HasFreeDrinks' => $this->HasFreeDrinks,
                'GalleryID' => $this->GalleryID
            ));
        $event->write();

        $this->update(array(
            'State' => 'Merged',
            'EventID' => $event->ID,
        ));

        if ($writeUpdate) {
            $this->write();
        }

        return $event;
    }

    /**
     * Update the linked event with data in this model
     */
    public function updateExistingEvent ($writeUpdate = true) {

    }
}
