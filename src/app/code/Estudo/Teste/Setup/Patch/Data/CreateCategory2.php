<?php
declare(strict_types=1);

namespace Estudo\Teste\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

/**
 * Patch to create Teste category
 */
class CreateCategory2 implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var string
     */
    public const URL_KEY = 'subteste';

    private ModuleDataSetupInterface $moduleDataSetup;
    private CategoryInterfaceFactory $categoryFactory;
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategoryInterfaceFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Registry $registry
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategoryInterfaceFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        Registry $registry
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        if (!$registry->registry('isSecureArea')) {
            $registry->register('isSecureArea', true);
        }
    }

    /**
     * Run code inside patch
     * If code fails, patch must be reverted, in case when we are speaking about schema - then under revert
     * means run PatchInterface::revert()
     * If we speak about data, under revert means: $transaction->rollback()
     *
     * @return $this
     * @throws LocalizedException
     */
    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /** @var CategoryInterface $category */
        $category = $this->categoryFactory->create();

        $parentCategory = $category->loadByAttribute(
            'url_key',
            CreateCategory::URL_KEY
        );

        $categoryData = [
            'name' => 'Categoria Subteste',
            'url_key' => self::URL_KEY,
            'active' => true,
            'is_anchor' => false,
            'include_in_menu' => true,
            'display_mode' => 'PAGE',
            'is_active' => true,
            'parent_id' => $parentCategory->getId(),
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
        return [
            CreateCategory::class
        ];
    }

    /**
     * Rollback all changes, done by this patch
     *
     * @return void
     * @throws LocalizedException
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

        if ($category) {
            $this->categoryRepository->delete($category);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
