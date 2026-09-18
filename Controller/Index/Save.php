<?php

declare(strict_types=1);

namespace Kozhemiakin\CustomerHobby\Controller\Index;

use Exception;
use Kozhemiakin\CustomerHobby\Model\Customer\Attribute\Source\Hobby as HobbySource;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Controller\AccountInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Save hobby controller
 */
class Save implements AccountInterface, HttpPostActionInterface
{
    /**
     * Save constructor
     *
     * @param RequestInterface $request
     * @param Session $customerSession
     * @param RedirectFactory $resultRedirectFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param FormKeyValidator $formKeyValidator
     * @param LoggerInterface $logger
     * @param ManagerInterface $messageManager
     * @param HobbySource $hobbySource
     */
    public function __construct(
        private readonly RequestInterface            $request,
        private readonly Session                     $customerSession,
        private readonly RedirectFactory             $resultRedirectFactory,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly FormKeyValidator            $formKeyValidator,
        private readonly LoggerInterface             $logger,
        private readonly ManagerInterface            $messageManager,
        private readonly HobbySource                 $hobbySource
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('hobby/index/edit');
        if (!$this->formKeyValidator->validate($this->request)) {
            $this->messageManager->addErrorMessage(__('Invalid form key. Please refresh the page.'));

            return $resultRedirect;
        }
        $customerId = $this->customerSession->getCustomerId();
        if (!$customerId) {
            $this->messageManager->addErrorMessage(__('Customer session expired. Please log in again.'));
            $resultRedirect->setPath('customer/account/login');

            return $resultRedirect;
        }
        try {
            $hobby = $this->request->getParam('hobby');
            $this->validateHobby($hobby);
            $customer = $this->customerRepository->getById($customerId);
            $customer->setCustomAttribute('hobby', $hobby ?: null);
            $this->customerRepository->save($customer);
            $this->messageManager->addSuccessMessage(__('Your hobby has been saved.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $this->messageManager->addErrorMessage(__('Something went wrong while saving your hobby.'));
        }

        return $resultRedirect;
    }

    /**
     * Validate hobby value
     *
     * @param mixed $hobby
     * @return void
     * @throws LocalizedException
     */
    private function validateHobby(mixed $hobby): void
    {
        if ($hobby === null || $hobby === '') {
            return;
        }
        $allowed = array_column($this->hobbySource->getAllOptions(), 'value');
        if (!is_numeric($hobby) || !in_array((int)$hobby, $allowed, true)) {
            throw new LocalizedException(__('Please select a valid hobby.'));
        }
    }
}
