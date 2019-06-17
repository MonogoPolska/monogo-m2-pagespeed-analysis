<?php

namespace Monogo\PagespeedAnalysis\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Pagespeed
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 *
 * @method int getEntityId()
 * @method string getCreatedAt()
 * @method $this setMode(string $mode)
 * @method string getMode()
 * @method $this setUrl(string $url)
 * @method string getUrl()
 * @method $this setOverallCategory(string $overall)
 * @method string getOverallCategory()
 * @method $this setPerformanceScore(float $score)
 * @method float getPerformanceScore()
 * @method $this setAccessibilityScore(float $score)
 * @method float getAccessibilityScore()
 * @method $this setBestPracticesScore(float $score)
 * @method float getBestPracticesScore()
 * @method $this setSeoScore(float $score)
 * @method float getSeoScore()
 * @method $this setPwaScore(float $score)
 * @method float getFirstContentfulPaint()
 * @method $this setFirstContentfulPaint(float $score)
 * @method float getFirstMeaningfulPaint()
 * @method $this setFirstMeaningfulPaint(float $score)
 * @method $this setSpeedIndex(float $score)
 * @method float getSpeedIndex()
 * @method $this setInteractive(float $score)
 * @method float getInteractive()
 * @method $this setFirstCpuIdle(float $score)
 * @method float getFirstCpuIdle()
 * @method float getPwaScore()
 * @method $this setTtfb(float $ttfb)
 * @method float getTtfb()
 * @method $this setRenderBlockingResources(string $resources)
 * @method string getRenderBlockingResources()
 * @method $this setNetworkRequests(string $resources)
 * @method string getNetworkRequests()
 */
class Pagespeed extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'monogo_pagespeed';

    /**
     * @var string
     */
    protected $_cacheTag = 'monogo_pagespeed';

    /**
     * @var string
     */
    protected $_eventPrefix = 'monogo_pagespeed';

    /**
     * Init
     */
    protected function _construct()
    {
        $this->_init('Monogo\PagespeedAnalysis\Model\ResourceModel\Pagespeed');
    }

    /**
     * Get Identities
     *
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
