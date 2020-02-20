<?php

namespace AssetManagerTest\Service;

use AssetManager\Resolver\AggregateResolver;
use AssetManager\Resolver\ResolverInterface;
use AssetManager\Service\AssetCacheManager;
use AssetManager\Service\AssetFilterManager;
use AssetManager\Service\AssetManager;
use AssetManager\Service\AssetManagerServiceFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class AssetManagerServiceFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $assetFilterManager = new AssetFilterManager();
        $assetCacheManager  = $this->getMockBuilder(AssetCacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            AggregateResolver::class,
            $this->getMockBuilder(ResolverInterface::class)->getMock()
        );

        $serviceManager->setService(
            AssetFilterManager::class,
            $assetFilterManager
        );

        $serviceManager->setService(
            AssetCacheManager::class,
            $assetCacheManager
        );

        $serviceManager->setService('config', [
            'asset_manager' => [
                'Dummy data',
                'Bacon',
            ],
        ]);

        $factory = new AssetManagerServiceFactory();
        $this->assertInstanceOf(AssetManager::class, $factory->createService($serviceManager));
    }
}
