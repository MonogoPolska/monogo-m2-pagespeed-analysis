<?php

namespace Monogo\PagespeedAnalysis\Model\ResourceModel\Pagespeed;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Pagespeed Collection
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected $_eventPrefix = 'monogo_pagespeed';

    protected $_eventObject = 'pagespeed_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Monogo\PagespeedAnalysis\Model\Pagespeed',
            'Monogo\PagespeedAnalysis\Model\ResourceModel\Pagespeed'
        );
    }
}
