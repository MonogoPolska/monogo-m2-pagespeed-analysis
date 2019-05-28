<?php

namespace Monogo\PagespeedAnalysis\Model\System\Config;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Strategy
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Strategy implements OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label'=>'Mobile','value'=>'mobile'],
            ['label'=>'Desktop','value'=>'desktop'],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $optionArray = [];
        foreach ($this->toOptionArray() as $option) {
            $optionArray[$option['value']] = $option['label'];
        }
        return $optionArray;
    }
}
