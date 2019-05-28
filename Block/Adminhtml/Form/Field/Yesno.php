<?php

namespace Monogo\PagespeedAnalysis\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Context;
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
     * Type constructor.
     *
     * @param \Magento\Framework\View\Element\Context $context Context
     * @param array                                   $data    Data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get option array
     *
     * @return array
     */
    protected function getSelectOptions()
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
     * Set input name
     *
     * @param string $value Value
     *
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->getSelectOptions() as $rewriteType => $rewriteLabel) {
                $this->addOption($rewriteType, addslashes($rewriteLabel));
            }
        }
        return parent::_toHtml();
    }
}
