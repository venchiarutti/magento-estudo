<?php
declare(strict_types=1);

namespace Estudo\QuickLearn\Setup\Patch\Data;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Patch to create Teste category
 */
class CreateCategory implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var string
     */
    public const URL_KEY = 'teste';

    private ModuleDataSetupInterface $moduleDataSetup;
    private CategoryInterfaceFactory $categoryFactory;
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategoryInterfaceFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategoryInterfaceFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Run code inside patch
     * If code fails, patch must be reverted, in case when we are speaking about schema - then under revert
     * means run PatchInterface::revert()
     * If we speak about data, under revert means: $transaction->rollback()
     *
     * @return $this
     * @throws CouldNotSaveException
     */
    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /** @var CategoryInterface $category */
        $category = $this->categoryFactory->create();

        $categoryData = [
            'name' => 'Categoria Teste',
            'url_key' => self::URL_KEY,
            'active' => true,
            'is_anchor' => false,
            'include_in_menu' => true,
            'display_mode' => 'PAGE',
            'is_active' => true,
            'position' => 1
        ];

        $category->setData($categoryData)
            ->setAttributeSetId($category->getDefaultAttributeSetId());

        $this->categoryRepository->save($category);

        $this->moduleDataSetup->getConnection()->endSetup();
        return $this;
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

    /**
     * Get array of patches that have to be executed prior to this.
     * Example of implementation:
     * [
     *      \Vendor_Name\Module_Name\Setup\Patch\Patch1::class,
     *      \Vendor_Name\Module_Name\Setup\Patch\Patch2::class
     * ]
     *
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Rollback all changes, done by this patch
     *
     * @return void
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function revert(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /** @var CategoryInterface $category */
        $category = $this->categoryFactory->create();

        $category = $category->loadByAttribute(
            'url_key',
            self::URL_KEY
        );

        $this->categoryRepository->delete($category);

        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
