<?php
declare(strict_types=1);

namespace Scandiweb\Test\Setup\Patch\Data;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;

class AddSimpleProduct implements DataPatchInterface
{
    private ProductFactory $productFactory;
    private ProductRepositoryInterface $productRepository;
    private StoreManagerInterface $storeManager;
    private CategoryLinkManagementInterface $categoryLinkManagement;

    public function __construct(
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        CategoryLinkManagementInterface $categoryLinkManagement
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->categoryLinkManagement = $categoryLinkManagement;
    }

    public function apply()
    {
        $store = $this->storeManager->getStore();

        $product = $this->productFactory->create();
        $product->setSku('scandi-test-product');
        $product->setName('Scandi Test Product');
        $product->setAttributeSetId(4); // Default attribute set
        $product->setStatus(1); // Enabled
        $product->setWeight(1);
        $product->setVisibility(4); // Catalog, Search
        $product->setTypeId('simple');
        $product->setPrice(19.99);
        $product->setWebsiteIds([$store->getWebsiteId()]);
        $product->setStockData([
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1
        ]);

        $this->productRepository->save($product);

        // Assign to "Men" category (example category ID: 5)
        $categoryId = 5; // You should check the real ID via admin or DB if needed
        $this->categoryLinkManagement->assignProductToCategories(
            $product->getSku(),
            [$categoryId]
        );
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
