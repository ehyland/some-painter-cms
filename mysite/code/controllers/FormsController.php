<?php
class FormsController extends JSONController{

    private static $allowed_actions = array(
        'newEventAction'
    );

    private static $url_handlers = array(
        'new-event' => 'newEventAction'
    );

    public function newEventAction(SS_HTTPRequest $request) {

        $data = json_decode($request->getBody(), true);

        $eventForm = new NewEventForm($this, __FUNCTION__);
        $eventForm->loadDataFrom($data);

        if ($eventForm->validate()) {
            // Valid

            $submittedEvent = UserSubmittedEvent::create()
                ->update($eventForm->getData());

            $submittedEvent->write();

            return $this->sendResponse(array(
                'success' => true
            ));
        }
        else {
            // Invalid
            $errors = $eventForm->getValidator()->getErrors();
            return $this->sendResponse(array(
                'success' => false,
                'errors' => $errors
            ));
        }
    }
}
