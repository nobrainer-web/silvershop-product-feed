<?php

namespace Meldgaard\ProductFeed;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\DataExtension;
use TractorCow\AutoComplete\AutoCompleteField;

class CategoryFeedExtension extends DataExtension
{
    private static $db = [
        'GoogleCondition'         => 'Enum(array("new","refurbished","used"),"new")',
        'Brand'                   => 'Varchar',
        'PricerunnerDeliveryTime' => 'Varchar'
    ];

    private static $has_one = [
        'GoogleProductCategory'      => ProductFeedCategory::class,
        'PricerunnerProductCategory' => ProductFeedCategory::class,
    ];

    public function updateCMSFields(FieldList $fields)
    {

        $brandField = new TextField('Brand');

        $googleShopping = new ToggleCompositeField('GoogleShoppingSettings',
            _t(
                'GoogleShoppingFeed.GoogleShoppingFeed',
                'Google Shopping Feed'
            ),
            [
                DropdownField::create(
                    'GoogleCondition',
                    'Product condition',
                    singleton($this->owner->ClassName)->dbObject('GoogleCondition')->enumValues()
                ),
                AutoCompleteField::create(
                    'GoogleProductCategoryID',
                    'Category',
                    '',
                    ProductFeedCategory::class,
                    'Title'
                )
            ]);

        $priceRunner = new ToggleCompositeField('PriceRunnerSettings',
            _t(
                'GoogleShoppingFeed.GoogleShoppingFeed',
                'Pricerunner Shopping Feed'
            ),
            [
                AutoCompleteField::create(
                    'PricerunnerProductCategoryID',
                    'Category',
                    '',
                    ProductFeedCategory::class,
                    'Title'
                ),
                TextField::create('PricerunnerDeliveryTime', 'Leveringstid')
            ]);

        if ($fields->fieldByName('Root')) {
            $fields->addFieldToTab('Root.ProductFeeds', $brandField);
            $fields->addFieldToTab('Root.ProductFeeds', $googleShopping);
            $fields->addFieldToTab('Root.ProductFeeds', $priceRunner);
        }

        return $fields;
    }
}