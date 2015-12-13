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

    private static $defaults = array(
        'State' => 'PendingApproval'
    );

    public function isLocked () {
        return $this->State === 'Merged' && $this->EventID;
    }

    public function getCMSFields() {

        if ($this->isLocked()) {
            $fields = FieldList::create(
                TabSet::create('Root', Tab::create('Main'))
            );

            // Link to event
            $fields->addFieldToTab(
                'Root.Main',
                LiteralField::create(
                    'EventEditLink',
                    '<div class="field">'
                        .'<label class="left">Linked Event: </label>'
                        .$this->Event()->getDataAdminEditAnchorTag()
                    .'</div>'
                )
            );

            // Display readonly fields
            foreach ($this->db() as $field => $type) {
                $fields->addFieldToTab('Root.Main', ReadonlyField::create($field));
            }

            return $fields;
        }else{
            return parent::getCMSFields();
        }
    }

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
