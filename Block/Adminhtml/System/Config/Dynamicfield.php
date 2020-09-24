<?php

declare(strict_types=1);

namespace Monogo\PagespeedAnalysis\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Html\Select;

/**
 * Dynamicfield Block
 *
 * @category PagespeedAnalysis
 * @package  Monogo|PagespeedAnalysis
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Dynamicfield extends AbstractFieldArray
{
    /**
     * @var Select
     */
    private $yesnoRenderer;

    /**
     * Get Yes No renderer
     *
     * @return Select
     */
    protected function getYesNoRenderer(): Select
    {
        if (!$this->yesnoRenderer) {
            try {
                $this->yesnoRenderer = $this->getLayout()->createBlock(
                    \Monogo\PagespeedAnalysis\Block\Adminhtml\Form\Field\Yesno::class,
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                );
                $this->yesnoRenderer->setClass('customer_group_select required-entry validate-select');
            } catch (\Exception $e) {
            }
        }
        return $this->yesnoRenderer;
    }

    /**
     * {@inheritDoc}
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'col_1',
            [
                'label' => __('Endpoint url with http://'),
                'renderer' => false,
                'class' => 'required-entry',
            ]
        );
        $this->addColumn(
            'col_2',
            [
                'label' => __('Enabled'),
                'renderer' => $this->getYesNoRenderer(),
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * {@inheritDoc}
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->getYesNoRenderer()->calcOptionHash($row->getData('col_2'))] =
            'selected="selected"';

        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
