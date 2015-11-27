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
                'galleries' => array()
            )
        );

        $galleryIDs = array();

        // Get events
        $where =  sprintf("DATE(`StartDate`) = '%s'", $date->Format('Y-m-d'));
        foreach (Event::get()->where($where) as $event) {
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
