<?php
declare(strict_types=1);

namespace Estudo\QuickLearn\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Registry;

class CreateQuickLearnCategory implements DataPatchInterface, PatchRevertableInterface
{
    public const URL_KEY = 'quicklearn';

    private ModuleDataSetupInterface $moduleDataSetup;
    private CategoryInterfaceFactory $categoryFactory;
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategoryInterfaceFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        Registry $registry
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        if (!$registry->registry('isSecureArea')) {
            $registry->register('isSecureArea', true);
        }
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $category = $this->categoryFactory->create();

        $categoryData = [
            'name' => 'Categoria QuickLearn',
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

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $category = $this->categoryFactory->create();

        $currentCategory = $category->loadByAttribute(
            'url_key',
            self::URL_KEY
        );

        if ($currentCategory) {
            $this->categoryRepository->delete($currentCategory);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
