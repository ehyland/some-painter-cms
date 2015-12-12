<?php
class UserSubmittedEvent extends EventSource {

    private static $db = array(
        'ArtistName' => 'Varchar(255)',
        'StartDate' => 'SS_Datetime',
        'EndDate' => 'SS_Datetime',
        'GalleryName' => 'Varchar(255)',
        'GalleryAddress' => 'Varchar(255)',
        'FreeDrinks' => 'Boolean'
    );

    public function createModels () {

    }
    public function createNewEvent () {

    }
    public function updateExistingEvent () {

    }
    public function updateExistingOrCreateEvent () {

    }
}
