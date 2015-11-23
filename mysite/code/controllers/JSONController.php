<?php
class JSONController extends Controller{

    private static $allowed_actions = array(
        'getEventsAction'
    );

    private static $url_handlers = array(
        '' => 'getEventsAction'
    );


    public function getEventsAction(SS_HTTPRequest $request) {
        $data = EventsDataUtil::get_events_data_for_day(SS_Datetime::now());
        $this->response->addHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($data));
        return $this->response;
    }
}
