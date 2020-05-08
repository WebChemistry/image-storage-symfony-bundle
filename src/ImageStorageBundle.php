<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\SymfonyBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WebChemistry\ImageStorage\ImagineFilters\OperationInterface;
use WebChemistry\ImageStorage\SymfonyBundle\Extension\Imagine\CompilerPass\LoadOperationsCompilerPass;
use WebChemistry\ImageStorage\SymfonyBundle\Extension\ImageStorageExtension;

final class ImageStorageBundle extends Bundle
{

	public function build(ContainerBuilder $container): void
	{
		parent::build($container);

		if (interface_exists(OperationInterface::class)) {
			$container->registerForAutoconfiguration(OperationInterface::class)
				->addTag(LoadOperationsCompilerPass::OPERATION_TAG);

			$container->addCompilerPass(new LoadOperationsCompilerPass());
		}
	}

	protected function getContainerExtensionClass(): string
	{
		return ImageStorageExtension::class;
	}

}
