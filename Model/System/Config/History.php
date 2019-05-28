<?php

namespace Monogo\PagespeedAnalysis\Model\System\Config;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class History
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class History implements OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label'=>'30 days','value'=>'30'],
            ['label'=>'60 days','value'=>'60'],
            ['label'=>'90 days','value'=>'90'],
            ['label'=>'180 days','value'=>'180'],
            ['label'=>'360 days','value'=>'360'],
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
