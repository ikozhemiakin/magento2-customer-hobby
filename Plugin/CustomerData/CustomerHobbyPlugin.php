<?php
declare(strict_types=1);

namespace Kozhemiakin\CustomerHobby\Plugin\CustomerData;

use Exception;
use Kozhemiakin\CustomerHobby\Model\Customer\Attribute\Source\Hobby as HobbySource;
use Magento\Customer\CustomerData\Customer;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Psr\Log\LoggerInterface;

/**
 * Class CustomerHobbyPlugin
 *
 * Adds customer hobby data to the customer section in customerData
 */
class CustomerHobbyPlugin
{
    /**
     * CustomerHobbyPlugin constructor.
     *
     * @param CurrentCustomer $currentCustomer
     * @param HobbySource $hobbySource
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly CurrentCustomer $currentCustomer,
        private readonly HobbySource     $hobbySource,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Add hobby to customer section data
     *
     * @param Customer $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData(Customer $subject, array $result): array
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return $result;
        }
        try {
            $customer = $this->currentCustomer->getCustomer();
            $hobby = $customer->getCustomAttribute('hobby');

            $result['hobby'] = $hobby
                ? (string)$this->hobbySource->getOptionText($hobby->getValue())
                : '';
        } catch (Exception $e) {
            $this->logger->error(
                'Unable to retrieve customer hobby.',
                ['exception' => $e]
            );
            $result['hobby'] = '';
        }

        return $result;
    }
}
