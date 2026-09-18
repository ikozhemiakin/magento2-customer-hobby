<?php

declare(strict_types=1);

namespace Kozhemiakin\CustomerHobby\Model\Customer\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * class Hobby
 *
 * Source for customer hobby attribute
 */
class Hobby extends AbstractSource
{
    public const VALUE_YOGA = 1;

    public const VALUE_TRAVELING = 2;

    public const VALUE_HIKING = 3;

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions(): array
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('-- Please Select --'), 'value' => ''],
                ['label' => __('Yoga'), 'value' => self::VALUE_YOGA],
                ['label' => __('Traveling'), 'value' => self::VALUE_TRAVELING],
                ['label' => __('Hiking'), 'value' => self::VALUE_HIKING]
            ];
        }

        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|int|null $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        if ($value === null || $value === '') {
            return false;
        }

        return parent::getOptionText($value);
    }
}
