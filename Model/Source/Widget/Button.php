<?php declare(strict_types=1);

namespace Phpro\CookieConsent\Model\Source\Widget;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Button extends AbstractSource
{
    public function getAllOptions()
    {
        return [
            0 => 'Button',
            1 => 'Link',
        ];
    }
}
