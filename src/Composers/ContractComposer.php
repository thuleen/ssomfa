<?php

namespace Thuleen\Ssomfa\Composers;

use Illuminate\View\View;
use Web3\Utils;
use Web3\Contract;
use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;

class ContractComposer
{
    protected $contractAddress;
    protected $gethRpcUrl;
    protected $contractAbi;
    protected $web3;

    public function __construct()
    {
        $this->contractAddress = env('MFA_ADDRESS');
        $this->gethRpcUrl = env('GETH_RPC_URL');
        $this->web3 = new Web3(new HttpProvider(new HttpRequestManager($this->gethRpcUrl)));

        $abiFilePath = config('ssomfa.smart-contract.contract_json');
        $abiJson = file_get_contents($abiFilePath);
        $abi = json_decode($abiJson, true);
        $this->contractAbi = $abi['abi'];
        $this->listenToContractEvents();
    }

    public function compose(View $view)
    {
        try {
            $isContractLoaded = $this->isContractLoaded();
            $view->with(compact('isContractLoaded'));
        } catch (\Exception $e) {
            $isContractLoaded = false;
            $view->with(compact('isContractLoaded'));
        }
    }

    private function isContractLoaded()
    {
        try {
            $contract = new Contract($this->gethRpcUrl, $this->contractAbi);
            $contract->at($this->contractAddress);
            return true; // Contract is successfully loaded
        } catch (\Exception $e) {
            return false; // Contract loading failed
        }
    }

    protected function listenToContractEvents()
    {
        $contract = new Contract($this->gethRpcUrl, $this->contractAbi);
        $contract->at($this->contractAddress);
        // $contract->call('name', [], function ($err, $result) {
        //     if ($err !== null) {
        //         // Handle the error
        //         dd($err);
        //     } else {
        //         // Handle the result
        //         dd($result);
        //     }
        // });
        $eventName = 'Registered';
        $eventLogs = $contract->getEventLogs($eventName);
        dd($eventLogs);
    }
}
