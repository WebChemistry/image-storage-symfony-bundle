<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\SymfonyBundle\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use WebChemistry\ImageStorage\Doctrine\Annotation\AnnotationScopeProvider;
use WebChemistry\ImageStorage\File\FileFactory;
use WebChemistry\ImageStorage\File\FileFactoryInterface;
use WebChemistry\ImageStorage\Filesystem\FilesystemInterface;
use WebChemistry\ImageStorage\Filesystem\LocalFilesystem;
use WebChemistry\ImageStorage\Filter\FilterProcessorInterface;
use WebChemistry\ImageStorage\ImageStorageInterface;
use WebChemistry\ImageStorage\ImagineFilters\FilterProcessor;
use WebChemistry\ImageStorage\ImagineFilters\OperationInterface;
use WebChemistry\ImageStorage\ImagineFilters\OperationRegistryInterface;
use WebChemistry\ImageStorage\LinkGenerator\LinkGenerator;
use WebChemistry\ImageStorage\LinkGeneratorInterface;
use WebChemistry\ImageStorage\PathInfo\PathInfoFactory;
use WebChemistry\ImageStorage\PathInfo\PathInfoFactoryInterface;
use WebChemistry\ImageStorage\Resolver\BucketResolverInterface;
use WebChemistry\ImageStorage\Resolver\BucketResolvers\BucketResolver;
use WebChemistry\ImageStorage\Resolver\FileNameResolverInterface;
use WebChemistry\ImageStorage\Resolver\FileNameResolvers\PrefixFileNameResolver;
use WebChemistry\ImageStorage\Resolver\FilterResolverInterface;
use WebChemistry\ImageStorage\Resolver\FilterResolvers\OriginalFilterResolver;
use WebChemistry\ImageStorage\Storage\ImageStorage;

final class ImageStorageExtension extends Extension
{

	/**
	 * @param string[] $configs
	 */
	public function load(array $configs, ContainerBuilder $container): void
	{
		$this->loadFilesystem($container);
		$this->loadResolvers($container);
		$this->loadPathInfo($container);
		$this->loadFile($container);
		$this->loadDoctrine($container);

		if (interface_exists(OperationInterface::class)) {
			$this->loadImagineExtension($container);
		}

		$container->register('webchemistry.imageStorage.storage', ImageStorage::class)
			->setAutowired(true);

		$container->setAlias(ImageStorageInterface::class, 'webchemistry.imageStorage.storage');

		$container->register('webchemistry.imageStorage.linkGenerator', LinkGenerator::class)
			->setAutowired(true);

		$container->setAlias(LinkGeneratorInterface::class, 'webchemistry.imageStorage.linkGenerator');
	}

	private function loadResolvers(ContainerBuilder $container): void
	{
		$container->register('webchemistry.imageStorage.resolvers.bucket', BucketResolver::class)
			->setAutowired(true);

		$container->setAlias(BucketResolverInterface::class, 'webchemistry.imageStorage.resolvers.bucket');

		$container->register('webchemistry.imageStorage.resolvers.name', PrefixFileNameResolver::class)
			->setAutowired(true);

		$container->setAlias(FileNameResolverInterface::class, 'webchemistry.imageStorage.resolvers.name');

		$container->register('webchemistry.imageStorage.resolvers.filter', OriginalFilterResolver::class)
			->setAutowired(true);

		$container->setAlias(FilterResolverInterface::class, 'webchemistry.imageStorage.resolvers.filter');
	}

	private function loadImagineExtension(ContainerBuilder $container): void
	{
		$container->register('webchemistry.imageStorage.imagine.filterProcessor', FilterProcessor::class)
			->setAutowired(true);

		$container->setAlias(FilterProcessorInterface::class, 'webchemistry.imageStorage.imagine.filterProcessor');

		$container->register('webchemistry.imageStorage.imagine.operationRegistry', OperationRegistryInterface::class)
			->setAutowired(true);

		$container->setAlias(OperationRegistryInterface::class, 'webchemistry.imageStorage.imagine.operationRegistry');
	}

	private function loadDoctrine(ContainerBuilder $container): void
	{
		$container->register('webchemistry.imageStorage.doctrine.annotations.imageScope', AnnotationScopeProvider::class)
			->setAutowired(true);

		$container->setAlias(AnnotationScopeProvider::class, 'webchemistry.imageStorage.doctrine.annotations.imageScope');
	}

	private function loadFilesystem(ContainerBuilder $container): void
	{
		$container->register('webchemistry.imageStorage.filesystem', LocalFilesystem::class)
			->setArgument(0, '%kernel.project_dir%/public');

		$container->setAlias(FilesystemInterface::class, 'webchemistry.imageStorage.filesystem');
	}

	private function loadPathInfo(ContainerBuilder $container): void
	{
		$container->register('webchemistry.imageStorage.pathInfoFactory', PathInfoFactory::class)
			->setAutowired(true);

		$container->setAlias(PathInfoFactoryInterface::class, 'webchemistry.imageStorage.pathInfoFactory');
	}

	private function loadFile(ContainerBuilder $container): void
	{
		$container->register('webchemistry.imageStorage.fileFactory', FileFactory::class)
			->setAutowired(true);

		$container->setAlias(FileFactoryInterface::class, 'webchemistry.imageStorage.fileFactory');
	}

}
