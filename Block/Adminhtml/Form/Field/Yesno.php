<?php

declare(strict_types=1);

namespace Monogo\PagespeedAnalysis\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

/**
 * Yesno Block
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Yesno extends Select
{
    /**
     * Options
     *
     * @var array
     */
    private $selectOptions;

    /**
     * Get option array
     *
     * @return array
     */
    protected function getSelectOptions() :array
    {
        if ($this->selectOptions === null) {
            $this->selectOptions = [
                '0' => __('No'),
                '1' => __('Yes'),
            ];
        }
        return $this->selectOptions;
    }

    /**
     * Set "name" for <select> element
     *
     * @param  string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml() :string
    {
        if (!$this->getOptions()) {
            foreach ($this->getSelectOptions() as $rewriteType => $rewriteLabel) {
                $this->addOption($rewriteType, addslashes($rewriteLabel->getText()));
            }
        }
        return parent::_toHtml();
    }
}
