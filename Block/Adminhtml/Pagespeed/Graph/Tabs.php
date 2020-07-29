<?php

declare(strict_types=1);

namespace Monogo\PagespeedAnalysis\Block\Adminhtml\Pagespeed\Graph;

use Monogo\PagespeedAnalysis\Block\Adminhtml\Pagespeed\Graph;

/**
 * Graph Tabs
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Tabs extends Graph
{
    /**
     * @var string
     */
    protected $_template = 'Monogo_PagespeedAnalysis::pagespeed/graph/tabs.phtml';
}
