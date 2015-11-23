<?php

class CMSEventsController extends LeftAndMain {
  private static $url_segment = 'events';
  private static $menu_title = 'Events';
  private static $tree_class = 'Event';

  public function getEditForm($id = null, $fields = null) {
    $form = parent::getEditForm();
    $form->addExtraClass('center ss-tabset cms-tabset ' . $this->BaseCSSClasses());

    $fields = $form->Fields();
    $this->createFields($fields);
    $fields->setForm($form);
    return $form;
  }

  public function createFields($fields){
    $fields->push(
      $root = TabSet::create('Root')->setTemplate('CMSTabSet')
    );

    $root->push($this->createNext7DaysTab());
    $root->push($this->createEventsTab());
    $root->push($this->createGalleriesTab());
    $root->push($this->createLocationsTab());
  }

  public function createNext7DaysTab(){
    $sevenDaysTab = Tab::create('Next7Days', 'Next 7 Days',
      $days = TabSet::create('Days')
    );

    for ($i=0; $i < 7; $i++) {
      $data = $this->getFieldsForEventDay($i);
      $days->push(
        Tab::create("Plus{$i}Days", $data['TabTitle'])
          ->setChildren($data['Fields'])
      );
    }

    return $sevenDaysTab;
  }

  public function getFieldsForEventDay($daysFromNow = 0){

    // Get time
    if ($daysFromNow === 0) {
      $time = time();
    }elseif ($daysFromNow === 1) {
      $time = strtotime('+1 day');
    }else{
      $time = strtotime("+{$daysFromNow} days");
    }

    // Field data
    $date = date('Y-m-d', $time);
    $name = 'DaysEvents_' . date('l', $time);
    $title = date('l', $time) . "'s Events - " . date('d/m/Y', $time);
    $events = Event::get()->where("DATE(`StartDate`) = '$date'");

    $eventsGrid = GridField::create(
      $name,
      $title,
      $events,
      GridFieldConfig_RecordEditor::create()
        ->removeComponentsByType('GridFieldPageCount')
        ->removeComponentsByType('GridFieldAddNewButton')
        ->addComponent(new GridFieldAddNewButton('toolbar-header-right'))
    );

    $fields = FieldList::create(
      $eventsGrid
    );

    $tabTitle = date('l', $time) . " ({$events->count()})";

    return array(
      'Fields' => $fields,
      'TabTitle' => $tabTitle
    );
  }

  public function createEventsTab(){
    $today = date('Y-m-d');

    $futureEvents = Event::get()->where("Date(StartDate) >= '$today'");
    $futureEventsGrid = GridField::create(
      'FutureEventsGrid',
      'Future Events',
      $futureEvents,
      GridFieldConfig_RecordEditor::create()
        ->removeComponentsByType('GridFieldPageCount')
        ->removeComponentsByType('GridFieldAddNewButton')
        ->addComponent(new GridFieldAddNewButton('toolbar-header-right'))
    );

    $pastEvents = Event::get()->where("Date(StartDate) < '$today'");
    $pastEventsGrid = GridField::create(
      'PastEventsGrid',
      'Past Events',
      $pastEvents,
      GridFieldConfig_RecordEditor::create()
        ->removeComponentsByType('GridFieldPageCount')
        ->removeComponentsByType('GridFieldAddNewButton')
    );

    $tab = Tab::create('Events',
      TabSet::create('EventsTabSet',
        $futureTab = Tab::create(
          'Events_Future_Tab',
          "Future ({$futureEvents->count()})",
          $futureEventsGrid
        ),
        $pastTab = Tab::create(
          'Events_Past_Tab',
          "Past ({$pastEvents->count()})",
          $pastEventsGrid
        )
      )
    );

    return $tab;
  }

  public function createGalleriesTab(){

    $galleries = Gallery::get();
    $galleriesGrid = GridField::create(
      'GalleriesGrid',
      'Galleries',
      $galleries,
      GridFieldConfig_RecordEditor::create()
        ->removeComponentsByType('GridFieldPageCount')
        ->removeComponentsByType('GridFieldAddNewButton')
    );

    $tab = Tab::create('Galleries', $galleriesGrid);

    return $tab;
  }

  public function createLocationsTab(){

    $locations = Location::get();
    $locationsGrid = GridField::create(
      'LocationsGrid',
      'Locations',
      $locations,
      GridFieldConfig_RecordEditor::create()
        ->removeComponentsByType('GridFieldPageCount')
        ->removeComponentsByType('GridFieldAddNewButton')
    );

    $tab = Tab::create('Locations', $locationsGrid);

    return $tab;
  }
}
