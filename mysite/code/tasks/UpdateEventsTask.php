<?php

class UpdateEventsTask extends BuildTask {

    protected $enabled = true;
    protected $title = "Sync calendar events";
    protected $description = "Sync calendar events";
    protected $nl;

    private function log($message){
        echo date('d/m/Y H:i:s P') . " => $message$this->nl";
    }

    public function run($request) {
        $this->nl = (Director::is_cli()) ? "\n" : "<br>";
        $this->log("Start!");
        $this->processCalendarData();
        $this->log("Done!");
    }

    public function processCalendarData(){
        $rawData = GoogleCalenderUtil::create()->getEvents();

        if (!$rawData) {
            $this->log("Error fetching google calendar events");
            return;
        }

        $calEventsData = $rawData['items'];
        $total = count($calEventsData);
        $count = 0;

        $this->log("$total events to processed");

        foreach ($calEventsData as $calEventData) {
            $count++;

            $gcEvent = GoogleCalendarEvent::create_or_update($calEventData);

            if ($gcEvent->EventID) {
                // Update existing
                $action = "Updated";
                $event = $gcEvent->updateExistingEvent();
            }else{
                // Create new
                $action = "Created";
                $event = $gcEvent->createNewEvent();
            }

            $this->log("$count/$total :: $action event $event->Title ($event->ID)");
        }
    }
}
