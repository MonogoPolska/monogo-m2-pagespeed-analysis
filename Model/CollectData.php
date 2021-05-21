<?php

declare(strict_types=1);

namespace Monogo\PagespeedAnalysis\Model;

use Magento\Framework\DB\TransactionFactory;
use Monogo\PagespeedAnalysis\Api\Data\ConfigInterface;
use Monogo\PagespeedAnalysis\Helper\Config;
use Monogo\PagespeedAnalysis\Helper\Log;

/**
 * Class CollectData
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class CollectData implements ConfigInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Api
     */
    protected $api;

    /**
     * @var PagespeedFactory
     */
    protected $pagespeedFactory;

    /**
     * @var int
     */
    protected $numberOfRetries = 5;

    /**
     * @var int
     */
    protected $run = 0;

    /**
     * @var array
     */
    protected $categories = [
        self::PERFORMANCE,
        self::ACCESSIBILITY,
        self::BEST_PRACTICES,
        self::SEO,
        self::PWA,
    ];

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $transaction;

    /**
     * @var Log
     */
    private $logger;

    /**
     * CollectData constructor.
     *
     * @param Config             $config             Config
     * @param Api                $api                Api
     * @param PagespeedFactory   $pagespeedFactory   PagespeedFactory
     * @param TransactionFactory $transactionFactory TransactionFactory
     * @param Log                $logger             Log
     */
    public function __construct(
        Config $config,
        Api $api,
        PagespeedFactory $pagespeedFactory,
        TransactionFactory $transactionFactory,
        Log $logger
    ) {
        $this->config = $config;
        $this->api = $api;
        $this->pagespeedFactory = $pagespeedFactory;
        $this->transaction = $transactionFactory->create();
        $this->logger = $logger;
    }

    /**
     * Run data collection
     *
     * @return void
     */
    public function run(): void
    {
        if ($this->config->getIsEnabled()
            && !empty($this->config->getEndpoints())
            && !empty($this->config->getStrategy())
        ) {
            $this->collect();
        }
    }

    /**
     * Collect data
     *
     * @return void
     */
    protected function collect(): void
    {
        foreach ($this->config->getEndpoints() as $endpoint) {
            foreach ($this->config->getStrategy() as $strategy) {
                $retry = $this->numberOfRetries;
                while ($retry != 0) {
                    $this->run = 1;
                    $this->logger->log('Trying ' . $endpoint . ' with ' . $strategy . ' strategy');
                    $response = $this->api->getApiResponse($this->config->getApiToken(), $endpoint, $strategy);
                    if ($response) {
                        $this->prepareObject($response, $endpoint);
                        $retry = 0;
                    } else {
                        $retry--;
                    }
                }
            }
        }
    }

    /**
     * Prepare object to save
     *
     * @param string $response API response
     * @param string $endpoint Endpoint
     *
     * @return void
     */
    protected function prepareObject(string $response, string $endpoint): void
    {
        $objectToSave = $this->pagespeedFactory->create();
        $responseArray = json_decode($response, true);

        if (!$this->validateResponse($responseArray)) {
            return;
        }

        $objectToSave->setMode($responseArray[self::LIGHTHOUSE_RESULT][self::CONFIG_SETTINGS][self::EMULATED_FACTOR]);
        $objectToSave->setUrl($endpoint);
        $objectToSave->setOverallCategory($this->prepareOverallCategory($responseArray));
        $objectToSave->setPerformanceScore($responseArray[self::LIGHTHOUSE_RESULT][self::CATEGORIES][self::PERFORMANCE][self::SCORE]);
        $objectToSave->setAccessibilityScore($responseArray[self::LIGHTHOUSE_RESULT][self::CATEGORIES][self::ACCESSIBILITY][self::SCORE]);
        $objectToSave->setBestPracticesScore($responseArray[self::LIGHTHOUSE_RESULT][self::CATEGORIES][self::BEST_PRACTICES][self::SCORE]);
        $objectToSave->setSeoScore($responseArray[self::LIGHTHOUSE_RESULT][self::CATEGORIES][self::SEO][self::SCORE]);
        $objectToSave->setPwaScore($responseArray[self::LIGHTHOUSE_RESULT][self::CATEGORIES][self::PWA][self::SCORE]);
        $objectToSave->setFirstContentfulPaint($responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::METRICS][self::DETAILS][self::ITEMS][0][self::FCP]);
        $objectToSave->setFirstMeaningfulPaint($responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::METRICS][self::DETAILS][self::ITEMS][0][self::FMP]);
        $objectToSave->setSpeedIndex($responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::METRICS][self::DETAILS][self::ITEMS][0][self::SPEED_INDEX]);
        $objectToSave->setInteractive($responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::METRICS][self::DETAILS][self::ITEMS][0][self::INTERACTIVE]);
        $objectToSave->setFirstCpuIdle($responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::METRICS][self::DETAILS][self::ITEMS][0][self::FCI]);
        $objectToSave->setTtfb($this->prepareTtfb($responseArray));
        $objectToSave->setRenderBlockingResources(json_encode($this->prepareRenderBlockingResources($responseArray)));
        $objectToSave->setNetworkRequests(json_encode($this->prepareNetworkRequests($responseArray)));
        $this->prepareCwv($objectToSave, $responseArray);
        $this->prepareOriginCwv($objectToSave, $responseArray);
        $this->transaction->addObject($objectToSave);
    }

    /**
     * Validate response
     *
     * @param array $responseArray Response Array
     *
     * @return bool
     */
    protected function validateResponse(array $responseArray): bool
    {
        if (empty($responseArray)) {
            $this->logger->log('Empty Response');
            return false;
        }
        if (!key_exists(self::LOADING_EXP, $responseArray)) {
            $this->logger->log('Empty loadingExperience');
            return false;
        }
        if (!key_exists(self::LIGHTHOUSE_RESULT, $responseArray)) {
            $this->logger->log('Empty lighthouseResult');
            return false;
        }
        if (!key_exists(self::CONFIG_SETTINGS, $responseArray[self::LIGHTHOUSE_RESULT])) {
            $this->logger->log('Empty configSettings');
            return false;
        }
        if (!key_exists(self::EMULATED_FACTOR, $responseArray[self::LIGHTHOUSE_RESULT][self::CONFIG_SETTINGS])) {
            $this->logger->log('Empty emulatedFormFactor');
            return false;
        }
        if (!key_exists(self::CATEGORIES, $responseArray[self::LIGHTHOUSE_RESULT])) {
            $this->logger->log('Empty categories');
            return false;
        }
        foreach ($responseArray[self::LIGHTHOUSE_RESULT][self::CATEGORIES] as $category => $result) {
            if (!in_array($category, $this->categories)) {
                $this->logger->log('Missing categories');
                return false;
            }

            if (!key_exists(self::SCORE, $result)) {
                $this->logger->log('Empty score');
                return false;
            }
        }
        if (!key_exists(self::AUDITS, $responseArray[self::LIGHTHOUSE_RESULT])) {
            $this->logger->log('Empty audits');
            return false;
        }
        if (!key_exists(self::SRT, $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS])) {
            $this->logger->log('Empty server-response-time');
            return false;
        }
        if (!key_exists(self::DISPLAY_VALUE, $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::SRT])) {
            $this->logger->log('Empty displayValue');
            return false;
        }

        if (!key_exists(self::METRICS, $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS])) {
            $this->logger->log('Empty metrics');
            return false;
        }

        if (!key_exists(self::DETAILS, $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::METRICS])) {
            $this->logger->log('Empty metrics details');
            return false;
        }

        if (!key_exists(self::ITEMS,
            $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::METRICS][self::DETAILS])) {
            $this->logger->log('Empty metrics items');
            return false;
        }

        if (!key_exists(0,
            $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::METRICS][self::DETAILS][self::ITEMS])) {
            $this->logger->log('Empty metrics items data');
            return false;
        }

        if (!key_exists(
            self::FCP,
            $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::METRICS][self::DETAILS][self::ITEMS][0]
        )) {
            $this->logger->log('Empty firstContentfulPaint');
            return false;
        }
        if (!key_exists(
            self::FMP,
            $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::METRICS][self::DETAILS][self::ITEMS][0]
        )) {
            $this->logger->log('Empty firstMeaningfulPaint');
            return false;
        }
        if (!key_exists(self::SPEED_INDEX,
            $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::METRICS][self::DETAILS][self::ITEMS][0])) {
            $this->logger->log('Empty speedIndex');
            return false;
        }

        if (!key_exists(
            self::INTERACTIVE,
            $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::METRICS][self::DETAILS][self::ITEMS][0]
        )) {
            $this->logger->log('Empty interactive');
            return false;
        }
        if (!key_exists(
            self::FCI,
            $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::METRICS][self::DETAILS][self::ITEMS][0]
        )) {
            $this->logger->log('Empty firstCPUIdle');
            return false;
        }
        return true;
    }

    /**
     * Prepare Overall Category
     *
     * @param array $responseArray response array
     *
     * @return string
     */
    protected function prepareOverallCategory(array $responseArray): string
    {
        if (key_exists(self::OVERALL_CATEGORY, $responseArray[self::LOADING_EXP])) {
            return $responseArray[self::LOADING_EXP][self::OVERALL_CATEGORY];
        } else {
            return '';
        }
    }

    /**
     * Prepare Time To First Byte value
     *
     * @param array $responseArray response array
     *
     * @return int
     */
    protected function prepareTtfb(array $responseArray): int
    {
        $string = $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::SRT][self::DISPLAY_VALUE];
        $string = str_replace(',', '', $string);
        $string = str_replace('.', '', $string);
        return (int)filter_var($string, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Prepare Render Block Resource
     *
     * @param array $responseArray response array
     *
     * @return array
     */
    protected function prepareRenderBlockingResources(array $responseArray): array
    {
        $value = [];
        if (!key_exists(self::RENDER_BLOCK_RESOURCES, $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS])) {
            return $value;
        }
        if (!key_exists(self::DETAILS,
            $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::RENDER_BLOCK_RESOURCES])) {
            return $value;
        }
        if (!key_exists(
            self::ITEMS,
            $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::RENDER_BLOCK_RESOURCES][self::DETAILS]
        )) {
            return $value;
        }

        if (empty($responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::RENDER_BLOCK_RESOURCES][self::DETAILS][self::ITEMS])) {
            return $value;
        }

        foreach ($responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::RENDER_BLOCK_RESOURCES][self::DETAILS][self::ITEMS] as $item) {
            if (!empty($item)) {
                $value[] = $item;
            }
        }

        return $value;
    }

    /**
     * Prepare Network Request
     *
     * @param array $responseArray response array
     *
     * @return array
     */
    protected function prepareNetworkRequests(array $responseArray): array
    {
        $value = [];
        if (!key_exists(self::NETWORK_REQUESTS, $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS])) {
            return $value;
        }
        if (!key_exists(self::DETAILS, $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::NETWORK_REQUESTS])) {
            return $value;
        }
        if (!key_exists(
            self::ITEMS,
            $responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::NETWORK_REQUESTS][self::DETAILS]
        )) {
            return $value;
        }

        if (empty($responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::NETWORK_REQUESTS][self::DETAILS][self::ITEMS])) {
            return $value;
        }

        foreach ($responseArray[self::LIGHTHOUSE_RESULT][self::AUDITS][self::NETWORK_REQUESTS][self::DETAILS][self::ITEMS] as $item) {
            if (!empty($item)) {
                $value[] = [
                    self::TRANSFER_SIZE => $item[self::TRANSFER_SIZE],
                    self::STATUS_CODE => $item[self::STATUS_CODE],
                    self::URL => $item[self::URL],
                    self::RESOURCE_TYPE => key_exists(self::RESOURCE_TYPE, $item) ? $item[self::RESOURCE_TYPE] : '',
                    self::MIME_TYPE => key_exists(self::MIME_TYPE, $item) ? $item[self::MIME_TYPE] : '',
                    self::RESOURCE_SIZE => key_exists(self::RESOURCE_SIZE, $item) ? $item[self::RESOURCE_SIZE] : '',
                    self::TIME => key_exists(self::END_TIME, $item) && key_exists(
                        self::START_TIME,
                        $item
                    ) ? (float)$item[self::END_TIME] - (float)$item[self::START_TIME] : 0,
                ];
            }
        }
        usort($value, [$this, 'sortByTime']);

        return $value;
    }

    /**
     * Sort array by time
     *
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    private static function sortByTime(array $a, array $b): int
    {
        if ($a[self::TIME] == $b[self::TIME]) {
            return 0;
        }
        return ($a[self::TIME] < $b[self::TIME]) ? 1 : -1;
    }

    /**
     * Destructor - save transaction
     */
    public function __destruct()
    {
        if ($this->run) {
            try {
                $pagespeedResource = $this->pagespeedFactory->create()->getResource();
                $connection = $pagespeedResource->getConnection();
                $connection->beginTransaction();
                $connection->update(
                    $pagespeedResource->getTable('monogo_pagespeed'),
                    ['render_blocking_resources' => null, 'network_requests' => null]
                );
                $connection->commit();
                $this->transaction->save();
            } catch (\Exception $e) {
                $this->logger->log($e->getMessage());
            }
        }
    }

    /**
     * @param Pagespeed $objectToSave
     * @param array     $responseArray
     */
    public function prepareCwv(\Monogo\PagespeedAnalysis\Model\Pagespeed $objectToSave, array $responseArray)
    {
        if (key_exists(self::LOADING_EXP, $responseArray)
            && key_exists(self::METRICS, $responseArray[self::LOADING_EXP])
        ) {
            $objectToSave->setCwvLeAvailable(1);
            $tmpArray = $responseArray[self::LOADING_EXP][self::METRICS];

            if (key_exists(self::CUMULATIVE_LAYOUT_SHIFT_SCORE, $tmpArray)) {
                $objectToSave->setCwvLeClsFast($tmpArray[self::CUMULATIVE_LAYOUT_SHIFT_SCORE][self::DISTRIBUTIONS][0][self::PROPORTION]);
                $objectToSave->setCwvLeClsAverage($tmpArray[self::CUMULATIVE_LAYOUT_SHIFT_SCORE][self::DISTRIBUTIONS][1][self::PROPORTION]);
                $objectToSave->setCwvLeClsSlow($tmpArray[self::CUMULATIVE_LAYOUT_SHIFT_SCORE][self::DISTRIBUTIONS][2][self::PROPORTION]);
                $objectToSave->setCwvLeClsCategory($tmpArray[self::CUMULATIVE_LAYOUT_SHIFT_SCORE][self::CATEGORY]);
            }
            if (key_exists(self::FIRST_CONTENTFUL_PAINT_MS, $tmpArray)) {
                $objectToSave->setCwvLeFcpFast($tmpArray[self::FIRST_CONTENTFUL_PAINT_MS][self::DISTRIBUTIONS][0][self::PROPORTION]);
                $objectToSave->setCwvLeFcpAverage($tmpArray[self::FIRST_CONTENTFUL_PAINT_MS][self::DISTRIBUTIONS][1][self::PROPORTION]);
                $objectToSave->setCwvLeFcpSlow($tmpArray[self::FIRST_CONTENTFUL_PAINT_MS][self::DISTRIBUTIONS][2][self::PROPORTION]);
                $objectToSave->setCwvLeFcpCategory($tmpArray[self::FIRST_CONTENTFUL_PAINT_MS][self::CATEGORY]);
            }
            if (key_exists(self::FIRST_INPUT_DELAY_MS, $tmpArray)) {
                $objectToSave->setCwvLeFidFast($tmpArray[self::FIRST_INPUT_DELAY_MS][self::DISTRIBUTIONS][0][self::PROPORTION]);
                $objectToSave->setCwvLeFidAverage($tmpArray[self::FIRST_INPUT_DELAY_MS][self::DISTRIBUTIONS][1][self::PROPORTION]);
                $objectToSave->setCwvLeFidSlow($tmpArray[self::FIRST_INPUT_DELAY_MS][self::DISTRIBUTIONS][2][self::PROPORTION]);
                $objectToSave->setCwvLeFidCategory($tmpArray[self::FIRST_INPUT_DELAY_MS][self::CATEGORY]);
            }
            if (key_exists(self::LARGEST_CONTENTFUL_PAINT_MS, $tmpArray)) {
                $objectToSave->setCwvLeLcpFast($tmpArray[self::LARGEST_CONTENTFUL_PAINT_MS][self::DISTRIBUTIONS][0][self::PROPORTION]);
                $objectToSave->setCwvLeLcpAverage($tmpArray[self::LARGEST_CONTENTFUL_PAINT_MS][self::DISTRIBUTIONS][1][self::PROPORTION]);
                $objectToSave->setCwvLeLcpSlow($tmpArray[self::LARGEST_CONTENTFUL_PAINT_MS][self::DISTRIBUTIONS][2][self::PROPORTION]);
                $objectToSave->setCwvLeLcpCategory($tmpArray[self::LARGEST_CONTENTFUL_PAINT_MS][self::CATEGORY]);
            }
        }
    }

    /**
     * @param Pagespeed $objectToSave
     * @param array     $responseArray
     */
    public function prepareOriginCwv(\Monogo\PagespeedAnalysis\Model\Pagespeed $objectToSave, array $responseArray)
    {
        if (key_exists(self::ORIGIN_LOADING_EXP, $responseArray)
            && key_exists(self::METRICS, $responseArray[self::ORIGIN_LOADING_EXP])
        ) {
            $objectToSave->setCwvOleAvailable(1);
            $tmpArray = $responseArray[self::ORIGIN_LOADING_EXP][self::METRICS];

            if (key_exists(self::CUMULATIVE_LAYOUT_SHIFT_SCORE, $tmpArray)) {
                $objectToSave->setCwvOleClsFast($tmpArray[self::CUMULATIVE_LAYOUT_SHIFT_SCORE][self::DISTRIBUTIONS][0][self::PROPORTION]);
                $objectToSave->setCwvOleClsAverage($tmpArray[self::CUMULATIVE_LAYOUT_SHIFT_SCORE][self::DISTRIBUTIONS][1][self::PROPORTION]);
                $objectToSave->setCwvOleClsSlow($tmpArray[self::CUMULATIVE_LAYOUT_SHIFT_SCORE][self::DISTRIBUTIONS][2][self::PROPORTION]);
                $objectToSave->setCwvOleClsCategory($tmpArray[self::CUMULATIVE_LAYOUT_SHIFT_SCORE][self::CATEGORY]);
            }
            if (key_exists(self::FIRST_CONTENTFUL_PAINT_MS, $tmpArray)) {
                $objectToSave->setCwvOleFcpFast($tmpArray[self::FIRST_CONTENTFUL_PAINT_MS][self::DISTRIBUTIONS][0][self::PROPORTION]);
                $objectToSave->setCwvOleFcpAverage($tmpArray[self::FIRST_CONTENTFUL_PAINT_MS][self::DISTRIBUTIONS][1][self::PROPORTION]);
                $objectToSave->setCwvOleFcpSlow($tmpArray[self::FIRST_CONTENTFUL_PAINT_MS][self::DISTRIBUTIONS][2][self::PROPORTION]);
                $objectToSave->setCwvOleFcpCategory($tmpArray[self::FIRST_CONTENTFUL_PAINT_MS][self::CATEGORY]);
            }
            if (key_exists(self::FIRST_INPUT_DELAY_MS, $tmpArray)) {
                $objectToSave->setCwvOleFidFast($tmpArray[self::FIRST_INPUT_DELAY_MS][self::DISTRIBUTIONS][0][self::PROPORTION]);
                $objectToSave->setCwvOleFidAverage($tmpArray[self::FIRST_INPUT_DELAY_MS][self::DISTRIBUTIONS][1][self::PROPORTION]);
                $objectToSave->setCwvOleFidSlow($tmpArray[self::FIRST_INPUT_DELAY_MS][self::DISTRIBUTIONS][2][self::PROPORTION]);
                $objectToSave->setCwvOleFidCategory($tmpArray[self::FIRST_INPUT_DELAY_MS][self::CATEGORY]);
            }
            if (key_exists(self::LARGEST_CONTENTFUL_PAINT_MS, $tmpArray)) {
                $objectToSave->setCwvOleLcpFast($tmpArray[self::LARGEST_CONTENTFUL_PAINT_MS][self::DISTRIBUTIONS][0][self::PROPORTION]);
                $objectToSave->setCwvOleLcpAverage($tmpArray[self::LARGEST_CONTENTFUL_PAINT_MS][self::DISTRIBUTIONS][1][self::PROPORTION]);
                $objectToSave->setCwvOleLcpSlow($tmpArray[self::LARGEST_CONTENTFUL_PAINT_MS][self::DISTRIBUTIONS][2][self::PROPORTION]);
                $objectToSave->setCwvOleLcpCategory($tmpArray[self::LARGEST_CONTENTFUL_PAINT_MS][self::CATEGORY]);
            }
        }
    }
}
