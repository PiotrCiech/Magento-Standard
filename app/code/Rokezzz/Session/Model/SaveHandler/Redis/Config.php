<?php

declare(strict_types=1);

namespace Rokezzz\Session\Model\SaveHandler\Redis;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\State;
use Magento\Framework\Session\SaveHandler\Redis\Config as MagentoRedisConfig;
use Magento\Store\Model\ScopeInterface as StoreScopeInterface;

/**
 * Reads session/{redis|valkey}/* based on session/save from env.php.
 */
class Config extends MagentoRedisConfig
{
    private const SESSION_SAVE_VALKEY = 'valkey';

    public function __construct(
        private readonly DeploymentConfig $rokezzzDeploymentConfig,
        private readonly State $rokezzzAppState,
        private readonly ScopeConfigInterface $rokezzzScopeConfig
    ) {
        parent::__construct($rokezzzDeploymentConfig, $rokezzzAppState, $rokezzzScopeConfig);
    }

    public function getLogLevel()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('log_level'));
    }

    public function getHost()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('host'));
    }

    public function getPort()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('port'));
    }

    public function getDatabase()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('database'));
    }

    public function getPassword()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('password'));
    }

    public function getTimeout()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('timeout'));
    }

    public function getRetries()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('retries'));
    }

    public function getPersistentIdentifier()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('persistent_identifier'));
    }

    public function getCompressionThreshold()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('compression_threshold'));
    }

    public function getCompressionLibrary()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('compression_library'));
    }

    public function getMaxConcurrency()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('max_concurrency'));
    }

    public function getMinLifetime()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('min_lifetime'));
    }

    public function getDisableLocking()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('disable_locking'));
    }

    public function getBotLifetime()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('bot_lifetime'));
    }

    public function getBotFirstLifetime()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('bot_first_lifetime'));
    }

    public function getFirstLifetime()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('first_lifetime'));
    }

    public function getBreakAfter()
    {
        return $this->rokezzzDeploymentConfig->get(
            $this->path('break_after_' . $this->rokezzzAppState->getAreaCode())
        );
    }

    public function getLifetime()
    {
        if ($this->rokezzzAppState->getAreaCode() === \Magento\Framework\App\Area::AREA_ADMINHTML) {
            return (int)$this->rokezzzScopeConfig->getValue(self::XML_PATH_ADMIN_SESSION_LIFETIME);
        }

        return (int)$this->rokezzzScopeConfig->getValue(
            self::XML_PATH_COOKIE_LIFETIME,
            StoreScopeInterface::SCOPE_STORE
        );
    }

    public function getSentinelServers()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('sentinel_servers'));
    }

    public function getSentinelMaster()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('sentinel_master'));
    }

    public function getSentinelVerifyMaster()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('sentinel_verify_master'));
    }

    public function getSentinelConnectRetries()
    {
        return $this->rokezzzDeploymentConfig->get($this->path('sentinel_connect_retries'));
    }

    private function path(string $key): string
    {
        $backend = $this->rokezzzDeploymentConfig->get('session/save') === self::SESSION_SAVE_VALKEY
            ? 'valkey'
            : 'redis';

        return 'session/' . $backend . '/' . $key;
    }
}
