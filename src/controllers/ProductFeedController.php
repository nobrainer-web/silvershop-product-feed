<?php

namespace Meldgaard\ProductFeed;

use SilverShop\Page\Product;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;

class ProductFeedController extends Controller
{

    private static $allowed_actions = [
        'google',
        'pricerunner'
    ];

    public function google()
    {

        Config::modify()->set('SSViewer', 'set_source_file_comments', false);

        $this->getResponse()->addHeader(
            'Content-Type',
            'application/xml; charset="utf-8"'
        );
        $this->getResponse()->addHeader(
            'X-Robots-Tag',
            'noindex'
        );
        $items = Product::get()->filter('RemoveFromProductFeed', false)->exclude('ClassName','GiftVoucherProduct');

        $this->extend('updateGoogleShoppingFeedItems', $items);

        return $this->customise(new ArrayData(array(
            "SiteConfig" => SiteConfig::current_site_config(),
            'Items'      => $items
        )))->renderWith("Meldgaard/ProductFeed/google");
    }

    public function pricerunner()
    {

        Config::modify()->set('SSViewer', 'set_source_file_comments', false);

        $this->getResponse()->addHeader(
            'Content-Type',
            'application/xml; charset="utf-8"'
        );
        $this->getResponse()->addHeader(
            'X-Robots-Tag',
            'noindex'
        );
        $items = Product::get()->exclude('ClassName','GiftVoucherProduct');

        $this->extend('updatePricerunnerFeedItems', $items);

        return $this->customise(new ArrayData(array(
            'SiteConfig'      => SiteConfig::current_site_config(),
            'Items'           => $items,
            'DefaultDelivery' => Config::inst()->get('ProductFeedController', 'DefaultDelivery')
        )))->renderWith("Meldgaard/ProductFeed/pricerunner");
    }

}