<?php

namespace Jti\ConfirmOrder\Cron;

use Monogo\PagespeedAnalysis\Helper\Config;
use Monogo\PagespeedAnalysis\Model\CollectData;

/**
 * Class Cron Run
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Run
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CollectData
     */
    protected $collectData;

    /**
     * PagespeedAnalysis constructor.
     *
     * @param Config      $config      Config Helper
     * @param CollectData $collectData CollectData
     */
    public function __construct(
        Config $config,
        CollectData $collectData
    ) {
        $this->config = $config;
        $this->collectData = $collectData;
    }

    /**
     * Optimize tables
     *
     * @return void
     */
    public function collectData()
    {
        if ($this->config->getIsEnabled() && $this->config->getUseCron()) {
            $this->collectData->run();
        }
    }

    /**
     * Run cron
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $this->collectData();
    }
}
