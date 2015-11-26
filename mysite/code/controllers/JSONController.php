<?php
class JSONController extends Controller{

    const CACHE_NAME = 'EVENT_DATA_CACHE';

    private static $allowed_actions = array(
        'getEventsAction'
    );

    private static $url_handlers = array(
        '' => 'getEventsAction'
    );


    public function getEventsAction(SS_HTTPRequest $request) {
        // Search date
        $date = SS_Datetime::now();

        // Check cache
        $cache = SS_Cache::factory(self::CACHE_NAME);
        $cacheKey = $date->Format('Y_m_d');
        if ($result = $cache->load($cacheKey)) {
            $data = unserialize($result);
        }else{
            $data = EventsDataUtil::get_events_data_for_day($date);
            $cache->save(serialize($data), $cacheKey);
        }

        $this->response->addHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($data));
        return $this->response;
    }
}
