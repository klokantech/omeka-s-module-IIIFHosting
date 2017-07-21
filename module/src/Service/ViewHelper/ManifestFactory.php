<?php
namespace IIIFHosting\Service\ViewHelper;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use IIIFHosting\View\Helper\Manifest;

class ManifestFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new Manifest();
    }
}
