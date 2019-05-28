<?php

namespace Monogo\PagespeedAnalysis\Model\System\Config;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

/**
 * Class Dynamicfield
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Dynamicfield extends ArraySerialized
{
    /**
     * {@inheritDoc}
     */
    public function beforeSave()
    {
        $exceptions = $this->getValue();

        $this->setValue($exceptions);

        return parent::beforeSave();
    }
}
