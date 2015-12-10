<?php
class AppConfig extends DataObject {
    private static $db = array(

        // General
        'DefaultSiteTitle' => 'Varchar(255)',
        'DefaultMetaDescription' => 'Text',
        'DefaultMetaKeywords' => 'Text',    // Comma separated list of keywords

        // No Events
        'NoEventsMessages' => 'Text',   // Line separated list of messages
        'NoEventsFormURL' => 'Varchar(255)',

        // Facebook Open Graph
        'Default_OG_Title' => 'Varchar(255)',
        'Default_OG_Description' => 'Varchar(255)',
        'Default_OG_Site_name' => 'Varchar(255)',
        'Default_OG_Type' => 'Varchar(255)',
        'Default_OG_Locale' => 'Varchar(255)',
        'Default_OG_Image_width' => 'Int',
        'Default_OG_Image_height' => 'Int',

        // Thank You Messages
        'ThankYou_Facebook_Share' => 'Varchar(255)',
        'ThankYou_Twitter_Share' => 'Varchar(255)'
    );

    private static $has_one = array(
        "OpenGraphDefaultImage" => "Image"
    );

    private static $defaults = array(
        'DefaultMetaKeywords' => 'Art, Art gallery, gallery, Melbourne, exhibitions, exhibition, tonight, Australia, events',

        'DefaultMetaDescription' => 'Art gallery openings, exhibitions and special events on in Melbourne tonight.',
        'DefaultSiteTitle' => 'Somepainter - Art gallery openings in Melbourne tonight',

        'NoEventsMessages' => "Hmmm.. looks like there's nothing on tonight. Maybe tomorrow?\nShit! Nothing on tonight either. How about the next day?\nOh! Nothing here. Try the day after?\nSorry. There's nothing happening. To help us improve can you answer this one question?",
        'NoEventsFormURL' => 'https://docs.google.com/forms/d/1ynqtBbWCiq0SAC_cQk1wpmg2v3tu3pdys25ACxMy0eI/',

        'Default_OG_Image_width' => 1200,
        'Default_OG_Image_height' => 630
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        // General Setting
        $fields->addFieldsToTab('Root.Main', array(
            TextField::create('DefaultSiteTitle'),
            TextField::create('DefaultMetaDescription'),
            TextField::create('DefaultMetaKeywords')->setDescription('Comma separated list of keywords'),
        ));

        // OG Setting
        $fields->addFieldToTab('Root.FacebookOG',
            LiteralField::create('FacebookOGLink', '<a href="https://developers.facebook.com/docs/sharing/webmasters">https://developers.facebook.com/docs/sharing/webmasters</a>')
        );
        foreach (self::$db as $key => $type) {
            if (strpos($key, 'Default_OG_') !== 0)
                continue;
            $ogTitle = substr($key, strlen('Default_'));
            $ogTitle = str_replace('_', ':', $ogTitle);
            $ogTitle = strtolower($ogTitle);

            $fields->addFieldToTab('Root.FacebookOG', TextField::create($key, $ogTitle));
        }
        $fields->addFieldToTab('Root.FacebookOG',
            UploadField::create('OpenGraphDefaultImage', 'Share Image')->setAllowedMaxFileNumber(1)
        );

        // No Events Display Settings
        $fields->addFieldsToTab('Root.UserMessages', array(

            HeaderField::create('NoEventsMessagesHeader', 'No Events'),
            TextareaField::create('NoEventsMessages')
                ->setDescription('Line separated list of messages. l1=tonights, l2=tomorrow, l3=nextDay, l4=dayAfter'),
            TextField::create('NoEventsFormURL')->setDescription('Link to external form'),

            HeaderField::create('ThankYouMessagesHeader', 'Thank You Messages'),
            TextField::create('NoEventsFormURL')->setDescription('Link to external form'),
            TextField::create('ThankYou_Facebook_Share', 'Facebook Share'),
            TextField::create('ThankYou_Twitter_Share', 'Twitter Share')
        ));

        return $fields;
    }

    public function forAPI(){
        $data = $this->getBaseAPIFields([
            'ID',
            'ClassName',
            'Created'
        ]);

        $data['Default_OG_Image'] = 'http://cms.somepainter.com/' $this->OpenGraphDefaultImage()->CroppedImage(
            $this->Default_OG_Image_width,
            $this->Default_OG_Image_height
        )->getURL();

        return $data;
    }
}
