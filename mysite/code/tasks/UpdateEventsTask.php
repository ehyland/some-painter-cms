<?php

class UpdateEventsTask extends BuildTask {

  protected $enabled = true;
  protected $title = "Sync calendar events";
  protected $description = "Sync calendar events";
  protected $nl;

  private function log($message){

    echo date('d/m/Y H:i:s P') . " => $message$this->nl";
  }

  public function run($request) {
    $this->nl = (Director::is_cli()) ? "\n" : "<br>";

    $this->log("Start!");
    $this->processCalendarData();
    $this->log("Done!");
  }

  public function processCalendarData(){
    $rawData = GoogleCalenderUtil::create()->getEvents();
    $formattedData = array();

    $total = count($rawData['items']);
    $count = 0;

    $this->log("$total events to processed");

    foreach ($rawData['items'] as $eventData) {

      // Get data
      $derivedData = $this->getDerivedData($eventData);
      $gCalData = $this->prefixGCalData($eventData);
      $locationData = array_key_exists('location', $eventData) ?
        $this->getLocationData($eventData['location']) : array();

      $data = array(
        'derived' => $derivedData,
        'calendar' => $gCalData,
        'location' => $locationData
      );

      // Create or update Event
      $eventID = Event::create_or_update_with_calendar_data($data, true);
      $data['EventID'] = $eventID;

      // Create or update Location
      $locationID = Location::create_or_update_with_google_data($data);
      $data['LocationID'] = $locationID;

      // Get or create gallery
      $galleryID = Gallery::create_or_update_with_google_data($data);
      $data['GalleryID'] = $galleryID;

      $this->log(++$count . "/$total");
    }
  }

  public function getDerivedData($rawData){
    $data = array(
      'GalleryName' => '',
      'ArtistName' => '',
      'StartDate' => '',
      'EndDate' => ''
    );

    // Dates
    $data['StartDate'] = $rawData['start']['dateTime'];
    $data['EndDate'] = $rawData['end']['dateTime'];

    // Set GalleryName & ArtistName
    $this->updateWithSummaryData($data, $rawData);

    return $data;
  }

  public function updateWithSummaryData(&$data, $rawData){

    // If summary is in raw data
    if (isset($rawData['summary'])) {
      $summary = trim($rawData['summary']);
      if (is_string($summary) && strlen($summary)) {
        $summary_parts = explode(' - ', $summary);

        // If summary in expected format
        if (count($summary_parts) === 2) {
          $data['GalleryName'] = $summary_parts[0];
          $data['ArtistName'] = $summary_parts[1];
        }
      }
    }
  }

  public function prefixGCalData($rawData){
    $data = array();
    foreach ($rawData as $key => $value) {
      if (!is_array($value)) {
        $data[Event::GOOGLE_CALENDAR_DATA_PREFIX . $key] = $value;
      }
    }
    return $data;
  }

  public function getLocationData($queryString){
    $queryString = trim($queryString);

    // Add focus address to melbourne australia
    if (stripos($queryString, 'australia') === FALSE) {
      if (stripos($queryString, 'melbourne') === FALSE) {
        $queryString .= ' Melbourne';
      }
      $queryString .= ' Australia';
    }

    $data = GoogleGeocodingUtil::create()->get($queryString);

    if (!is_array($data)) {
      $data = array();
    }

    return $data;
  }
}
