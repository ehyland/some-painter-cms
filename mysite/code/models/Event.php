<?php
class Event extends DataObject{

  const GOOGLE_CALENDAR_DATA_PREFIX = 'GCal_';

  private static $db = array(
    'Title' => 'Varchar(255)',
    'StartDate' => 'SS_Datetime',
    'EndDate' => 'SS_Datetime',

    'GCal_etag' => 'Varchar(255)',
    'GCal_id' => 'Varchar(255)',
    'GCal_htmlLink' => 'Varchar(255)',
    'GCal_updated' => 'Varchar(255)',
    'GCal_summary' => 'Varchar(255)',
    'GCal_location' => 'Varchar(255)'
  );

  private static $has_one = array(
    'Gallery' => 'Gallery'
  );

  public static function create_or_update_with_calendar_data($data, $logOut=false){

    $derivedData = $data['derived'];
    $calendarData = $data['calendar'];

    // Get existing event
    $event = self::get()->filter(array(
      'GCal_id' => $calendarData['GCal_id']
    ))->first();

    // Create event if not found
    if (!$event) $event = self::create();

    // Build update map
    $update = $calendarData;
    $update['EndDate'] = $derivedData['EndDate'];
    $update['StartDate'] = $derivedData['StartDate'];
    if (!$event->Title) $update['Title'] = $calendarData['GCal_summary'];

    // Update event
    $event->update($update)->write();

    return $event->ID;
  }

  public function forAPI(){
    return $this->getBaseAPIFields(array(
        'ClassName',
        'GCal_etag',
        'GCal_id',
        'GCal_htmlLink',
        'GCal_updated',
        'GCal_summary',
        'GCal_location',
    ));
  }
}
