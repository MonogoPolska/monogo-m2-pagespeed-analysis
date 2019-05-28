<?php

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
     * @param string $value
     *
     * @return string
     */
    public function getScoreColor($value)
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
