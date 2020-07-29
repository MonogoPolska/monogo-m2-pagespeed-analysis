<?php

declare(strict_types=1);

namespace Monogo\PagespeedAnalysis\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

/**
 * Log Handler
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Handler extends Base
{
    /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     *
     * @var string
     */
    protected $fileName = '/var/log/pagespeed.log';
}
