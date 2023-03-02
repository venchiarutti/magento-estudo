<?php
declare(strict_types=1);
namespace Estudo\QuickLearn\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class CreateQuickLearnCategory2 implements DataPatchInterface
{
    public const URL_KEY = 'quicklearn-filha';

    private ModuleDataSetupInterface $moduleDataSetup;
    private CategoryInterfaceFactory $categoryFactory;
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategoryInterfaceFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
    }

    public static function getDependencies(): array
    {
        return [
            CreateQuickLearnCategory::class
        ];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $category = $this->categoryFactory->create();

        $parentCategory = $category->loadByAttribute(
            'url_key',
            CreateQuickLearnCategory::URL_KEY
        );

        $categoryData = [
            'name' => 'Categoria QuickLearn Filha',
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
