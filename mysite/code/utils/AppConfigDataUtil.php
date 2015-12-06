<?php
class AppConfigDataUtil {
    public static function get_config_data () {
        if (!$config = AppConfig::get()->first()) {
            return [];
        }

        return $config->forAPI();
    }
}
