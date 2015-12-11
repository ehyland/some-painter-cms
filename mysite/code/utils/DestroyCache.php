<?php
// TODO: use RequestFilter to flush cache at end of request
class DestroyCache_DataObjectExtension extends Extension{

    private static $caches_need_destroying = false;

    public static function do_caches_need_destroying () {
        return self::$caches_need_destroying;
    }

    public function onAfterWrite () {
        self::$caches_need_destroying = true;
    }

}

class DestroyCache_RequestFilter implements RequestFilter {
    public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model) {
        return true;
    }

    public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model) {

       if (DestroyCache_DataObjectExtension::do_caches_need_destroying()) {
           $this->destroyJSONCahces();
       }

       // return true to send the response.
       return true;
   }

   private function destroyJSONCahces () {
       $cacheNames = [JSONController::EVENTS_CACHE_NAME, JSONController::CONFIG_CACHE_NAME];
       foreach ($cacheNames as $cacheName) {
           SS_Cache::factory($cacheName)->clean(Zend_Cache::CLEANING_MODE_ALL);
       }
   }
}
