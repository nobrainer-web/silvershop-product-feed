<?php

namespace Meldgaard\ProductFeed;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\DataExtension;
use TractorCow\AutoComplete\AutoCompleteField;

class ProductFeedExtension extends DataExtension
{
    private static $db = [
        'RemoveFromProductFeed'   => 'Boolean',
        'GoogleCondition'         => 'Enum(array("new","refurbished","used"),"new")',
        'Brand'                   => 'Varchar',
        'EAN'                     => 'Varchar',
        'PricerunnerDeliveryTime' => 'Varchar'
    ];

    private static $has_one = [
        'GoogleProductCategory'      => ProductFeedCategory::class,
        'PricerunnerProductCategory' => ProductFeedCategory::class,
    ];

    public function updateCMSFields(FieldList $fields)
    {

        $removeField = new CheckboxField('RemoveFromProductFeed');
        $brandField = new TextField('Brand');
        $eanField = new TextField('EAN');

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
            if (is_string($this->owner->Variations() && $this->owner->Variations()->exists())) {
                $fields->addFieldToTab('Root.ProductFeeds', $removeField);
                $fields->addFieldToTab('Root.ProductFeeds', $brandField);
                $fields->addFieldToTab('Root.ProductFeeds', $googleShopping);
                $fields->addFieldToTab('Root.ProductFeeds', $priceRunner);
            } else {
                $fields->addFieldToTab('Root.ProductFeeds', $removeField);
                $fields->addFieldToTab('Root.ProductFeeds', $brandField);
                $fields->addFieldToTab('Root.ProductFeeds', $eanField);
                $fields->addFieldToTab('Root.ProductFeeds', $googleShopping);
                $fields->addFieldToTab('Root.ProductFeeds', $priceRunner);
            }
        }

        return $fields;
    }

    public function getInheritedPricerunnerDeliveryTime()
    {
        return $this->getNearestInheritedField('PricerunnerDeliveryTime', '');
    }

    public function getInheritedBrand()
    {
        return $this->getNearestInheritedField('Brand', '');
    }

    public function getInheritedPricerunnerProductCategory()
    {
        $default = ProductFeedCategory::create();

        return $this->getNearestInheritedRelation('PricerunnerProductCategory', $default);
    }

    public function getInheritedGoogleProductCategory()
    {
        $default = ProductFeedCategory::create();

        return $this->getNearestInheritedRelation('GoogleProductCategory', $default);
    }

    public function getNearestInheritedRelation($field, $default)
    {
        $value = $this->owner->{$field}();
        if ($value->exists()) {
            return $value;
        }

        $parent = $this->owner->Parent();

        $inheritedValue = $default;
        while ($parent && $parent->exists()) {
            $inheritedValue = $parent->{$field}();
            if ($inheritedValue->exists()) {
                break;
            }
            $parent = $parent->Parent();
        }

        return $inheritedValue ?: $default;
    }

    public function getNearestInheritedField($field, $default)
    {
        $value = $this->owner->{$field};
        if ($value) {
            return $value;
        }

        $parent = $this->owner->Parent();

        $inheritedValue = $default;
        while ($parent && $parent->exists()) {
            $inheritedValue = $parent->{$field};
            if ($inheritedValue) {
                break;
            }
            $parent = $parent->Parent();
        }

        return $inheritedValue ?: $default;
    }

}