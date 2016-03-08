<?php
class DataAdmin extends ModelAdmin {

    private static $managed_models = array(
        'Event',
        'Gallery',
        'Location',
        'GoogleCalendarEvent',
        'UserSubmittedEvent'
    );

    private static $url_segment = 'events';

    private static $menu_title = 'Data Admin';
}
