<?php

class EventsDataUtil extends Object{

    public static function get_events_data_for_day(SS_Datetime $date) {
        return self::generate_data_for_day($date);
    }

    private static function generate_data_for_day(SS_Datetime $date){

        $data = array(
            'timestamp' => time(),
            'searchDate' => $date->Format("Y-m-d"),
            'collections' => array(
                'events' => array(),
                'galleries' => array(),
                'locations' => array()
            )
        );

        $galleryIDs = array();
        $locationIDs = array();

        // Get events
        $where =  sprintf("DATE(`StartDate`) = '%s'", $date->Format('Y-m-d'));
        $events = Event::get()
            ->where($where)
            ->exclude(array(
                "GalleryID" => 0,
                "Gallery.LocationID" => 0
            ));
        foreach ($events as $event) {
            $galleryIDs[] = $event->GalleryID;
            $data['collections']['events'][] = $event->forAPI();
        }

        // Get galleries
        $galleries = Gallery::get()
            ->byIDs(array_unique($galleryIDs));
        foreach ($galleries as $gallery) {
            $locationIDs[] = $gallery->LocationID;
            $data['collections']['galleries'][] = $gallery->forAPI();
        }

        // Get locations
        $locations = Location::get()
            ->byIDs(array_unique($locationIDs));
        foreach ($locations as $location) {
            $data['collections']['locations'][] = $location->forAPI();
        }

        return $data;
    }
}
