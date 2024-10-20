<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\DependencyInjection;

use Jgrasp\PrestashopMigrationPlugin\Model\Address\AddressModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Attribute\AttributeGroupModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Attribute\AttributeModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Feature\FeatureModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Category\CategoryModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Country\CountryModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Currency\CurrencyModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Customer\CustomerModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Employee\EmployeeModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Lang\LangModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Product\ProductAttributeModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Product\ProductBrandModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Product\ProductBrandProductModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Product\ProductModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Product\ProductFeatureModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Shop\ShopModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Tax\TaxCategoryModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Tax\TaxModel;
use Jgrasp\PrestashopMigrationPlugin\Model\Zone\ZoneModel;
use Jgrasp\PrestashopMigrationPlugin\Repository\Address\AddressRepository;
use Jgrasp\PrestashopMigrationPlugin\Repository\Category\CategoryRepository;
use Jgrasp\PrestashopMigrationPlugin\Repository\Country\CountryRepository;
use Jgrasp\PrestashopMigrationPlugin\Repository\Currency\CurrencyRepository;
use Jgrasp\PrestashopMigrationPlugin\Repository\Customer\CustomerRepository;
use Jgrasp\PrestashopMigrationPlugin\Repository\EntityRepository;
use Jgrasp\PrestashopMigrationPlugin\Repository\Product\ProductAttributeRepository;
use Jgrasp\PrestashopMigrationPlugin\Repository\Product\ProductRepository;
use Jgrasp\PrestashopMigrationPlugin\Repository\Shop\ShopRepository;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('prestashop');

        $rootNode = $treeBuilder->getRootNode()->children();

        $rootNode
            ->scalarNode('connection')->defaultValue('')->info('Doctrine connection name')->cannotBeEmpty()->end()
            ->scalarNode('prefix')->defaultValue('ps_')->info('Table prefix for database')->cannotBeEmpty()->end()
            ->scalarNode('flush_step')->defaultValue(100)->info('Number of persist between flush during import.')->cannotBeEmpty()->end()
            ->scalarNode('public_directory')->defaultNull()->info('The public directory where the product images are stored (ex : "https://www.example.com/img/p/")')->cannotBeEmpty()->end()
            ->scalarNode('tmp_directory')->defaultValue('/tmp/prestashop')->info('The temporary directory where the product images will be downloaded.')->cannotBeEmpty()->end();

        $this->addResourceSection($rootNode);

        return $treeBuilder;
    }

    public function addResourceSection(NodeBuilder $builder)
    {
        $builder
            ->arrayNode('resources')
                ->children()
                    ->arrayNode('address')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('address')->end()
                            ->scalarNode('repository')->defaultValue(AddressRepository::class)->end()
                            ->scalarNode('model')->defaultValue(AddressModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_address')->end()
                            ->scalarNode('sylius')->defaultValue('address')->end()
                            ->scalarNode('priority')->defaultValue(220)->end()
                        ->end()
                    ->end()
                    ->arrayNode('admin_user')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('employee')->end()
                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                            ->scalarNode('model')->defaultValue(EmployeeModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_employee')->end()
                            ->scalarNode('sylius')->defaultValue('admin_user')->end()
                            ->scalarNode('priority')->defaultValue(240)->end()
                        ->end()
                    ->end()
                    ->arrayNode('channel')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('shop')->end()
                            ->scalarNode('repository')->defaultValue(ShopRepository::class)->end()
                            ->scalarNode('model')->defaultValue(ShopModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_shop')->end()
                            ->scalarNode('sylius')->defaultValue('channel')->end()
                            ->scalarNode('priority')->defaultValue(250)->end()
                        ->end()
                    ->end()
                    ->arrayNode('country')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('country')->end()
                            ->scalarNode('repository')->defaultValue(CountryRepository::class)->end()
                            ->scalarNode('model')->defaultValue(CountryModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_country')->end()
                            ->scalarNode('sylius')->defaultValue('country')->end()
                            ->scalarNode('priority')->defaultValue(255)->end()
                        ->end()
                    ->end()
                    ->arrayNode('currency')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('currency')->end()
                            ->scalarNode('repository')->defaultValue(CurrencyRepository::class)->end()
                            ->scalarNode('model')->defaultValue(CurrencyModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_currency')->end()
                            ->scalarNode('sylius')->defaultValue('currency')->end()
                            ->scalarNode('priority')->defaultValue(255)->end()
                        ->end()
                    ->end()
                    ->arrayNode('customer')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('customer')->end()
                            ->scalarNode('repository')->defaultValue(CustomerRepository::class)->end()
                            ->scalarNode('model')->defaultValue(CustomerModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_customer')->end()
                            ->scalarNode('sylius')->defaultValue('customer')->end()
                            ->scalarNode('priority')->defaultValue(230)->end()
                        ->end()
                    ->end()
                    ->arrayNode('locale')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('lang')->end()
                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                            ->scalarNode('model')->defaultValue(LangModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_lang')->end()
                            ->scalarNode('sylius')->defaultValue('locale')->end()
                            ->scalarNode('priority')->defaultValue(255)->end()
                        ->end()
                    ->end()
                    ->arrayNode('product')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('product')->end()
                            ->scalarNode('repository')->defaultValue(ProductRepository::class)->end()
                            ->scalarNode('model')->defaultValue(ProductModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_product')->end()
                            ->scalarNode('use_translation')->defaultValue(true)->end()
                            ->scalarNode('sylius')->defaultValue('product')->end()
                            ->scalarNode('priority')->defaultValue(200)->end()
                        ->end()
                    ->end()
                    ->arrayNode('product_feature')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('feature')->end()
                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                            ->scalarNode('model')->defaultValue(FeatureModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_feature')->end()
                            ->scalarNode('use_translation')->defaultValue(true)->end()
                            ->scalarNode('sylius')->defaultValue('product_attribute')->end()
                            ->scalarNode('priority')->defaultValue(210)->end()
                        ->end()
                    ->end()
                    ->arrayNode('product_feature_value')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('feature_product')->end()
                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                            ->scalarNode('model')->defaultValue(ProductFeatureModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue(null)->end()
                            ->scalarNode('use_translation')->defaultValue(false)->end()
                            ->scalarNode('sylius')->defaultValue('product_attribute_value')->end()
                            ->scalarNode('priority')->defaultValue(210)->end()
                        ->end()
                    ->end()
                    ->arrayNode('product_option')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('attribute_group')->end()
                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                            ->scalarNode('model')->defaultValue(AttributeGroupModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_attribute_group')->end()
                            ->scalarNode('use_translation')->defaultValue(true)->end()
                            ->scalarNode('sylius')->defaultValue('product_option')->end()
                            ->scalarNode('priority')->defaultValue(210)->end()
                        ->end()
                    ->end()
                    ->arrayNode('product_option_value')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('attribute')->end()
                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                            ->scalarNode('model')->defaultValue(AttributeModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_attribute')->end()
                            ->scalarNode('use_translation')->defaultValue(true)->end()
                            ->scalarNode('sylius')->defaultValue('product_option_value')->end()
                            ->scalarNode('priority')->defaultValue(205)->end()
                        ->end()
                    ->end()
                    ->arrayNode('product_variant')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('product_attribute')->end()
                            ->scalarNode('repository')->defaultValue(ProductAttributeRepository::class)->end()
                            ->scalarNode('model')->defaultValue(ProductAttributeModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_product_attribute')->end()
                            ->scalarNode('use_translation')->defaultValue(false)->end()
                            ->scalarNode('sylius')->defaultValue('product_variant')->end()
                            ->scalarNode('priority')->defaultValue(190)->end()
                        ->end()
                    ->end()
                    ->arrayNode('taxon')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('category')->end()
                            ->scalarNode('repository')->defaultValue(CategoryRepository::class)->end()
                            ->scalarNode('model')->defaultValue(CategoryModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_category')->end()
                            ->scalarNode('use_translation')->defaultValue(true)->end()
                            ->scalarNode('sylius')->defaultValue('taxon')->end()
                            ->scalarNode('priority')->defaultValue(210)->end()
                        ->end()
                    ->end()
                   /* ->arrayNode('shipping_method')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('carrier')->end()
                            ->scalarNode('repository')->defaultValue(CarrierRepository::class)->end()
                            ->scalarNode('model')->defaultValue(CarrierModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_carrier')->end()
                            ->scalarNode('use_translation')->defaultValue(true)->end()
                            ->scalarNode('sylius')->defaultValue('shipping_method')->end()
                            ->scalarNode('priority')->defaultValue(240)->end()
                        ->end()
                    ->end()*/
                    ->arrayNode('tax_category')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('tax')->end()
                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                            ->scalarNode('model')->defaultValue(TaxCategoryModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_tax')->end()
                            ->scalarNode('use_translation')->defaultValue(true)->end()
                            ->scalarNode('sylius')->defaultValue('tax_category')->end()
                            ->scalarNode('priority')->defaultValue(255)->end()
                        ->end()
                    ->end()
                    ->arrayNode('tax_rate')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('tax')->end()
                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                            ->scalarNode('model')->defaultValue(TaxModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_tax')->end()
                            ->scalarNode('use_translation')->defaultValue(true)->end()
                            ->scalarNode('sylius')->defaultValue('tax_rate')->end()
                            ->scalarNode('priority')->defaultValue(245)->end()
                        ->end()
                    ->end()
                    ->arrayNode('zone')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('zone')->end()
                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                            ->scalarNode('model')->defaultValue(ZoneModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_zone')->end()
                            ->scalarNode('use_translation')->defaultValue(false)->end()
                            ->scalarNode('sylius')->defaultValue('zone')->end()
                            ->scalarNode('priority')->defaultValue(250)->end()
                        ->end()
                    ->end()
                    ->arrayNode('brand')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('product')->end()
                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                            ->scalarNode('model')->defaultValue(ProductBrandModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_manufacturer')->end()
                            ->scalarNode('use_translation')->defaultValue(true)->end()
                            ->scalarNode('sylius')->defaultValue('product_brand')->end()
                            ->scalarNode('priority')->defaultValue(250)->end()
                        ->end()
                    ->end()
                    ->arrayNode('brand_by_product')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('table')->defaultValue('product')->end()
                            ->scalarNode('repository')->defaultValue(ProductRepository::class)->end()
                            ->scalarNode('model')->defaultValue(ProductBrandProductModel::class)->end()
                            ->scalarNode('primary_key')->defaultValue('id_product')->end()
                            ->scalarNode('use_translation')->defaultValue(false)->end()
                            ->scalarNode('sylius')->defaultValue('product')->end()
                            ->scalarNode('priority')->defaultValue(250)->end()
                            ->scalarNode('persister')->defaultValue('prestashop.persister.product_brand_product')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
