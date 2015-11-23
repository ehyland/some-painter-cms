<?php

class DateExtension extends Extension{
  public function realRFC2822(){
    $val = $this->owner->value;
    if($val) return date(DateTime::RFC2822, strtotime($val));
  }
}
