<?php
declare(strict_types=1);

namespace Kozhemiakin\CustomerHobby\ViewModel\Account;

use Kozhemiakin\CustomerHobby\Model\Customer\Attribute\Source\Hobby as HobbySource;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Hobby implements ArgumentInterface
{
    /**
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param HobbySource $hobbySource
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        private readonly Session                     $customerSession,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly HobbySource                 $hobbySource,
        private readonly UrlInterface                $urlBuilder
    ) {
    }

    /**
     * Get current customer hobby value.
     *
     * @return int|string|null
     */
    public function getCurrentHobby(): int|string|null
    {
        $customerId = $this->customerSession->getCustomerId();
        if (!$customerId) {
            return null;
        }
        try {
            $customer = $this->customerRepository->getById($customerId);

            return $customer->getCustomAttribute('hobby')?->getValue();
        } catch (LocalizedException $e) {
            return null;
        }
    }

    /**
     * Get customer hobby select attribute options
     *
     * @return array
     */
    public function getCustomerHobbyOptions(): array
    {
        return $this->hobbySource->getAllOptions();
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormAction(): string
    {
        return $this->urlBuilder->getUrl('hobby/index/save', ['_secure' => true]);
    }
}
