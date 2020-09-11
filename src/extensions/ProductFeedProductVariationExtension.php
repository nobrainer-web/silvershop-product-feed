<?php

namespace Meldgaard\ProductFeed;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class ProductFeedProductVariationExtension extends DataExtension
{
    private static $db = [
        'RemoveFromProductFeed' => 'Boolean',
        'EAN'                   => 'Varchar'
    ];

    public function updateCMSFields(FieldList $fields)
    {

        $removeField = new CheckboxField('RemoveFromProductFeed');
        $eanField = new TextField('EAN');

        $fields->push($removeField);
        $fields->push($eanField);

        return $fields;
    }
}