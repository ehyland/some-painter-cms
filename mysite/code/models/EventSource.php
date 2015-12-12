<?php
class EventSource extends DataObject {

    private static $db = array(
        'State' => 'Enum("PendingApproval, Approved, Rejected, Merged", "PendingApproval")'
    );
    private static $has_one = array(
        'Event' => 'Event'
    );
    private static $has_many = array();
    private static $belongs_to = array();
    private static $belongs_many_many = array();

    private static $defaults = array();

    protected function validate() {
        return parent::validate();
    }

    // Creates Event, Gallery and Location models from instance data
    // models are not save to database
    public function createModels () {
        // abstract method
    }

    // Creats and saves a new event
    public function createNewEvent () {
        // abstract method
    }

    // Updates existing event
    public function updateExistingEvent () {
        // abstract method
    }

    // Updates existing or creates new event
    public function updateExistingOrCreateEvent () {
        // abstract method
    }
}
