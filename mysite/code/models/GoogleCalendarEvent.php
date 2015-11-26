<?php
class GoogleCalendarEvent extends DataObject{
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

    private static $belongs_to = array(
        'Event' => 'Event'
    );

    private static $indexes = array(
        'GoogleCalIDIndex' => 'unique("google_calendar_id")', //make it a unique index
    );

    public static function create_or_update_with_calendar_data($data){
        $update = array();

        $update['google_calendar_id'] = $data['calendar']['id'];

        // Add fields that map 1:1
        foreach (array_keys(self::$db) as $key) {
            if (array_key_exists($key, $data['calendar'])) {
                $update[$key] = $data['calendar'][$key];
            }
        }

        // Add nested values
        $update['creator_email'] = $data['calendar']['creator']['email'];
        $update['creator_displayName'] = $data['calendar']['creator']['displayName'];
        $update['creator_self'] = $data['calendar']['creator']['self'];
        $update['start_dateTime'] = $data['calendar']['start']['dateTime'];
        $update['end_dateTime'] = $data['calendar']['end']['dateTime'];

        // Find or create
        $filter = array('google_calendar_id' => $update['google_calendar_id']);
        if (!$googleCalendarEvent = self::get()->filter($filter)->first()) {
            $googleCalendarEvent = self::create();
        }

        // Update and write
        return $googleCalendarEvent->update($update)->write();
    }
}
