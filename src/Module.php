<?php

declare(strict_types=1);

namespace Axleus\Db;

final class Module
{
    public function getConfig(): array
    {
        $configProvider = new ConfigProvider();
        return [
            'service_manager' => $configProvider->getDependencyConfig(),
        ];
    }
}