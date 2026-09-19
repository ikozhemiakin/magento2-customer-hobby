<?php

declare(strict_types=1);

namespace Kozhemiakin\CustomerHobby\Controller\Index;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Edit hobby page controller
 */
class Edit implements AccountInterface, HttpGetActionInterface
{
    /**
     * @param PageFactory $pageFactory
     */
    public function __construct(
        private readonly PageFactory $pageFactory
    ) {
    }

    /**
     * Execute action
     *
     * @return Page|null
     */
    public function execute(): ?Page
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->set((string)__('Edit Hobby'));

        return $resultPage;
    }
}
