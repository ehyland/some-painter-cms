<?php
class AppConfig extends DataObject {
    private static $db = array(
        'DefaultSiteTitle' => 'Varchar(255)',

        'DefaultMetaDescription' => 'Text',
        'DefaultMetaKeywords' => 'Text',    // Comma separated list of keywords

        'NoEventsMessages' => 'Text',   // Line separated list of messages
        'NoEventsFormURL' => 'Varchar(255)',

        // Facebook Open Graph
        // https://developers.facebook.com/docs/sharing/opengraph/object-properties

        // Basic
        'Default_OG_Title' => 'Varchar(255)',
        'Default_OG_Description' => 'Varchar(255)',
        'Default_OG_Site_name' => 'Varchar(255)',

        // Other
        'Default_OG_Type' => 'Varchar(255)',
        'Default_OG_Locale' => 'Varchar(255)',

        // Image
        'Default_OG_Image' => 'Varchar(255)',
        'Default_OG_Image_type' => 'Varchar(255)',
        'Default_OG_Image_width' => 'Varchar(255)',
        'Default_OG_Image_height' => 'Varchar(255)'
    );

    private static $defaults = array(
        'DefaultMetaKeywords' => 'Art, Art gallery, gallery, Melbourne, exhibitions, exhibition, tonight, Australia, events',

        'DefaultMetaDescription' => 'Art gallery openings, exhibitions and special events on in Melbourne tonight.',
        'DefaultSiteTitle' => 'Somepainter - Art gallery openings in Melbourne tonight',

        'NoEventsMessages' => "Hmmm.. looks like there's nothing on tonight. Maybe tomorrow?\nShit! Nothing on tonight either. How about the next day?\nOh! Nothing here. Try the day after?\nSorry. There's nothing happening. To help us improve can you answer this one question?",
        'NoEventsFormURL' => 'https://docs.google.com/forms/d/1ynqtBbWCiq0SAC_cQk1wpmg2v3tu3pdys25ACxMy0eI/'
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', array(
            TextField::create('DefaultSiteTitle'),
            TextField::create('DefaultMetaDescription'),
            TextField::create('DefaultMetaKeywords')->setDescription('Comma separated list of keywords'),
        ));

        foreach (self::$db as $key => $type) {
            if (strpos($key, 'Default_OG_') !== 0)
                continue;
            $ogTitle = substr($key, strlen('Default_'));
            $ogTitle = str_replace('_', ':', $ogTitle);
            $ogTitle = strtolower($ogTitle);

            $fields->addFieldToTab('Root.FacebookOG', TextField::create($key, $ogTitle));
        }


        $fields->addFieldsToTab('Root.NoEventsCopy', array(
            TextareaField::create('NoEventsMessages')
                ->setDescription('Line separated list of messages. l1=tonights, l2=tomorrow, l3=nextDay, l4=dayAfter'),
            TextField::create('NoEventsFormURL')->setDescription('Link to external form')
        ));

        return $fields;
    }

    public function forAPI(){
        return $this->getBaseAPIFields([
            'ID',
            'ClassName',
            'Created'
        ]);
    }
}
