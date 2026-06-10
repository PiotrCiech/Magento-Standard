<?php

namespace Furgonetka\Pickups\Helper;

class HelperFunctions extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_config;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Module\ResourceInterface
     */
    protected $_moduleResource;

    /**
     * @var \Magento\Authorization\Model\RoleFactory
     */
    protected $_roleFactory;

    /**
     * @var \Magento\Authorization\Model\RulesFactory
     */
    protected $_rulesFactory;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\App\Cache\ManagerFactory
     */
    protected $_cacheManagerFactory;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * @param \Magento\Framework\App\Helper\Context              $context
     * @param \Magento\Config\Model\ResourceModel\Config         $config
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Message\ManagerInterface        $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\Module\ResourceInterface        $moduleResource
     * @param \Magento\Authorization\Model\RoleFactory           $roleFactory
     * @param \Magento\Authorization\Model\RulesFactory          $rulesFactory
     * @param \Magento\User\Model\UserFactory                    $userFactory
     * @param \Magento\Sales\Model\OrderFactory                  $orderFactory
     * @param \Magento\Framework\UrlInterface                    $urlBuilder
     * @param \Magento\Framework\App\Cache\ManagerFactory        $cacheManagerFactory
     * @param \Magento\Framework\Encryption\EncryptorInterface   $encryptor
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Config\Model\ResourceModel\Config $config,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\ResourceInterface $moduleResource,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\Authorization\Model\RulesFactory $rulesFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Cache\ManagerFactory $cacheManagerFactory,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {

        $this->_config = $config;
        $this->_scopeConfig = $scopeConfig;
        $this->_messageManager = $messageManager;
        $this->_storeManager = $storeManager;
        $this->_moduleResource = $moduleResource;
        $this->_roleFactory = $roleFactory;
        $this->_rulesFactory = $rulesFactory;
        $this->_userFactory = $userFactory;
        $this->_orderFactory = $orderFactory;
        $this->_urlBuilder = $urlBuilder;
        $this->_cacheManagerFactory = $cacheManagerFactory;
        $this->encryptor = $encryptor;

        parent::__construct($context);
    }


    /**
     * Saves configuration data
     *
     * @param string  $path
     * @param mixed   $value
     * @param string  $scope
     * @param integer $scopeId
     */
    public function saveConfig($path, $value, $scope = \Furgonetka\Pickups\Helper\Data::SCOPE_DEFAULT, $scopeId = 0)
    {
        $this->_config->saveConfig($path, $value, $scope, $scopeId);
    }

    public function deleteConfig($path, $scope = \Furgonetka\Pickups\Helper\Data::SCOPE_DEFAULT, $scopeId = 0)
    {
        $this->_config->deleteConfig($path, $scope, $scopeId);
    }

    /**
     * Gets configuration data
     *
     * @param string $path
     * @param string $scopeType
     *
     * @return string
     */
    public function getConfig($path, $scopeType = \Furgonetka\Pickups\Helper\Data::SCOPE_DEFAULT)
    {
        return $this->_scopeConfig->getValue($path, \Furgonetka\Pickups\Helper\Data::SCOPE_DEFAULT);
    }

    /**
     * @return string
     */
    public function getShopAuthUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl() . 'furgonetka/oauth/callback/';
    }

    public function getDomainOnly()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function generateApiPassword()
    {
        return \bin2hex(\openssl_random_pseudo_bytes(16));
    }


    /**
     * @param type string
     *
     * @return string
     */
    public function getModuleVersion()
    {
        return $this->_moduleResource->getDbVersion(\Furgonetka\Pickups\Helper\Data::MODULE_NAME);
    }

    /**
     * @param integer $orderId
     *
     * @return string
     */
    public function getOrderIncrementId($orderId)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $this->_orderFactory->create();
        $order->load($orderId);

        return $order->getIncrementId();
    }


    /**
     * @param array $types
     */
    public function clearCache(array $types)
    {

        /* @var $cacheManager \Magento\Framework\App\Cache\Manager */
        $cacheManager = $this->_cacheManagerFactory->create();
        $cacheManager->clean($types);
    }

    public function getOrderDetails($orderIncrementId)
    {
        $order = $this->_orderFactory->create();
        $order->loadByIncrementId($orderIncrementId);
        $pickupPointData = $order->getFurgonetkaPickupPoint();

        if ($pickupPointData) {
            $pickupPointData = \json_decode($pickupPointData, true);
        }

        return $pickupPointData;
    }


    public function getRestUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function createOrUpdateAdminUser()
    {
        $apiKey = $this->generateApiPassword();
        $user = $this->_userFactory->create()->loadByUsername(\Furgonetka\Pickups\Helper\Data::DEFAULT_ADMIN_USER);
        if (!is_bool($user) && $user->getId()) {
            $this->saveConfig(Data::CONFIG_API_PASSWORD, $this->encryptor->encrypt($apiKey));
            $user->setPassword($apiKey)->save();
        } else {
            $this->createNewAdmin($apiKey);
        }

        return ['api_key' => $apiKey];
    }

    private function createNewAdmin($apiKey)
    {
        $adminInfo = [
            'username' => \Furgonetka\Pickups\Helper\Data::DEFAULT_ADMIN_USER,
            'firstname' => 'furgonetka',
            'lastname' => 'api',
            'email' => 'furgonetka@furgonetka.pl',
            'password' => $apiKey,
            'interface_locale' => 'en_US',
            'is_active' => 1,
        ];

        $userModel = $this->_userFactory->create();
        $userModel->setData($adminInfo);
        $userModel->setRoleId(1);
        try {
            $userModel->save();
            $this->saveConfig(Data::CONFIG_API_PASSWORD, $this->encryptor->encrypt($apiKey));
        } catch (\Exception $ex) {
            $ex->getMessage();
        }
    }

    public function isConnected()
    {
        return ($this->getConfig(\Furgonetka\Pickups\Helper\Data::IS_TEST_MODE) && $this->getConfig(\Furgonetka\Pickups\Helper\Data::ACCESS_TOKEN));
    }

}