<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\SymfonyBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WebChemistry\ImageStorage\ImagineFilters\ImageFilterInterface;
use WebChemistry\ImageStorage\SymfonyBundle\Extension\Imagine\CompilerPass\LoadFiltersCompilerPass;
use WebChemistry\ImageStorage\SymfonyBundle\Extension\Imagine\ImageStorageExtension;

final class ImageStorageBundle extends Bundle
{

	public const FILTER_TAG = 'webchemistry.imageStorage.filter';

	public function build(ContainerBuilder $container): void
	{
		parent::build($container);

		$container->registerForAutoconfiguration(ImageFilterInterface::class)
			->addTag(self::FILTER_TAG);

		$container->addCompilerPass(new LoadFiltersCompilerPass());
	}

	protected function getContainerExtensionClass(): string
	{
		return ImageStorageExtension::class;
	}

}
