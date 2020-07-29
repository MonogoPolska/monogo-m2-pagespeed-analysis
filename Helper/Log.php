<?php

declare(strict_types=1);

namespace Monogo\PagespeedAnalysis\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Monogo\PagespeedAnalysis\Logger\Logger;

/**
 * Log helper
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Log extends AbstractHelper
{
    /**
     * @var Logger
     */
    protected $logger = null;

    /**
     * @var Config
     */
    protected $config = null;

    /**
     * Data constructor.
     *
     * @param Context $context Context
     * @param Logger  $logger  Logger
     * @param Config  $config  Config
     */
    public function __construct(
        Context $context,
        Logger $logger,
        Config $config
    ) {
        $this->logger = $logger;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * Get logger
     *
     * @return Logger
     */
    protected function getLogger() :Logger
    {
        return $this->logger;
    }

    /**
     * Log to file
     *
     * @param string|array $message Message
     *
     * @return void
     */
    public function log($message) :void
    {
        $enabled
            = $this->config->getEnableLog();
        if ($enabled) {
            $this->getLogger()->info(print_r($message, true));
        }
    }
}
