<?php

declare(strict_types=1);

namespace Monogo\PagespeedAnalysis\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * AccessibilityScore PracticesScore
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class AccessibilityScore extends Column
{
    /**
     * @var Score
     */
    protected $score;

    /**
     * PerformanceScore constructor.
     *
     * @param ContextInterface   $context            ContextInterface
     * @param UiComponentFactory $uiComponentFactory UiComponentFactory
     * @param Score              $score              Score
     * @param array              $components         Components
     * @param array              $data               Data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Score $score,
        array $components = [],
        array $data = []
    ) {
        $this->score = $score;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $value = $item['accessibility_score'] * 100;
                $span = '<span style="font-weight:bold ;color: ' . $this->score->getScoreColor($value) . '">' . $value . '</span>';

                $item['accessibility_score'] = $span;
            }
        }
        return $dataSource;
    }
}
