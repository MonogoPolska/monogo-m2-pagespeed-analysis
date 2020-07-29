<?php

declare(strict_types=1);

namespace Monogo\PagespeedAnalysis\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Pagespeed ResourceModel
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Pagespeed extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('monogo_pagespeed', 'entity_id');
    }
}
