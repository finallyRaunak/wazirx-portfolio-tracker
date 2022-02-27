<?php

namespace BalanceSheet;

use Exception;
use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Exceptions\PhpfastcacheDriverCheckException;
use Phpfastcache\Exceptions\PhpfastcacheDriverException;
use Phpfastcache\Exceptions\PhpfastcacheDriverNotFoundException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException;
use Phpfastcache\Exceptions\PhpfastcacheLogicException;
use ReflectionException;

/**
 * WazirX lib final class to fetch data from WazirX.
 * It is a singleton class to protect from multiple instance.
 *
 * @author Raunak Gupta
 * @version 1.0.0
 */
final class Wrx
{
    private static $instances = [];

    private $apiKey;

    private $apiSecret;

    private $apiURL = 'https://api.wazirx.com';

    private $cacheAdapter;

    private $fields = [];

    private $headers = [];

    private $loopCtr = 0;

    /**
     * @throws PhpfastcacheDriverNotFoundException
     * @throws PhpfastcacheInvalidConfigurationException
     * @throws PhpfastcacheDriverCheckException
     * @throws PhpfastcacheLogicException
     * @throws ReflectionException
     * @throws PhpfastcacheDriverException
     * @throws PhpfastcacheInvalidArgumentException
     */
    protected function __construct(string $apiKey = null, string $apiSecret = null)
    {
        CacheManager::setDefaultConfig(new ConfigurationOption([
                    'path' => __DIR__.DIRECTORY_SEPARATOR.'cache',
                    'defaultChmod' => 0755,
                    'itemDetailedDate' => true,
        ]));
        $this->cacheAdapter = CacheManager::getInstance('files');
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone()
    {
    }

    /**
     * Singletons should not be restorable from strings.
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception('Cannot unserialize a singleton.');
    }

    /**
     * @return mixed|static return instance of Wrx class
     */
    public static function getInstance(string $apiKey = null, string $apiSecret = null)
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static($apiKey, $apiSecret);
        }

        return self::$instances[$cls];
    }

    /**
     * Method to return all the funds/crypto present into WazirX Wallet.
     *
     * @return array
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws PhpfastcacheInvalidArgumentException
     */
    public function getFunds(): array
    {
        $cacheKey = md5($this->apiKey).'-funds';

        $this->fields = [
            'timestamp' => round(microtime(true) * 1000),
            'recvWindow' => 60000,
        ];

        $this->fields['signature'] = hash_hmac('sha256', http_build_query($this->fields), $this->apiSecret);

        $this->headers = [
            'X-Api-Key: '.$this->apiKey,
            'Content-Type: application/x-www-form-urlencoded',
        ];

        $this->request('/sapi/v1/funds', $cacheKey, MY_WALLET_TTL);

        return $this->removeDust($cacheKey);
    }

    /**
     * Method to remove and return only the non-empty crypto present into WazirX Wallet.
     *
     * @param string $cacheKey
     *
     * @return array
     *
     * @throws PhpfastcacheInvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function removeDust(string $cacheKey): array
    {
        $cachedStr = $this->cacheAdapter->getItem($cacheKey.'-actual');

        if ($cachedStr->isHit()) {
            return $cachedStr->get();
        }

        $wallet = [];
        $funds = $this->cacheAdapter->getItem($cacheKey);
        $array = $funds->get();
        foreach ($array as $arr) {
            if (empty((float) $arr['free']) && empty((float) $arr['locked'])) {
                continue;
            }
            $wallet[$arr['asset']] = [
                'total' => $arr['free'] + $arr['locked'],
                'free' => $arr['free'],
                'locked' => $arr['locked'],
            ];
        }
        $cachedStr->set($wallet)->expiresAfter(6000); //in seconds, also accepts Datetime
        $this->cacheAdapter->save($cachedStr);

        return $wallet;
    }

    /**
     * Method to return current market rate of a specific trading pair like BTCINR, BTTCUSDT, etc., from WazirX.
     *
     * @param string $symbol
     * @return array
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheLogicException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function ticker(string $symbol): array
    {
        $cacheKey = 'ticker-'.$symbol;
        $this->fields = [
            'symbol' => $symbol,
        ];
        $this->headers = [];

        return $this->request('/sapi/v1/ticker/24hr', $cacheKey, TICKER_TTL);
    }

    /**
     * Method to return current market rate of all the trading pair from WazirX.
     *
     * @return array
     *
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheLogicException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function tickers(): array
    {
        $cacheKey = 'tickers';
        $this->fields = $this->headers = [];
        $tickers = $this->request('/sapi/v1/tickers/24hr', $cacheKey, TICKERS_TTL);

        $this->checkAndSetTradingPairsList($tickers);

        return $tickers;
    }

    /**
     * Method to return all the orders of a specific trading pair like BTCINR, BTTCUSDT, etc., from WazirX.
     *
     * @param string $symbol
     * @return array
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheLogicException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getOrders(string $symbol): array
    {
        $cacheKey = md5($this->apiKey).'-'.$symbol.'-order';

        $this->fields = [
            'symbol' => $symbol,
            'timestamp' => round(microtime(true) * 1000),
            'recvWindow' => 60000,
            'limit' => 1000,
        ];
        $encodedPayload = hash_hmac('sha256', http_build_query($this->fields), $this->apiSecret);

        $this->fields['signature'] = $encodedPayload;

        $this->headers = [
            'X-Api-Key: '.$this->apiKey,
            'Content-Type: application/x-www-form-urlencoded',
        ];

        return $this->request('/sapi/v1/allOrders', $cacheKey, MY_ORDER_TTL);
    }

    /**
     * Method to finally make a CURL call to WazirX server.
     *
     * @param string $path URI
     * @param string $cacheKey name of the cache
     * @param int $duration number of minutes to cache a response
     *
     * @return array
     *
     * @throws PhpfastcacheInvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws Exception
     */
    private function request(string $path, string $cacheKey, int $duration = 10): array
    {
        $cachedStr = $this->cacheAdapter->getItem($cacheKey);

        if ($cachedStr->isHit()) {
            return $cachedStr->get();
        }
        sleep(2);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiURL.$path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        if (!empty($this->headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }

        $body = curl_exec($ch);

        // extract header
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($body, 0, $headerSize);
        $header = getHeaders($header);

        // extract body
        $content = substr($body, $headerSize);

        $error = curl_error($ch);
        curl_close($ch);

        if (!empty($error)) {
            throw new Exception($error);
        }
        $response = json_decode($content, 1);

        $this->hasValideCredentials($header, $response);

        if ($this->hasTooManyRequest($header, $response)) {
            $this->loopCtr += 1;
            if ($this->loopCtr >= 3) {
                throw new Exception($error, 999);
            }
            sleep(2);
//            pr($this->loopCtr, 1, '$this->loopCtr');
            $this->request($path, $cacheKey); //re-calling the method
        }

        $this->saveCache($cachedStr, $response, $duration);

        return $response;
    }

    /**
     * Method to cache a response.
     *
     * @param $cachedInst
     * @param array $payload data/value which needs to be cached
     * @param int $duration number of minutes to cache a response
     *
     * @return void
     *
     * @throws Exception
     */
    private function saveCache($cachedInst, array $payload, int $duration): void
    {
        $objTimeZone = new \DateTimeZone('Asia/Kolkata');
        $objDateTime = new \DateTime('now', $objTimeZone);
        $expiration = clone $objDateTime;
        $expiration->modify("+ {$duration} minutes");

        $cachedInst->set($payload)
                ->setCreationDate($objDateTime)
                ->setExpirationDate($expiration);

        $this->cacheAdapter->save($cachedInst);
    }

    /**
     * Method to check if the API request is being blocked by WazirX server or not.
     *
     * @param array $header
     * @param array $response
     *
     * @return bool
     */
    private function hasTooManyRequest(array $header, array $response): bool
    {
        if ((!empty($response['code']) && ($response['code'] == 2136)) || (!empty($header['http_code'])) && ($header['http_code'] == 429)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $header
     * @param array $response
     *
     * @return void
     *
     * @throws Exception
     */
    private function hasValideCredentials(array $header, array $response): void
    {
        if ((!empty($response['code']) && ($response['code'] == 2112)) && (!empty($header['http_code'])) && ($header['http_code'] == 400)) {
            throw new Exception($response['message'], $response['code']);
        }
        if ((!empty($response['code']) && ($response['code'] == 2005)) && (!empty($header['http_code'])) && ($header['http_code'] == 401)) {
            throw new Exception('Please provide a valid API Secret.', $response['code']);
        }
    }

    protected function checkAndSetTradingPairsList($tickers): void
    {
        $cachedStr = $this->cacheAdapter->getItem('wrx-trading-pairs');

        if (!$cachedStr->isHit()) {
            $tp = array_column($tickers, 'symbol');
            $this->saveCache($cachedStr, $tp, TRADING_PAIR_TTL);
        }
    }

    public function isValidTradingPair(string $symbol): bool
    {
        $cachedStr = $this->cacheAdapter->getItem('wrx-trading-pairs');
        if ($cachedStr->isHit()) {
            $tp = $cachedStr->get();

            return in_array($symbol, $tp) ? true : false;
        }

        return true;
    }
}
