<?php

namespace Monogo\PagespeedAnalysis\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Monogo\PagespeedAnalysis\Helper\Config;
use Monogo\PagespeedAnalysis\Model\CollectData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Command Run
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Run extends Command
{
    protected $config;

    protected $collectData;

    protected $appState;

    /**
     * FrontendPing constructor.
     *
     * @param State       $appState    State
     * @param Config      $config      Config Helper
     * @param CollectData $collectData CollectData
     *
     * @throws \Exception
     */
    public function __construct(
        State $appState,
        Config $config,
        CollectData $collectData
    ) {
        $this->config = $config;
        $this->collectData = $collectData;
        $this->appState = $appState;

        parent::__construct();
    }

    /**
     * Command Configuration
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('monogo:pagespeed:run')
            ->setDescription('Collect data for PageSpeed');
        parent::configure();
    }

    /**
     * Execute
     *
     * @param InputInterface  $input  InputInterface
     * @param OutputInterface $output OutputInterface
     *
     * @return void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode(Area::AREA_GLOBAL);
        } catch (\Exception $e) {
        }
        if ($this->config->getIsEnabled() && $this->config->getApiToken()) {
            $this->collectData->run();
        }
    }
}
