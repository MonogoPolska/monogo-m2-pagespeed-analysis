<?php

namespace Monogo\PagespeedAnalysis\Block\Adminhtml\Pagespeed;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Monogo\PagespeedAnalysis\Helper\Config;
use Monogo\PagespeedAnalysis\Model\ResourceModel\Pagespeed\CollectionFactory;
use Monogo\PagespeedAnalysis\Ui\Component\Listing\Column\Score;

/**
 * Graph Block
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Graph extends Widget
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Config
     */
    protected $config;

    protected $date = null;

    protected $collection = [];

    protected $score;

    /**
     * @var string
     */
    protected $_template = 'Monogo_PagespeedAnalysis::pagespeed/graph.phtml';

    /**
     * Graph constructor.
     *
     * @param Context           $context           Context
     * @param CollectionFactory $collectionFactory CollectionFactory
     * @param Config            $config            Config
     * @param Score             $score             Score
     * @param array             $data              Data
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Config $config,
        Score $score,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
        $this->score = $score;
        parent::__construct($context, $data);
    }

    /**
     * Get Filter date
     *
     * @return string
     */
    public function getFilterDate()
    {
        if ($this->date === null) {
            $this->date = date('Y-m-d', strtotime('-' . $this->config->getChartHistory() . ' day'));
        }
        return $this->date;
    }

    /**
     * Get Collection by endpoint
     *
     * @param string $endpoint Endpoint
     *
     * @return mixed
     */
    public function getCollection($endpoint)
    {
        if (!key_exists(md5($endpoint), $this->collection) || empty($this->collection[md5($endpoint)])) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('url', $endpoint);
            $collection->addFieldToFilter('created_at', ['gt' => $this->getFilterDate()]);
            $collection->setOrder('created_at', 'asc');
            $collection->setOrder('mode', 'asc');
            $this->collection[md5($endpoint)] = $collection;
        }
        return $this->collection[md5($endpoint)];
    }

    /**
     * Get Chart Data
     *
     * @return array
     */
    public function getChartData()
    {
        $chartData = [];
        foreach ($this->config->getEndpoints() as $endpoint) {
            $data = [];
            $collection = $this->getCollection($endpoint);
            foreach ($collection as $item) {
                $createdAt = date('d M Y H:i', strtotime($item->getCreatedAt()));
                if ($item->getComment()) {
                    $data[$item->getMode()]['created_at'][] = [
                        $createdAt,
                        $item->getComment(),
                    ];
                } else {
                    $data[$item->getMode()]['created_at'][] = [
                        $createdAt,
                    ];
                }

                $data[$item->getMode()]['pwa'][] = $item->getPwaScore() * 100;
                $data[$item->getMode()]['seo'][] = $item->getSeoScore() * 100;
                $data[$item->getMode()]['ttfb'][] = $item->getTtfb();
                $data[$item->getMode()]['performance'][] = $item->getPerformanceScore() * 100;
                $data[$item->getMode()]['accessibility'][] = $item->getAccessibilityScore() * 100;
                $data[$item->getMode()]['best_practices'][] = $item->getBestPracticesScore() * 100;
            }

            $chartData[base64_encode($endpoint)] = $data;
        }
        return $chartData;
    }

    public function getLastRecord($endpoint, $mode)
    {
        /**
         * @var \Monogo\PagespeedAnalysis\Model\ResourceModel\Pagespeed\Collection $collection
         */
        $collection = clone $this->getCollection($endpoint);
        $collection->getSelect()->reset(\Magento\Framework\DB\Select::ORDER);
        $collection->setOrder('created_at', 'desc');
        $collection->clear();
        foreach ($collection as $item) {
            if ($item->getMode() == $mode) {
                return $item;
            } else {
                continue;
            }
        }
    }

    /**
     * Get Chart Height
     *
     * @return string
     */
    public function getChartHeight()
    {
        return $this->config->getChartHeight();
    }

    /**
     * Get SEO color
     *
     * @return string
     */
    public function getSeoColor()
    {
        return $this->config->getChartSeoColor();
    }

    /**
     * Get PWA color
     *
     * @return string
     */
    public function getPwaColor()
    {
        return $this->config->getChartPwaColor();
    }

    /**
     * Get Performance color
     *
     * @return string
     */
    public function getPerformanceColor()
    {
        return $this->config->getChartPerformanceColor();
    }

    /**
     * Get Accessibility color
     *
     * @return string
     */
    public function getAccessibilityColor()
    {
        return $this->config->getChartAccessibilityColor();
    }

    /**
     * Get Best Practices color
     *
     * @return string
     */
    public function getBestPracticesColor()
    {
        return $this->config->getChartBestPracticesColor();
    }

    /**
     * Get TTFB color
     *
     * @return string
     */
    public function getTtfbColor()
    {
        return $this->config->getChartTtfbColor();
    }

    /**
     * Get Score color
     *
     * @param string $value
     *
     * @return string
     */
    public function getScoreColor($value)
    {
        return $this->score->getScoreColor($value * 100);
    }

    /**
     * Get chart history
     *
     * @return int
     */
    public function getChartHistory()
    {
        return $this->config->getChartHistory();
    }
}
