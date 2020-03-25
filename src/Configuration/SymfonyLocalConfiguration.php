<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\SymfonyBundle\Configuration;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SymfonyLocalConfiguration
{

	private RequestStack $requestStack;
	private string $projectDir;

	public function __construct(string $projectDir, RequestStack $requestStack)
	{
		$this->projectDir = $projectDir;
		$this->requestStack = $requestStack;
	}

	public function getFilesystem(): FilesystemInterface
	{
		$adapter = new Local($this->projectDir . '/public');

		return new Filesystem($adapter);
	}

	public function getBaseUrl(): string
	{
		return $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
	}

}
