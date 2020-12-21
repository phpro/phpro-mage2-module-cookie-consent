<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Model\ResourceModel\CookieGroup;

use Phpro\CookieConsent\Model\CookieGroup as CookieGroupModel;
use Phpro\CookieConsent\Model\ResourceModel\AbstractCollection;
use Phpro\CookieConsent\Model\ResourceModel\CookieGroup as CookieGroupResourceModel;

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
