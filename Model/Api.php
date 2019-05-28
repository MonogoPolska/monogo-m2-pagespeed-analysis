<?php

namespace Monogo\PagespeedAnalysis\Model;

use Monogo\PagespeedAnalysis\Helper\Log;

/**
 * Class Api
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Api
{
    const API_URL = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

    /**
     * @var Log
     */
    private $logger;

    /**
     * Api constructor.
     *
     * @param Log $logger Log
     */
    public function __construct(Log $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get Raw API response
     *
     * @param string $key      API Key
     * @param string $url      Destination URL
     * @param string $strategy strategy
     *
     * @return string
     */
    public function getApiResponse($key, $url, $strategy)
    {
        try {
            $ch = curl_init($this->prepareUrl($key, $url, $strategy));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60 * 5);
            $output = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpcode == 200) {
                return $output;
            } else {
                $this->logger->log($output);
            }
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage());
            return null;
        }
    }

    /**
     * Prepare API URL
     *
     * @param string $key      API Key
     * @param string $url      Destination URL
     * @param string $strategy strategy
     *
     * @return string
     */
    protected function prepareUrl($key, $url, $strategy)
    {
        return self::API_URL .
            '?url=' . $url .
            '&key=' . $key .
            '&strategy=' . $strategy .
            '&category=accessibility&category=performance&category=pwa&category=seo&category=best-practices';
    }
}
