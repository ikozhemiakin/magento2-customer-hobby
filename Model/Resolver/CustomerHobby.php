<?php
declare(strict_types=1);

namespace Kozhemiakin\CustomerHobby\Model\Resolver;

use Kozhemiakin\CustomerHobby\Model\Customer\Attribute\Source\Hobby as HobbySource;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Resolver for customer hobby field in GraphQL
 */
class CustomerHobby implements ResolverInterface
{
    /**
     * CustomerHobby constructor
     *
     * @param HobbySource $hobbySource
     */
    public function __construct(
        private readonly HobbySource $hobbySource
    ) {
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): ?string {
        if (!isset($value['model']) || !($value['model'] instanceof CustomerInterface)) {
            return null;
        }

        $hobbyValue = $value['model']->getCustomAttribute('hobby')?->getValue();

        if ($hobbyValue === null || $hobbyValue === '') {
            return null;
        }
        $label = $this->hobbySource->getOptionText($hobbyValue);

        return $label ? (string)$label : null;
    }
}
