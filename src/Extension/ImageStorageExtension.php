<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\SymfonyBundle\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use WebChemistry\ImageStorage\Adapter\AdapterInterface;
use WebChemistry\ImageStorage\Adapter\SimpleAdapter;
use WebChemistry\ImageStorage\Configuration\ConfigurationInterface;
use WebChemistry\ImageStorage\Filter\FilterProcessorInterface;
use WebChemistry\ImageStorage\ImageStorageInterface;
use WebChemistry\ImageStorage\ImagineFilters\FilterLoader;
use WebChemistry\ImageStorage\ImagineFilters\FilterLoaderInterface;
use WebChemistry\ImageStorage\ImagineFilters\FilterProcessor;
use WebChemistry\ImageStorage\ImagineFilters\ImageFilterInterface;
use WebChemistry\ImageStorage\Metadata\ImageMetadataFactory;
use WebChemistry\ImageStorage\Metadata\ImageMetadataFactoryInterface;
use WebChemistry\ImageStorage\Resolver\BucketResolverInterface;
use WebChemistry\ImageStorage\Resolver\BucketResolvers\BucketResolver;
use WebChemistry\ImageStorage\Resolver\FilterResolverInterface;
use WebChemistry\ImageStorage\Resolver\FilterResolvers\OriginalFilterResolver;
use WebChemistry\ImageStorage\Resolver\NameResolverInterface;
use WebChemistry\ImageStorage\Resolver\NameResolvers\PrefixNameResolver;
use WebChemistry\ImageStorage\Resolver\PathResolverInterface;
use WebChemistry\ImageStorage\Resolver\PathResolvers\PathResolver;
use WebChemistry\ImageStorage\Storages\LocalStorage;
use WebChemistry\ImageStorage\SymfonyBundle\Configuration\SymfonyLocalConfiguration;

final class ImageStorageExtension extends Extension
{

	const FILTER_TAG = 'webchemistry.imageStorage.filter';

	public function load(array $configs, ContainerBuilder $container)
	{
		$container->register('webchemistry.imageStorage.configuration', SymfonyLocalConfiguration::class)
			->setAutowired(true)
			->setArgument(0, '%kernel.project_dir%');

		$container->setAlias(ConfigurationInterface::class, 'webchemistry.imageStorage.configuration');

		$this->loadResolvers($container);
		$this->loadMetadata($container);
		$this->loadFilters($container);
		$this->loadFilterProcessor($container);
		$this->loadAdapter($container);

		$container->register('webchemistry.imageStorage.storage', LocalStorage::class)
			->setAutowired(true);

		$container->setAlias(ImageStorageInterface::class, 'webchemistry.imageStorage.storage');
	}

	private function loadResolvers(ContainerBuilder $container): void
	{
		$container->register('webchemistry.imageStorage.resolvers.bucket', BucketResolver::class)
			->setAutowired(true);

		$container->setAlias(BucketResolverInterface::class, 'webchemistry.imageStorage.resolvers.bucket');

		$container->register('webchemistry.imageStorage.resolvers.name', PrefixNameResolver::class)
			->setAutowired(true);

		$container->setAlias(NameResolverInterface::class, 'webchemistry.imageStorage.resolvers.name');

		$container->register('webchemistry.imageStorage.resolvers.filter', OriginalFilterResolver::class)
			->setAutowired(true);

		$container->setAlias(FilterResolverInterface::class, 'webchemistry.imageStorage.resolvers.filter');

		$container->register('webchemistry.imageStorage.resolvers.path', PathResolver::class)
			->setAutowired(true);

		$container->setAlias(PathResolverInterface::class, 'webchemistry.imageStorage.resolvers.path');
	}

	private static function prefix(string $name): string
	{
		return "webchemistry.imageStorage.{$name}";
	}

	private function loadMetadata(ContainerBuilder $container): void
	{
		$container->register('webchemistry.imageStorage.metadata.factory', ImageMetadataFactory::class)
			->setAutowired(true);

		$container->setAlias(ImageMetadataFactoryInterface::class, 'webchemistry.imageStorage.metadata.factory');
	}

	private function loadFilterProcessor(ContainerBuilder $container): void
	{
		$container->register('webchemistry.imageStorage.filterProcessor', FilterProcessor::class)
			->setAutowired(true);

		$container->setAlias(FilterProcessorInterface::class, 'webchemistry.imageStorage.filterProcessor');

		$loader = $container->register('webchemistry.imageStorage.filterLoader', FilterLoader::class)
			->setAutowired(true);

		foreach ($container->findTaggedServiceIds(self::FILTER_TAG) as $serviceId => $tags) {
			$loader->addMethodCall('addFilter', [$container->get($serviceId)]);
		}

		$container->setAlias(FilterLoaderInterface::class, 'webchemistry.imageStorage.filterLoader');
	}

	private function loadAdapter(ContainerBuilder $container): void
	{
		$container->register('webchemistry.imageStorage.adapter', SimpleAdapter::class)
			->setAutowired(true);

		$container->setAlias(AdapterInterface::class, 'webchemistry.imageStorage.adapter');
	}

	private function loadFilters(ContainerBuilder $container): void
	{
		$container->registerForAutoconfiguration(ImageFilterInterface::class)
			->addTag(self::FILTER_TAG);
	}

}