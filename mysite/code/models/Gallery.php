<?php
class Gallery extends DataObject{
    private static $db = array(
        "Title" => "Varchar(255)",
        "Subtitle" => "Varchar(255)"
    );

    private static $has_one = array(
        "Location" => "Location"
    );

    private static $has_many = array(
        "Events" => "Event.Gallery"
    );

    public static function create_or_update_with_google_data($data){
        $locationID = $data['LocationID'];

        if ($locationID > 0 && $location = Location::get()->byID($locationID)) {
            $gallery = $location->Gallery();
            if(!$gallery->exists()){
                $gallery = Gallery::create()->update(array(
                    'Title' => $data['derived']['GalleryName'],
                    'LocationID' => $locationID
                ));
                $gallery->write();
            }

            $gallery->getComponents('Events')->add($data['EventID']);

            return $gallery->ID;
        }else{
            return 0;
        }
    }

    public function forAPI(){
        $data = $this->getBaseAPIFields(array(
            'Events',
            'LocationID'
        ));
        return $data;
    }
}
