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

        $fields = FieldList::create(
            TabSet::create('Root', Tab::create('Main'))
        );

        // Link to event
        if ($this->EventID) {
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
        }

        // Display readonly fields
        if ($this->isLocked()) {
            foreach ($this->db() as $field => $type) {
                $fields->addFieldToTab('Root.Main', ReadonlyField::create($field));
            }
        }else {
            $fields->addFieldToTab('Root.Main',
                DropdownField::create(
                    'State',
                    'State',
                     $this->dbObject('State')->enumValues()
                )
            );
        }

        return $fields;
    }

    protected function validate() {
        return parent::validate();
    }
}
