<?php
declare(strict_types=1);

namespace Kozhemiakin\CustomerHobby\Setup\Patch\Data;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use Magento\Customer\Setup\CustomerSetupFactory;
use Kozhemiakin\CustomerHobby\Model\Customer\Attribute\Source\Hobby;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Validator\ValidateException;

/**
 * Class CreateHobbyCustomerAttribute
 *
 * Create customer hobby attribute
 */
class CreateHobbyCustomerAttribute implements DataPatchInterface, PatchRevertableInterface
{
    private const ATTRIBUTE_CODE = 'hobby';

    /**
     * CreateHobbyCustomerAttribute constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeResource $attributeResource
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly CustomerSetupFactory     $customerSetupFactory,
        private readonly AttributeResource        $attributeResource
    ) {
    }

    /**
     * Run code inside patch
     *
     * @return void
     * @throws AlreadyExistsException
     * @throws LocalizedException
     * @throws ValidateException
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        // Add customer attribute with settings
        $customerSetup->addAttribute(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            self::ATTRIBUTE_CODE,
            [
                'type' => 'int',
                'label' => 'Hobby',
                'input' => 'select',
                'source' => Hobby::class,
                'required' => 0,
                'visible' => 1,
                'user_defined' => 1,
                'system' => 0,
                'global' => 1,
                'position' => 100,
                'is_used_in_grid' => 1,
                'is_visible_in_grid' => 1,
                'is_filterable_in_grid' => 1,
                'is_searchable_in_grid' => 1
            ]
        );

        // Add attribute to default attribute set and group
        $customerSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            self::ATTRIBUTE_CODE
        );

        $attribute = $customerSetup->getEavConfig()
            ->getAttribute(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, self::ATTRIBUTE_CODE);

        $attribute->setData('used_in_forms', [
            'adminhtml_customer',
            'customer_account_edit',
        ]);

        // Save attribute using its resource model
        $this->attributeResource->save($attribute);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Revert patch changes
     *
     * @return void
     */
    public function revert(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->removeAttribute(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            self::ATTRIBUTE_CODE
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Get array of patches that have to be executed prior to this.
     *
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
