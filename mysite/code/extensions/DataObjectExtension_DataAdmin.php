<?php
class DataObjectExtension_DataAdmin extends Extension{

    public function getDataAdminEditAnchorTag(){
        $id = $this->owner->ID;
        $title = $this->owner->Title;
        $className = $this->owner->ClassName;
        $href = "admin/events/{$className}/EditForm/field/{$className}/item/{$id}/edit";
        return "<a class=\"cms-panel-link\" href=\"{$href}\">Edit {$title}</a>";
    }

}
