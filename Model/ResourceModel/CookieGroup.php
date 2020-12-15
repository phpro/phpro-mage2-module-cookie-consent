<?php declare(strict_types=1);

namespace PHPro\CookieConsent\Model\ResourceModel;

use PHPro\CookieConsent\Model\CookieGroup as CookieGroupModel;

class CookieGroup extends AbstractResource
{
    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(CookieGroupModel::ENTITY);
        }

        return parent::getEntityType();
    }
}
