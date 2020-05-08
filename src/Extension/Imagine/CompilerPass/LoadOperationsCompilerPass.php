<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\SymfonyBundle\Extension\Imagine\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class LoadOperationsCompilerPass implements CompilerPassInterface
{

	public const OPERATION_TAG = 'webchemistry.imageStorage.imagine.operation';

	public function process(ContainerBuilder $container): void
	{
		$loader = $container->getDefinition('webchemistry.imageStorage.imagine.operationRegistry');

		foreach ($container->findTaggedServiceIds(self::OPERATION_TAG) as $serviceId => $tags) {
			$loader->addMethodCall('add', [new Reference($serviceId)]);
		}
	}

}
