<?php
/**
 * Button
 *
 * @copyright Copyright © 2024 Magerserv LTD.. All rights reserved.
 * @author    mageserv.ltd@gmail.com
 */

namespace Mageserv\Yamm\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Button
 */
class Button extends Field
{
    /**
     * @var string
     */
    protected $_template = 'system/config/button.phtml';

    /**
     * Unset scope
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope();

        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData([
            'button_label' => $originalData['button_label'],
            'button_url'   => $this->getUrl($originalData['button_url'], ['_current' => true]),
            'html_id'      => $element->getHtmlId(),
        ]);

        return $this->_toHtml();
    }
}
