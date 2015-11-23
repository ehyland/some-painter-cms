<?php

class GoogleServiceUtil extends Object{

  const BASE_URL = 'https://www.googleapis.com/calendar/v3';

  protected static function get_key()
  {
    return Config::inst()->get('GoogleServiceUtil', 'API_KEY');
  }

}
