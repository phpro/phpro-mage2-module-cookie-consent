<?php declare(strict_types=1);

namespace PHPro\CookieConsent\Model\ResourceModel\CookieGroup;

use PHPro\CookieConsent\Model\CookieGroup as CookieGroupModel;
use PHPro\CookieConsent\Model\ResourceModel\AbstractCollection;
use PHPro\CookieConsent\Model\ResourceModel\CookieGroup as CookieGroupResourceModel;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            CookieGroupModel::class,
            CookieGroupResourceModel::class
        );
    }
}
