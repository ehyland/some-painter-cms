<?php

class GoogleCalenderUtil extends GoogleServiceUtil{

  const BASE_URL = 'https://www.googleapis.com/calendar/v3/calendars/';

  public function getEvents($calendarId = 'charles.artopenings@gmail.com', $options = array()){

    $detaults = array(
      'key' => getenv('GOOGLE_API_KEY'),
      'maxResults' => 1000,
      'singleEvents' => 'true',
      'orderBy' => 'startTime',
      'timeMax' => date(DateTime::RFC3339, strtotime('+90 days')),
      'timeMin' => date(DateTime::RFC3339, strtotime('-1 day')),
      'pageToken' => null
    );

    $query = array_merge($detaults, $options);

    // Remove null values
    foreach ($query as $key => $value) {
      if($value === null) unset($query[$key]);
    }

    $url = self::BASE_URL . "{$calendarId}/events";

    $service = new RestfulService($url, 60);
    $service->checkErrors = false;
    $service->setQueryString($query);

    $res = $service->request();
    $data = Convert::json2array($res->getBody());

    return $data;
  }
}
