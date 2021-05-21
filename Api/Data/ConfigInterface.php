<?php

declare(strict_types=1);

namespace Monogo\PagespeedAnalysis\Api\Data;

/**
 * Interface ConfigInterface
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
interface ConfigInterface
{
    const CREATED_AT = 'created_at';

    const LIGHTHOUSE_RESULT = 'lighthouseResult';

    const CONFIG_SETTINGS = 'configSettings';

    const CATEGORIES = 'categories';

    const AUDITS = 'audits';

    const METRICS = 'metrics';

    const DETAILS = 'details';

    const LOADING_EXP = 'loadingExperience';

    const ORIGIN_LOADING_EXP = 'originLoadingExperience';

    const OVERALL_CATEGORY = 'overall_category';

    const EMULATED_FACTOR = 'emulatedFormFactor';

    const SCORE = 'score';

    const SRT = 'server-response-time';

    const DISPLAY_VALUE = 'displayValue';

    const ITEMS = 'items';

    const FCP = 'firstContentfulPaint';

    const FMP = 'firstMeaningfulPaint';

    const SPEED_INDEX = 'speedIndex';

    const INTERACTIVE = 'interactive';

    const FCI = 'firstCPUIdle';

    const RENDER_BLOCK_RESOURCES = 'render-blocking-resources';

    const NETWORK_REQUESTS = 'network-requests';

    const RESOURCE_TYPE = 'resourceType';

    const MIME_TYPE = 'mimeType';

    const RESOURCE_SIZE = 'resourceSize';

    const TIME = 'time';

    const TRANSFER_SIZE = 'transferSize';

    const STATUS_CODE = 'statusCode';

    const URL = 'url';

    const START_TIME = 'startTime';

    const END_TIME = 'endTime';

    const PERFORMANCE = 'performance';

    const ACCESSIBILITY = 'accessibility';

    const BEST_PRACTICES = 'best-practices';

    const SEO = 'seo';

    const PWA = 'pwa';

    const GRAPH = 'graph';

    const LARGEST_CONTENTFUL_PAINT_MS = 'LARGEST_CONTENTFUL_PAINT_MS';

    const FIRST_INPUT_DELAY_MS = 'FIRST_INPUT_DELAY_MS';

    const FIRST_CONTENTFUL_PAINT_MS = 'FIRST_CONTENTFUL_PAINT_MS';

    const CUMULATIVE_LAYOUT_SHIFT_SCORE = 'CUMULATIVE_LAYOUT_SHIFT_SCORE';

    const DISTRIBUTIONS = 'distributions';

    const PROPORTION = 'proportion';

    const CATEGORY = 'category';
}
