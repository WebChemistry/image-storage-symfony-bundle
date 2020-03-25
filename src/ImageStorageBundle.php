<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\SymfonyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use WebChemistry\ImageStorage\SymfonyBundle\Extension\ImageStorageExtension;

final class ImageStorageBundle extends Bundle
{

	protected function getContainerExtensionClass()
	{
		return ImageStorageExtension::class;
	}

}
