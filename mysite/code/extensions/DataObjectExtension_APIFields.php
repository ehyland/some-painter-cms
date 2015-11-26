<?php
class DataObjectExtension_APIFields extends Extension{
    private static $bool_types = ['Boolean'];
    private static $int_types = ['Int'];
    private static $float_types = ['Float', 'Decimal', 'Double', 'ConfidenceRating'];
    private static $date_types = ['SS_Datetime', 'Date'];

    public function getBaseAPIFields($exclude = array()){
        return array_merge(
            $this->owner->getDBAPIFields($exclude),
            $this->owner->getHasOneAPIFields($exclude),
            $this->owner->getHasManyAPIFields($exclude),
            $this->owner->getManyManyAPIFields($exclude)
        );
    }

    public function getDBAPIFields($exclude = array()){
        $obj = $this->owner;

        $data = array();

        // Get all fields
        $all = array_merge(
            array(
                'ID' => 'Int',
                'ClassName' => 'Enum',
                'LastEdited' => 'SS_Datetime',
                'Created' => 'SS_Datetime'
            ),
            $obj->db()
        );

        // Filter out unwanted fields
        $fields = self::exclude_fields($all, $exclude);

        // db fields and has_ones
        foreach ( $fields as $name => $type) {
            if (in_array($type, self::$bool_types)) {
                $data[$name] = boolval($obj->$name);
            }
            elseif (in_array($type, self::$int_types)) {
                $data[$name] = intval($obj->$name);
            }
            elseif (in_array($type, self::$float_types)) {
                $data[$name] = floatval($obj->$name);
            }
            elseif (in_array($type, self::$date_types)) {
                $data[$name] = $obj->obj($name)->Rfc3339();
            }
            else {
                $data[$name] = $obj->$name;
            }
        }

        return $data;
    }

    public function getHasOneAPIFields($exclude = array()){
        $obj = $this->owner;
        $data = array();
        $fields = self::exclude_fields($obj->has_one(), $exclude);

        foreach ($fields as $name => $type) {
            $data[$name.self::get_prop_name_suffix($type)] = self::get_value_for_relation($obj->$name());
        }

        return $data;
    }

    public function getHasManyAPIFields($exclude = array()){
        $obj = $this->owner;
        $data = array();
        $fields = self::exclude_fields($obj->has_many(), $exclude);

        foreach ($fields as $name => $type) {
            $data[$name] = array();
            $items = $obj->$name();
            foreach ($items as $item) {
                $data[$name][] = self::get_value_for_relation($item);
            }
        }

        return $data;
    }

    public function getManyManyAPIFields($exclude = array()){
        $obj = $this->owner;
        $data = array();
        $fields = self::exclude_fields($obj->many_many(), $exclude);

        foreach ($fields as $name => $type) {
            $data[$name] = array();
            $items = $obj->$name();
            foreach ($items as $item) {
                $data[$name][] = self::get_value_for_relation($item);
            }
        }

        return $data;
    }

    public static function get_value_for_relation($relObj){
        if (!is_object($relObj)) {
            return null;
        }
        elseif (is_a($relObj, 'File')) {
            return $relObj->getAbsoluteURL();
        }
        else{
            return intval($relObj->ID);
        }
    }

    public static function exclude_fields($all, $exclude){
        return  array_diff_key($all, array_flip($exclude));
    }

    public static function get_prop_name_suffix($className){
        if (!class_exists($className)) {
            return '';
        }
        elseif ($className === 'File' || is_subclass_of($className, 'File')) {
            return 'URL';
        }else{
            return 'ID';
        }
    }
}
