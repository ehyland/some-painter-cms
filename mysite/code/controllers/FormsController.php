<?php
class FormsController extends JSONController{

    private static $allowed_actions = array(
        'EventSubmissionForm'
    );

    public function EventSubmissionForm(SS_HTTPRequest $request) {

        $fields = FieldList::create(array(
            TextField::create('UserSubmittedGallery'),
            TextField::create('UserSubmittedArtistName'),
            DatetimeField::create('UserSubmittedStartDate'),
            CheckboxField::create('UserSubmittedHasFreeDrinks')
        ));

        $actions = FieldList::create();

        $validator = RequiredFields::create(array(
            'UserSubmittedGallery',
            'UserSubmittedArtistName',
            'UserSubmittedStartDate'
        ));

        $eventForm = Form::create($this, __FUNCTION__, $fields, $actions, $validator);
        $eventForm->loadDataFrom($_REQUEST);

        if ($eventForm->validate()) {
            // Valid

            $submittedEvent = UserSubmittedEvent::create()
                ->update($eventForm->getData());

            $submittedEvent->write();

            return $this->sendResponse(array(
                'success' => true
            ));
        }else{
            // Invalid
            $errors = $eventForm->getValidator()->getErrors();
            return $this->sendResponse(array(
                'success' => false,
                'errors' => $errors
            ));
        }
    }
}
