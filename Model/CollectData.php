<?php

namespace Monogo\PagespeedAnalysis\Model;

use Magento\Framework\DB\TransactionFactory;
use Monogo\PagespeedAnalysis\Helper\Config;
use Monogo\PagespeedAnalysis\Helper\Log;

/**
 * Class CollectData
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class CollectData
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
        'performance',
        'accessibility',
        'best-practices',
        'seo',
        'pwa',
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
    public function run()
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
    protected function collect()
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
     */
    protected function prepareObject($response, $endpoint)
    {
        $objectToSave = $this->pagespeedFactory->create();
        $responseArray = json_decode($response, true);

        if (!$this->validateResponse($responseArray)) {
            return;
        }

        $objectToSave->setMode($responseArray['lighthouseResult']['configSettings']['emulatedFormFactor']);
        $objectToSave->setUrl($endpoint);
        $objectToSave->setOverallCategory($this->prepareOverallCategory($responseArray));
        $objectToSave->setPerformanceScore($responseArray['lighthouseResult']['categories']['performance']['score']);
        $objectToSave->setAccessibilityScore($responseArray['lighthouseResult']['categories']['accessibility']['score']);
        $objectToSave->setBestPracticesScore($responseArray['lighthouseResult']['categories']['best-practices']['score']);
        $objectToSave->setSeoScore($responseArray['lighthouseResult']['categories']['seo']['score']);
        $objectToSave->setPwaScore($responseArray['lighthouseResult']['categories']['pwa']['score']);
        $objectToSave->setFirstContentfulPaint($responseArray['lighthouseResult']['audits']['metrics']['details']['items'][0]['firstContentfulPaint']);
        $objectToSave->setFirstMeaningfulPaint($responseArray['lighthouseResult']['audits']['metrics']['details']['items'][0]['firstMeaningfulPaint']);
        $objectToSave->setSpeedIndex($responseArray['lighthouseResult']['audits']['metrics']['details']['items'][0]['speedIndex']);
        $objectToSave->setInteractive($responseArray['lighthouseResult']['audits']['metrics']['details']['items'][0]['interactive']);
        $objectToSave->setFirstCpuIdle($responseArray['lighthouseResult']['audits']['metrics']['details']['items'][0]['firstCPUIdle']);
        $objectToSave->setTtfb($this->prepareTtfb($responseArray));
        $objectToSave->setRenderBlockingResources(json_encode($this->prepareRenderBlockingResources($responseArray)));
        $objectToSave->setNetworkRequests(json_encode($this->prepareNetworkRequests($responseArray)));
        $this->transaction->addObject($objectToSave);
    }

    /**
     * Validate response
     *
     * @param array $responseArray Response Array
     *
     * @return bool
     */
    protected function validateResponse($responseArray)
    {
        if (empty($responseArray)) {
            $this->logger->log('Empty Response');
            return false;
        }
        if (!key_exists('loadingExperience', $responseArray)) {
            $this->logger->log('Empty loadingExperience');
            return false;
        }
        if (!key_exists('lighthouseResult', $responseArray)) {
            $this->logger->log('Empty lighthouseResult');
            return false;
        }
        if (!key_exists('configSettings', $responseArray['lighthouseResult'])) {
            $this->logger->log('Empty configSettings');
            return false;
        }
        if (!key_exists('emulatedFormFactor', $responseArray['lighthouseResult']['configSettings'])) {
            $this->logger->log('Empty emulatedFormFactor');
            return false;
        }
        if (!key_exists('categories', $responseArray['lighthouseResult'])) {
            $this->logger->log('Empty categories');
            return false;
        }
        foreach ($responseArray['lighthouseResult']['categories'] as $category => $result) {
            if (!in_array($category, $this->categories)) {
                $this->logger->log('Missing categories');
                return false;
            }

            if (!key_exists('score', $result)) {
                $this->logger->log('Empty score');
                return false;
            }
        }
        if (!key_exists('audits', $responseArray['lighthouseResult'])) {
            $this->logger->log('Empty audits');
            return false;
        }
        if (!key_exists('time-to-first-byte', $responseArray['lighthouseResult']['audits'])) {
            $this->logger->log('Empty time-to-first-byte');
            return false;
        }
        if (!key_exists('displayValue', $responseArray['lighthouseResult']['audits']['time-to-first-byte'])) {
            $this->logger->log('Empty displayValue');
            return false;
        }

        if (!key_exists('metrics', $responseArray['lighthouseResult']['audits'])) {
            $this->logger->log('Empty metrics');
            return false;
        }

        if (!key_exists('details', $responseArray['lighthouseResult']['audits']['metrics'])) {
            $this->logger->log('Empty metrics details');
            return false;
        }

        if (!key_exists('items', $responseArray['lighthouseResult']['audits']['metrics']['details'])) {
            $this->logger->log('Empty metrics items');
            return false;
        }

        if (!key_exists(0, $responseArray['lighthouseResult']['audits']['metrics']['details']['items'])) {
            $this->logger->log('Empty metrics items data');
            return false;
        }

        if (!key_exists('firstContentfulPaint',
            $responseArray['lighthouseResult']['audits']['metrics']['details']['items'][0])) {
            $this->logger->log('Empty firstContentfulPaint');
            return false;
        }
        if (!key_exists('firstMeaningfulPaint',
            $responseArray['lighthouseResult']['audits']['metrics']['details']['items'][0])) {
            $this->logger->log('Empty firstMeaningfulPaint');
            return false;
        }
        if (!key_exists('speedIndex', $responseArray['lighthouseResult']['audits']['metrics']['details']['items'][0])) {
            $this->logger->log('Empty speedIndex');
            return false;
        }

        if (!key_exists('interactive',
            $responseArray['lighthouseResult']['audits']['metrics']['details']['items'][0])) {
            $this->logger->log('Empty interactive');
            return false;
        }
        if (!key_exists('firstCPUIdle',
            $responseArray['lighthouseResult']['audits']['metrics']['details']['items'][0])) {
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
    protected function prepareOverallCategory($responseArray)
    {
        if (key_exists('overall_category', $responseArray['loadingExperience'])) {
            return $responseArray['loadingExperience']['overall_category'];
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
    protected function prepareTtfb($responseArray)
    {
        $string = $responseArray['lighthouseResult']['audits']['time-to-first-byte']['displayValue'];
        $string = str_replace(',', '', $string);
        $string = str_replace('.', '', $string);
        $value = (int)filter_var($string, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return $value;
    }

    /**
     * Prepare Render Block Resource
     *
     * @param array $responseArray response array
     *
     * @return array
     */
    protected function prepareRenderBlockingResources($responseArray)
    {
        $value = [];
        if (!key_exists('render-blocking-resources', $responseArray['lighthouseResult']['audits'])) {
            return $value;
        }
        if (!key_exists('details', $responseArray['lighthouseResult']['audits']['render-blocking-resources'])) {
            return $value;
        }
        if (!key_exists(
            'items',
            $responseArray['lighthouseResult']['audits']['render-blocking-resources']['details']
        )) {
            return $value;
        }

        if (empty($responseArray['lighthouseResult']['audits']['render-blocking-resources']['details']['items'])) {
            return $value;
        }

        foreach ($responseArray['lighthouseResult']['audits']['render-blocking-resources']['details']['items'] as $item) {
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
    protected function prepareNetworkRequests($responseArray)
    {
        $value = [];
        if (!key_exists('network-requests', $responseArray['lighthouseResult']['audits'])) {
            return $value;
        }
        if (!key_exists('details', $responseArray['lighthouseResult']['audits']['network-requests'])) {
            return $value;
        }
        if (!key_exists(
            'items',
            $responseArray['lighthouseResult']['audits']['network-requests']['details']
        )) {
            return $value;
        }

        if (empty($responseArray['lighthouseResult']['audits']['network-requests']['details']['items'])) {
            return $value;
        }

        foreach ($responseArray['lighthouseResult']['audits']['network-requests']['details']['items'] as $item) {
            if (!empty($item)) {
                $value[] = [
                    'transferSize' => $item['transferSize'],
                    'statusCode' => $item['statusCode'],
                    'url' => $item['url'],
                    'resourceType' => key_exists('resourceType', $item) ? $item['resourceType'] : '',
                    'mimeType' => key_exists('mimeType', $item) ? $item['mimeType'] : '',
                    'resourceSize' => key_exists('resourceSize', $item) ? $item['resourceSize'] : '',
                    'time' => key_exists('endTime', $item) && key_exists(
                        'startTime',
                        $item
                    ) ? (float)$item['endTime'] - (float)$item['startTime'] : 0,
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
    private static function sortByTime($a, $b)
    {
        if ($a['time'] == $b['time']) {
            return 0;
        }
        return ($a['time'] < $b['time']) ? 1 : -1;
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
}
