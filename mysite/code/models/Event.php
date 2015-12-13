<?php
class Event extends DataObject{

    private static $db = array(
        'Title' => 'Varchar(255)',
        'ArtistName' => 'Varchar(255)',
        'StartDate' => 'SS_Datetime',
        'EndDate' => 'SS_Datetime',
        'IsFeatured' => 'Boolean',
        'HasFreeDrinks' => 'Boolean'
    );

    private static $has_one = array(
        'Gallery' => 'Gallery'
    );

    private static $belong_to = array(
        'EventSource' => 'EventSource'
    );

    private static $defaults = array(
        'IsFeatured' => false
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->removeByName('EventSource');

        // Add edit gallery link
        if ($this->GalleryID) {
            $fields->dataFieldByName('GalleryID')
                ->setDescription($this->Gallery()->getDataAdminEditAnchorTag());
        }

        return $fields;
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
