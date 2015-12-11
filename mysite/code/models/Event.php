<?php
class Event extends DataObject{

    private static $db = array(
        'Title' => 'Varchar(255)',
        'ArtistName' => 'Varchar(255)',
        'StartDate' => 'SS_Datetime',
        'EndDate' => 'SS_Datetime',
        'IsFeatured' => 'Boolean'
    );

    private static $has_one = array(
        'Gallery' => 'Gallery',
        'GoogleCalendarEvent' => 'GoogleCalendarEvent'
    );

    private static $defaults = array(
        'IsFeatured' => false
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->removeByName('GoogleCalendarEventID');

        // Add edit gallery link
        if ($this->GalleryID) {
            $fields->dataFieldByName('GalleryID')
                ->setDescription($this->Gallery()->getDataAdminEditAnchorTag());
        }

        return $fields;
    }

    public static function create_or_update_with_calendar_data($data){

        // Build update map
        $update = $data['derived'];
        $update['GoogleCalendarEventID'] = $data['GoogleCalendarEventID'];
        $update['Title'] = $data['calendar']['summary'];

        // Get or create event
        $filter = array('GoogleCalendarEventID' => $data['GoogleCalendarEventID']);
        if (!$event = self::get()->filter($filter)->first()) {
            $event = self::create();
        }

        // Update event
        return $event->update($update)->write();
    }

    public function forAPI(){
        $data = $this->getBaseAPIFields(array(
            'GoogleCalendarEvent'
        ));
        $data['MelbStartTime'] = $this->obj('StartDate')->Format('gA');
        $data['Date'] = $this->obj('StartDate')->Format('Y-m-d');
        return $data;
    }
}
