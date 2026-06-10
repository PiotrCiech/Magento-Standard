<?php

namespace Furgonetka\Pickups\Helper;

class FurgonetkaApi extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $helperFunctions;
    private $isTestMode;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * @var Magento\Framework\HTTP\Client\Curl
     */
    private $curl;

    /**
     * @param \Magento\Framework\App\Helper\Context            $context
     * @param HelperFunctions                                  $helperFunctions
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        HelperFunctions $helperFunctions,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->helperFunctions = $helperFunctions;
        $this->encryptor = $encryptor;
        $this->curl = $curl;
        parent::__construct($context);
        $this->isTestMode = ($this->helperFunctions->getConfig(\Furgonetka\Pickups\Helper\Data::IS_TEST_MODE) != 2);
        $this->curl->addHeader(
            'User-Agent',
            'magento2_' . $productMetadata->getVersion() . '_plugin_' . $this->helperFunctions->getModuleVersion()
        );
    }

    public function createIntegrationOAuthApplication($apiKey)
    {
        $data = [
            'type' => \Furgonetka\Pickups\Helper\Data::INTEGRATION_TYPE,
            'url' => $this->helperFunctions->getShopAuthUrl(),
            'data' => [
                'shopUrl' => $this->helperFunctions->getDomainOnly(),
                'username' => \Furgonetka\Pickups\Helper\Data::DEFAULT_ADMIN_USER,
                'apiKey' => $apiKey,
            ],
        ];

        $restApiUrl = $this->getRestApiUrl();
        $this->curl->post($restApiUrl . \Furgonetka\Pickups\Helper\Data::PATH_CREATE_OAUTH_APPLICATION, $data);

        $response = json_decode($this->curl->getBody(), true);

        if (empty($response['errors'])) {
            $this->helperFunctions->saveConfig(\Furgonetka\Pickups\Helper\Data::API_CLIENT_ID, $response['client_id']);
            $this->helperFunctions->saveConfig(\Furgonetka\Pickups\Helper\Data::API_CLIENT_SECRET,
                $response['client_secret']);
            $this->helperFunctions->clearCache(['config']);
            return [
                'client_id' => $response['client_id'],
                'client_secret' => $response['client_secret'],
            ];
        }
        return $response;
    }

    public function createTokenByOauth($code)
    {
        $clientId = $this->helperFunctions->getConfig(\Furgonetka\Pickups\Helper\Data::API_CLIENT_ID);
        $secret = $this->helperFunctions->getConfig(\Furgonetka\Pickups\Helper\Data::API_CLIENT_SECRET);

        $vars = [
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->helperFunctions->getShopAuthUrl(),
            'code' => urldecode($code),
        ];
        $hash = base64_encode(
            $clientId . ':' . $secret
        );
        $tokenRefreshUrl = $this->isTestMode ? \Furgonetka\Pickups\Helper\Data::TOKEN_REFRESH_URL_TEST : \Furgonetka\Pickups\Helper\Data::TOKEN_REFRESH_URL;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenRefreshUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            "Authorization: Basic {$hash}",
            "application/x-www-form-urlencoded",
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $server_output = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($server_output);
        if (isset($result->access_token) && $result->access_token) {
            $this->helperFunctions->saveConfig(\Furgonetka\Pickups\Helper\Data::ACCESS_TOKEN, $result->access_token);
            $this->helperFunctions->saveConfig(\Furgonetka\Pickups\Helper\Data::REFRESH_TOKEN, $result->refresh_token);
            $this->helperFunctions->clearCache(['config']);
            return $result->access_token;
        }
        return false;
    }

    public function refreshToken()
    {
        $clientId = $this->helperFunctions->getConfig(\Furgonetka\Pickups\Helper\Data::API_CLIENT_ID);
        $secret = $this->helperFunctions->getConfig(\Furgonetka\Pickups\Helper\Data::API_CLIENT_SECRET);

        $vars = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->helperFunctions->getConfig(\Furgonetka\Pickups\Helper\Data::REFRESH_TOKEN),
        ];
        $hash = base64_encode(
            $clientId . ':' . $secret
        );
        $tokenRefreshUrl = $this->isTestMode ? \Furgonetka\Pickups\Helper\Data::TOKEN_REFRESH_URL_TEST : \Furgonetka\Pickups\Helper\Data::TOKEN_REFRESH_URL;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenRefreshUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            "Authorization: Basic {$hash}",
            "application/x-www-form-urlencoded",
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $server_output = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($server_output);
        if (isset($result->access_token) && $result->access_token) {
            $this->helperFunctions->saveConfig(\Furgonetka\Pickups\Helper\Data::ACCESS_TOKEN, $result->access_token);
            $this->helperFunctions->saveConfig(\Furgonetka\Pickups\Helper\Data::REFRESH_TOKEN, $result->refresh_token);
            $this->helperFunctions->clearCache(['config']);
            return $result->access_token;
        }
        return false;
    }


    public function addIntegrationSource($access_token)
    {
        $params = [
            'type' => Data::INTEGRATION_TYPE,
            'version' => $this->helperFunctions->getModuleVersion(),
            'data' => [
                'shopUrl' => $this->helperFunctions->getDomainOnly(),
                'username' => \Furgonetka\Pickups\Helper\Data::DEFAULT_ADMIN_USER,
                'apiKey' => $this->encryptor->decrypt($this->helperFunctions->getConfig(Data::CONFIG_API_PASSWORD)),
            ]
        ];

        try {
            $restApiUrl = $this->getRestApiUrl();
            $this->curl->addHeader('Authorization', 'Bearer ' . $access_token);
            $this->curl->post($restApiUrl . \Furgonetka\Pickups\Helper\Data::PATH_CONFIGURATIONS, $params);

            $response = json_decode($this->curl->getBody(), true);

            if (!empty($response['sourceId'])) {
                $this->helperFunctions->saveConfig(Data::SOURCE_ID, $response['sourceId']);
                $this->helperFunctions->clearCache(['config']);
            }
            return $response;
        } catch (\Exception $e) {
            return false;
        }

    }

    public function getIframeLink($order)
    {
        return $this->createIframeLink($order);
    }

    protected function createIframeLink($order)
    {
        $data = $this->buildPackageFormData($order);

        $restApiUrl = $this->getRestApiUrl();
        $this->curl->addHeader(
            'Authorization',
            'Bearer ' .  $this->helperFunctions->getConfig(\Furgonetka\Pickups\Helper\Data::ACCESS_TOKEN)
        );
        $this->curl->addHeader('Content-Type', 'application/vnd.furgonetka.v1+json');
        $this->curl->post($restApiUrl . \Furgonetka\Pickups\Helper\Data::PATH_CREATE_FORM_URL, json_encode($data));
        $response = json_decode($this->curl->getBody(), true);
        return $response['url'];
    }

    private function buildPackageFormData($order)
    {
        $pickupPoint = json_decode($order->getFurgonetkaPickupPoint());
        $pointCode = isset($pickupPoint->point) ? $pickupPoint->point->code : null;
        $pointType = isset($pickupPoint->point) ? $pickupPoint->point->type : null;
        $shippingAddress = $order->getShippingAddress();
        $streetName = $shippingAddress->getStreet();
        $payment = $order->getPayment()->getMethodInstance();

        $params = [
            'sender' => [
                'name' => $this->getDefaultData('name'),
                'company' => $this->getDefaultData('company'),
                'street' => $this->getDefaultData('street'),
                'postcode' => $this->getDefaultData('postcode'),
                'city' => $this->getDefaultData('city'),
                'country_code' => $this->getDefaultData('country_code'),
                'email' => $this->getDefaultData('email'),
                'phone' => $this->getDefaultData('phone'),
            ],
            'receiver' => [
                'name' => $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname(),
                'company' => $shippingAddress->getCompany(),
                'street' => implode(' ', $streetName),
                'postcode' => $shippingAddress->getPostcode(),
                'city' => $shippingAddress->getCity(),
                'country_code' => $shippingAddress->getCountry(),
                'email' => $order->getCustomerEmail(),
                'phone' => $shippingAddress->getTelephone(),
                'point' => $pointCode,
            ],
            'partner_reference_number' => $order->getIncrementId(),
            'sale_source_id' => $this->helperFunctions->getConfig(\Furgonetka\Pickups\Helper\Data::SOURCE_ID),
            'service' => $pointType,
            'additional_services' => [],
            'type' => 'package',
            'parcels' => [
                [
                    'weight' => $order->getWeight(),
                ],
            ],
        ];

        $iban = $this->getDefaultData('iban');
        if (($payment->getCode() == 'cashondelivery') && $iban) {
            $params['additional_services']['cod'] = [
                'amount' => $order->getGrandTotal(),
                'iban' => $iban,
                'name' => $this->getDefaultData('name'),
            ];
        }
        return $params;
    }

    private function getDefaultData($fieldName)
    {
        return $this->helperFunctions->getConfig('furgonetka_pickups/seller/' . $fieldName);
    }

    public function getOauthUrl($clientId)
    {
        $url = $this->isTestMode ? \Furgonetka\Pickups\Helper\Data::OAUTHURL_TEST : \Furgonetka\Pickups\Helper\Data::OAUTHURL;
        $url .= '?response_type=code&client_id=' . $clientId . '&redirect_uri=' . $this->helperFunctions->getShopAuthUrl();
        return $url;
    }

    private function getRestApiUrl()
    {
        return $this->isTestMode ? \Furgonetka\Pickups\Helper\Data::TEST_API_REST_URL : \Furgonetka\Pickups\Helper\Data::API_REST_URL;
    }
}