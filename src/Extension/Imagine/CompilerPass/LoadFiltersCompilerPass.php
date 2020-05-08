<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\SymfonyBundle\Extension\Imagine\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use WebChemistry\ImageStorage\SymfonyBundle\ImageStorageBundle;

final class LoadFiltersCompilerPass implements CompilerPassInterface
{

	public function process(ContainerBuilder $container): void
	{
		$loader = $container->getDefinition('webchemistry.imageStorage.filterLoader');

		foreach ($container->findTaggedServiceIds(ImageStorageBundle::FILTER_TAG) as $serviceId => $tags) {
			$loader->addMethodCall('addFilter', [new Reference($serviceId)]);
		}
	}

}
