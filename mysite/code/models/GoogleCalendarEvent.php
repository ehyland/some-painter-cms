<?php
class GoogleCalendarEvent extends EventSource implements EventSourceInterface{
    private static $db = array(
        'google_calendar_id' => 'Varchar(255)',

        'kind' => 'Varchar(255)',
        'etag' => 'Varchar(255)',
        'status' => 'Varchar(255)',
        'htmlLink' => 'Varchar(255)',
        'updated' => 'Varchar(255)',
        'summary' => 'Varchar(255)',
        'location' => 'Varchar(255)',

        'creator_email' => 'Varchar(255)',
        'creator_displayName' => 'Varchar(255)',
        'creator_self' => 'Varchar(255)',
        'start_dateTime' => 'Varchar(255)',
        'end_dateTime' => 'Varchar(255)'
    );

    private static $indexes = array(
        'GoogleCalIDIndex' => 'unique("google_calendar_id")', //make it a unique index
    );

    public static function create_or_update ($rawData) {
        $data = self::parse_raw_data($rawData);

        if (!($gcEvent = self::get()->filter('google_calendar_id', $data['google_calendar_id'])->first())) {
            $gcEvent = self::create();
        }

        $gcEvent->update($data)->write();

        return $gcEvent;
    }

    /**
     * Return Event and Gallery data
     */
    public function createModels () {

        $eventData = array();
        $galleryData = array();

        // 1:1 data
        $eventData['Title'] = $this->summary;
        $eventData['StartDate'] = $this->start_dateTime;
        $eventData['EndDate'] = $this->end_dateTime;

        // Extract from summary format
        $summary = trim($this->summary);
        if (is_string($summary) && strlen($summary)) {
            $summary_parts = explode(' - ', $summary);
            if (count($summary_parts) === 2) {
                $galleryData['Title'] = $summary_parts[0];
                $eventData['ArtistName'] = $summary_parts[1];
            }
        }

        // Create models
        $event = Event::create()->update($eventData);
        $gallery = Gallery::create()->update($galleryData);

        return array(
            'Event' => $event,
            'Gallery' => $gallery
        );
    }

    public function createNewEvent () {
        $models = $this->createModels();
        $location = Location::create_from_string($this->location);

        // Check for existing gallery
        $existingGallery = Gallery::get()
            ->filter(array(
                'Location.PlaceID' => $location->PlaceID
            ))
            ->first();

        // If existing gallery, don't create a new one
        if ($existingGallery) {
            $location = $existingGallery->Location();
            $models['Gallery'] = $existingGallery;
        }
        else {
            // Write location to database
            $location->write();

            // Write gallery to database
            $models['Gallery']->update(array(
                'LocationID' => $location->ID
            ))->write();
        }

        // Write event to database
        $models['Event']->update(array(
            'GalleryID' => $models['Gallery']->ID
        ))->write();

        // Link event
        $this->update(array(
            'EventID' => $models['Event']->ID,
            'State' => 'Merged'
        ))->write();

        return $this->Event();
    }

    // Update existing Event, Gallery and Location
    public function updateExistingEvent () {
        $event = $this->Event();
        $gallery = $event->Gallery();
        $location = $gallery->Location();

        $updateModels = $this->createModels();

        $event->update(array(
            'Title' => $updateModels['Event']->Title,
            'ArtistName' => $updateModels['Event']->ArtistName,
            'StartDate' => $updateModels['Event']->StartDate,
            'EndDate' => $updateModels['Event']->EndDate,
            'IsFeatured' => $updateModels['Event']->IsFeatured
        ))->write();

        $this->update(array(
            'State' => 'Merged'
        ))->write();

        return $this->Event();
    }

    public function updateExistingOrCreateEvent () {
        if ($this->Event()) {
            return $this->updateExistingEvent();
        }else{
            return $this->createNewEvent();
        }
    }

    public static function parse_raw_data ($rawData) {
        $parsed = array();

        // Add fields that map 1:1
        foreach (array_keys(self::$db) as $key) {
            if (array_key_exists($key, $rawData)) {
                $parsed[$key] = $rawData[$key];
            }
        }

        // Other values
        $parsed['google_calendar_id'] = $rawData['id'];
        $parsed['creator_email'] = $rawData['creator']['email'];
        $parsed['creator_displayName'] = $rawData['creator']['displayName'];
        $parsed['creator_self'] = $rawData['creator']['self'];
        $parsed['start_dateTime'] = $rawData['start']['dateTime'];
        $parsed['end_dateTime'] = $rawData['end']['dateTime'];

        return $parsed;
    }
}
