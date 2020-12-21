<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Model\ResourceModel;

use Phpro\CookieConsent\Model\CookieGroup as CookieGroupModel;

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
