<?php

class EventsDataUtil extends Object{

    public static function get_events_data_for_day(SS_Datetime $date) {
        return self::generate_data_for_day($date);
    }

    private static function generate_data_for_day(SS_Datetime $date){
        $data = array(
            'timestamp' => time(),
            'collections' => array(
                'events' => array(),
                'galleries' => array()
            )
        );

        $galleryIDs = array();

        // Get events
        $dateString = $date->Format('Y-m-d');
        $events = Event::get()->where("DATE(`StartDate`) = '$dateString'");
        foreach ($events as $event) {
            $galleryIDs[] = $event->GalleryID;
            $data['collections']['events'][] = $event->forAPI();
        }

        // Get galleries
        $galleryIDs = array_unique($galleryIDs);
        $galleries = Gallery::get()->byIDs($galleryIDs);
        foreach ($galleries as $gallery) {
            $data['collections']['galleries'][] = $gallery->forAPI();
        }

        return $data;
    }
}
