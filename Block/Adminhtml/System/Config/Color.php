<?php

namespace Monogo\PagespeedAnalysis\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Color Block
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Color extends Field
{
    /**
     * Color constructor.
     *
     * @param Context $context Context
     * @param array   $data    Data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get element html
     *
     * @param AbstractElement $element AbstractElement
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
            require(["jquery","jquery/colorpicker/js/colorpicker"], function ($) {
                $(document).ready(function () {
                	var $el = $("#' . $element->getHtmlId() . '");
                    $el.css("backgroundColor", "' . $value . '");
 
                    $el.ColorPicker({
                    	color: "' . $value . '",
                        onChange: function (hsb, hex, rgb) {
                            $el.css("backgroundColor", "#" + hex).val("#" + hex);
                    	}
                	});
            	});
        	});
	        </script>';
        return $html;
    }
}
