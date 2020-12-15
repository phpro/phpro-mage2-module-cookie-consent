<?php declare(strict_types=1);

namespace PHPro\CookieConsent\Block\Widget\Preferences;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Button extends Template implements BlockInterface
{
    protected $_template = "PHPro_CookieConsent::widget/preferences/button.phtml";

    public function getButtonType(): int
    {
        return (int) $this->getData('preferences_button_type');
    }
}
