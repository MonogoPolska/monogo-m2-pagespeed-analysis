<?php

declare(strict_types=1);

namespace Monogo\PagespeedAnalysis\Block\Adminhtml\System\Config;

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
     * Get element html
     *
     * @param AbstractElement $element AbstractElement
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element) : string
    {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        return $html . '<script type="text/javascript">
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
    }
}
