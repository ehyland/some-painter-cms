<?php
/**
 *  Example data
 *  https://maps.googleapis.com/maps/api/geocode/json?address=94+brunswick+rd,+Brunswick,+Victoria+3056,+Australia
 */
class GoogleGeocodingUtil extends GoogleServiceUtil{

  const BASE_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

  public function get($address){
    $query = array(
      'address' => $address,
      'key' => self::get_key()
    );

    $service = new RestfulService(self::BASE_URL, 60);
    $service->checkErrors = false;
    $service->setQueryString($query);

    $res = $service->request();
    $data = json_decode($res->getBody(), true);

    if (!isset($data['status']) || $data['status'] !== "OK") {
      return false;
    }else{
      return $data['results']['0'];
    }
  }
}
