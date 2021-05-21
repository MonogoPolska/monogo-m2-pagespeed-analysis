<?php

declare(strict_types=1);

namespace Monogo\PagespeedAnalysis\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Config helper
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Config extends AbstractHelper
{
    const CONFIG_PATH = 'pagespeed/';

    const ENABLED = 'general/enable';

    const API_TOKEN = 'general/apitoken';

    const STRATEGY = 'general/strategy';

    const USE_CRON = 'general/use_cron';

    const CRON_EXPR = 'general/cron_expr';

    const ENDPOINTS_LIST = 'endpoints/list';

    const CHART_HEIGHT = 'chart/height';

    const CHART_ANIMATIONS = 'chart/animations';

    const CHART_AUTOSCALE = 'chart/autoscale';

    const CHART_HISTORY = 'chart/history';

    const CHART_PERFORMANCE_COLOR = 'chart/performance';

    const CHART_SEO_COLOR = 'chart/seo';

    const CHART_PWA_COLOR = 'chart/pwa';

    const CHART_BEST_PRACTICES_COLOR = 'chart/best_practices';

    const CHART_ACCESSIBILITY_COLOR = 'chart/accessibility';

    const CHART_TTFB_COLOR = 'chart/ttfb';

    const CHART_FIRST_CONTENTFUL_PAINT_COLOR = 'chart/first_contentful_paint';

    const CHART_FIRST_MEANINGFUL_PAINT_COLOR = 'chart/first_meaningful_paint';

    const CHART_SPEED_INDEX_COLOR = 'chart/speed_index';

    const CHART_INTERACTIVE_COLOR = 'chart/interactive';

    const CHART_FIRST_CPU_IDLE_COLOR = 'chart/first_cpu_idle';

    const CHART_HIDE_X_VALUES = 'chart/hide_x_values';

    const CHART_POINT_RADIUS = 'chart/point_radius';

    const CHART_POINT_HOVER_RADIUS = 'chart/point_hover_radius';

    const ENABLE_LOG = 'debug/enable_log';

    /**
     * @var array
     */
    protected $endpointList = [];

    /**
     * Get Store Config by key
     *
     * @param string $config_path Path
     *
     * @return mixed
     */
    public function getConfig($config_path): string
    {
        return (string)$this->scopeConfig->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Is Module enabled
     *
     * @return int
     */
    public function getIsEnabled(): int
    {
        return (int)$this->getConfig(self::CONFIG_PATH . self::ENABLED);
    }

    /**
     * Get API User
     *
     * @return string
     */
    public function getApiToken(): string
    {
        return $this->getConfig(self::CONFIG_PATH . self::API_TOKEN);
    }

    /**
     * Get Strategy
     *
     * @return array
     */
    public function getStrategy(): array
    {
        return explode(',', $this->getConfig(self::CONFIG_PATH . self::STRATEGY));
    }

    /**
     * Get Use Cron
     *
     * @return int
     */
    public function getUseCron(): int
    {
        return (int)$this->getConfig(self::CONFIG_PATH . self::USE_CRON);
    }

    /**
     * Get Cron Expr
     *
     * @return string
     */
    public function getCronExpr(): string
    {
        return $this->getConfig(self::CONFIG_PATH . self::CRON_EXPR);
    }

    /**
     * Get Endpoint list
     *
     * @return array
     */
    public function getEndpoints(): array
    {
        if (empty($this->endpointList)) {
            $endpointConfig = json_decode($this->getConfig(self::CONFIG_PATH . self::ENDPOINTS_LIST), true);

            if (!empty($endpointConfig)) {
                foreach ($endpointConfig as $endpointConfigItem) {
                    if ($endpointConfigItem['col_2'] == 1) {
                        $this->endpointList[] = rtrim($endpointConfigItem['col_1'], '/');
                    }
                }
            } else {
                $this->endpointList = [];
            }
        }
        return $this->endpointList;
    }

    /**
     * Get Chart height
     *
     * @return int
     */
    public function getChartHeight(): int
    {
        return (int)$this->getConfig(self::CONFIG_PATH . self::CHART_HEIGHT);
    }

    /**
     * Get Enable Animations
     *
     * @return int
     */
    public function getEnableAnimations(): int
    {
        return (int)$this->getConfig(self::CONFIG_PATH . self::CHART_ANIMATIONS);
    }

    /**
     * Get Use Auto Scale
     *
     * @return int
     */
    public function getUseAutoScale(): int
    {
        return (int)$this->getConfig(self::CONFIG_PATH . self::CHART_AUTOSCALE);
    }

    /**
     * Get Chart history
     *
     * @return int
     */
    public function getChartHistory(): int
    {
        return (int)$this->getConfig(self::CONFIG_PATH . self::CHART_HISTORY);
    }

    /**
     * Get Chart Performance color
     *
     * @return string
     */
    public function getChartPerformanceColor(): string
    {
        return $this->getConfig(self::CONFIG_PATH . self::CHART_PERFORMANCE_COLOR);
    }

    /**
     * Get Chart SEO color
     *
     * @return string
     */
    public function getChartSeoColor(): string
    {
        return $this->getConfig(self::CONFIG_PATH . self::CHART_SEO_COLOR);
    }

    /**
     * Get Chart PWA color
     *
     * @return string
     */
    public function getChartPwaColor(): string
    {
        return $this->getConfig(self::CONFIG_PATH . self::CHART_PWA_COLOR);
    }

    /**
     * Get Chart Best Practices color
     *
     * @return string
     */
    public function getChartBestPracticesColor(): string
    {
        return $this->getConfig(self::CONFIG_PATH . self::CHART_BEST_PRACTICES_COLOR);
    }

    /**
     * Get Chart Accessibility color
     *
     * @return string
     */
    public function getChartAccessibilityColor(): string
    {
        return $this->getConfig(self::CONFIG_PATH . self::CHART_ACCESSIBILITY_COLOR);
    }

    /**
     * Get TTFB color
     *
     * @return string
     */
    public function getChartTtfbColor(): string
    {
        return $this->getConfig(self::CONFIG_PATH . self::CHART_TTFB_COLOR);
    }

    /**
     * Get First Contentful Paint color
     *
     * @return string
     */
    public function getChartFirstContentfulPaintColor(): string
    {
        return $this->getConfig(self::CONFIG_PATH . self::CHART_FIRST_CONTENTFUL_PAINT_COLOR);
    }

    /**
     * Get First Meaningful Paint color
     *
     * @return string
     */
    public function getChartFirstMeaningfulPaintColor(): string
    {
        return $this->getConfig(self::CONFIG_PATH . self::CHART_FIRST_MEANINGFUL_PAINT_COLOR);
    }

    /**
     * Get Speed Index color
     *
     * @return string
     */
    public function getChartSpeedIndexColor(): string
    {
        return $this->getConfig(self::CONFIG_PATH . self::CHART_SPEED_INDEX_COLOR);
    }

    /**
     * Get Interactive color
     *
     * @return string
     */
    public function getChartInteractiveColor(): string
    {
        return $this->getConfig(self::CONFIG_PATH . self::CHART_INTERACTIVE_COLOR);
    }

    /**
     * Get First Cpu Idle color
     *
     * @return string
     */
    public function getChartFirstCpuIdleColor(): string
    {
        return $this->getConfig(self::CONFIG_PATH . self::CHART_FIRST_CPU_IDLE_COLOR);
    }

    /**
     * Hide X values
     *
     * @return int
     */
    public function getHideOxValues(): int
    {
        return (int)$this->getConfig(self::CONFIG_PATH . self::CHART_HIDE_X_VALUES);
    }

    /**
     * Get point radius
     *
     * @return int
     */
    public function getPointRadius(): int
    {
        return (int)$this->getConfig(self::CONFIG_PATH . self::CHART_POINT_RADIUS);
    }

    /**
     * Get point hover radius
     *
     * @return int
     */
    public function getPointHoverRadius(): int
    {
        return (int)$this->getConfig(self::CONFIG_PATH . self::CHART_POINT_HOVER_RADIUS);
    }

    /**
     * Enable log to file
     *
     * @return int
     */
    public function getEnableLog(): int
    {
        return (int)$this->getConfig(self::CONFIG_PATH . self::ENABLE_LOG);
    }
}
