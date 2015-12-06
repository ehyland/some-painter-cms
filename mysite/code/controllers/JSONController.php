<?php
class JSONController extends Controller{

    const EVENTS_CACHE_NAME = 'EVENT_DATA_CACHE';
    const CONFIG_CACHE_NAME = 'APP_CONFIG_DATA_CACHE';

    private static $allowed_actions = array(
        'getEventsAction'
    );

    private static $url_handlers = array(
        '//$SearchDate/$GetAppConfig' => 'getEventsAction'
    );


    public function getEventsAction(SS_HTTPRequest $request) {

        // Search date
        $date = DBField::create_field("SS_Datetime", $request->param("SearchDate"));
        if (!$date->getValue()) {
            $date = SS_Datetime::now();
        }

        // Get event data
        $cache = SS_Cache::factory(self::EVENTS_CACHE_NAME);
        $cacheKey = $date->Format('Y_m_d');
        if ($result = $cache->load($cacheKey)) {
            $data = unserialize($result);
        }else{
            $data = EventsDataUtil::get_events_data_for_day($date);
            $cache->save(serialize($data), $cacheKey);
        }

        // Get init data
        if ($request->param("GetAppConfig")) {
            $cache = SS_Cache::factory(self::CONFIG_CACHE_NAME);
            $cacheKey = 'APP_CONFIG';
            if ($result = $cache->load($cacheKey)) {
                $configData = unserialize($result);
            }else{
                $configData = AppConfigDataUtil::get_config_data();
                $cache->save(serialize($configData), $cacheKey);
            }
            $data['appConfig'] = $configData;
        }

        $this->response->addHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($data));
        return $this->response;
    }
}
