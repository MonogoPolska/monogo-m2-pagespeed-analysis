<?php

declare(strict_types=1);

namespace Monogo\PagespeedAnalysis\Ui\Component\Listing\Column;

/**
 * Class Score
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Score
{
    /**
     * Get Score color
     *
     * @param float $value
     *
     * @return string
     */
    public function getScoreColor(float $value) : string
    {
        if ($value < 49) {
            return 'red';
        }
        if ($value < 89) {
            return 'orange';
        }
        return 'green';
    }
}
