<?php

namespace BalanceSheet;

use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use Phpfastcache\Exceptions\PhpfastcacheLogicException;

class BalanceSheet
{
    public function index()
    {
        $this->render_view('wrx', [
            'csrf_token' => generateCSRFToken(),
        ]);
    }

    /**
     * @param string $apiKey User's WazirX API Key
     * @param string $apiSecret User's WazirX API Secret
     * @param array $tradingPair List of trading pair like INR, USDT, etc
     *
     * @return void
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws PhpfastcacheInvalidArgumentException
     */
    public function listOrders(string $apiKey, string $apiSecret, array $tradingPair): void
    {
        try {
            $wrx = Wrx::getInstance($apiKey, $apiSecret);
            $fundsArr = $wrx->getFunds();
            $inrBal = !empty($fundsArr['inr']['free']) ? round($fundsArr['inr']['free'], 2, PHP_ROUND_HALF_UP) : 0;

            $walletCrypto = array_keys($fundsArr);
            $cryptoMarket = $this->getMarketPrice($walletCrypto, $tradingPair);
            $walletInfo = $this->getAssociativeOrders($apiKey, $apiSecret, $walletCrypto, $tradingPair);

            $wholeData = array_merge_recursive($cryptoMarket, $walletInfo);

            $output = $this->calcProfitNLoss($wholeData);

            $this->jsonToTable($output, $inrBal);
        } catch (\Exception $ex) {
            if (in_array($ex->getCode(), [2112, 2005])) {
                $this->render_view('wrx', [
                    'alert_message' => $ex->getMessage(),
                    'has_error' => true,
                    'csrf_token' => generateCSRFToken(),
                ]);
            }
        }
    }

    /**
     * @param array $cryptoList
     * @param array $tradingPair
     *
     * @return array
     *
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheLogicException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getMarketPrice(array $cryptoList, array $tradingPair): array
    {
        $wrx = Wrx::getInstance();
        $price = [];
        $tickers = $wrx->tickers();

        foreach ($cryptoList as $crypto) {
            if ($crypto == 'inr') {
                continue;
            }
            foreach ($tradingPair as $base) {
                if ($crypto == 'wrx' && $base == 'wrx') {
                    continue;
                }
                $location = array_search($crypto.$base, array_column($tickers, 'symbol'));
                $price[$crypto.'/'.$base]['current_price'] = $tickers[$location]['bidPrice'];
            }
        }

        return $price;
    }

    /**
     * @param array $cryptoList list of crypto symbol
     * @param string $base
     *
     * @return array
     *
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheLogicException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAssociativeOrders(string $apiKey, string $apiSecret, array $cryptoList, array $tradingPair): array
    {
        $wrx = Wrx::getInstance($apiKey, $apiSecret);
        $price = [];
        foreach ($cryptoList as $crypto) {
            if ($crypto == 'inr') {
                continue;
            }
            $temp = $this->pullOrders($wrx, $crypto, $tradingPair);
            $price = array_merge($price, $temp);
        }

        return $price;
    }

    private function pullOrders($wrx, string $crypto, array $tradingPair)
    {
        $price = [];
        foreach ($tradingPair as $base) {
            if (!$wrx->isValidTradingPair($crypto.$base)) {
                continue;
            }
            $orders = $wrx->getOrders($crypto.$base);
            $qty = $invest = 0;
            if (empty($orders) || (!empty($orders['code']) && ($orders['code'] == 1999))) {
                continue;
            }
            foreach ($orders as $order) {
                if (in_array($order['status'], ['cancel', 'wait'])) {
                    continue;
                }
                if ($order['side'] == 'buy') {
                    $qty += $order['executedQty'];
                    $invest += ($order['executedQty'] * $order['price']);
                } elseif ($order['side'] == 'sell') {
                    $qty -= $order['executedQty'];
                    $invest -= ($order['executedQty'] * $order['price']);
                }

                //if after buy and sell qty is zero then resetting the var
                if ($qty == 0) {
                    $qty = $invest = 0;
                }
            }
            //if quantity is nil then exclude it.
            if (empty($qty)) {
                continue;
            }
            $price[$crypto.'/'.$base] = [
                'dca' => $invest / $qty,
                'qty' => $qty,
            ];
            $price[$crypto.'/'.$base]['investment'] = $price[$crypto.'/'.$base]['dca'] * $price[$crypto.'/'.$base]['qty'];
        }

        return $price;
    }

    /**
     * @param array $wholeData
     * @return array
     */
    public function calcProfitNLoss(array $wholeData): array
    {
        $finalFilterArr = [];
        foreach ($wholeData as $key => $val) {
            if (empty($val['qty'])) {
                continue;
            }
            $finalFilterArr[$key] = $val;
            $finalFilterArr[$key]['pl_per'] = round((($val['current_price'] - $val['dca']) / $val['dca']) * 100, 2, PHP_ROUND_HALF_UP);
            $finalFilterArr[$key]['pl_val'] = $val['current_price'] - $val['dca'];
        }

        return $finalFilterArr;
    }

    /**
     * @param array $invests
     *
     * @return void
     */
    public function jsonToTable(array $invests, float $usableBalance): void
    {
        $totalPer = $ctr = $totalProfit = 0;
        $cards = [];
        foreach ($invests as $symbol => $invest) {
            $curr = ($invest['qty'] * $invest['current_price']);
            $inv = ($invest['qty'] * $invest['dca']);
            $totalPer += $invest['pl_per'];
            $totalProfit += ($curr - $inv);
            $cards[] = [
                'ctr' => $ctr,
                'symbol' => $symbol,
                'curr' => $curr,
                'inv' => $inv,
                'returns' => ($curr - $inv),
                'pl_per' => $invest['pl_per'],
                'dca' => $invest['dca'],
                'current_price' => $invest['current_price'],
                'qty' => $invest['qty'],
            ];
            $ctr++;
        }
        $this->render_view('wrx', [
            'invests' => $cards,
            'avg_percentage' => round(($totalPer / $ctr), 2, PHP_ROUND_HALF_UP),
            'total_profit' => round($totalProfit, 2, PHP_ROUND_HALF_UP),
            'total_crypto' => $ctr,
            'wallet_balance' => $usableBalance,
            'csrf_token' => generateCSRFToken(),
        ]);
    }

    /**
     * @param string $view View file name
     * @param array $data data to pass on to view
     *
     * @return void
     */
    public function render_view(string $view, array $data = []): void
    {
        extract($data);
        require 'views/'.$view.'.php';
    }
}
