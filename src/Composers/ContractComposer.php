<?php

namespace Thuleen\Ssomfa\Composers;

use Illuminate\View\View;
use Web3\Contract;
use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use React\EventLoop\Loop;


class ContractComposer
{
    protected $contractAddress;
    protected $gethRpcUrl;
    protected $contractAbi;
    protected $web3;
    protected $contract;

    public function __construct()
    {
        $this->contractAddress = env('MFA_ADDRESS');
        $this->gethRpcUrl = env('GETH_RPC_URL');
        $this->web3 = new Web3(new HttpProvider(new HttpRequestManager($this->gethRpcUrl)));

        $abiFilePath = config('ssomfa.smart-contract.contract_json');
        $abiJson = file_get_contents($abiFilePath);
        $abi = json_decode($abiJson, true);
        $this->contractAbi = $abi['abi'];
        $contract = new Contract($this->gethRpcUrl, $this->contractAbi);
        $this->contract = $contract->at($this->contractAddress);
        $this->listenToContractEvents();
    }

    public function compose(View $view)
    {
        try {
            $isContractLoaded = $this->isContractLoaded();
            $contractAddress = $this->contractAddress;
            $view->with(compact('isContractLoaded', 'contractAddress'));
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

    // protected function listenToContractEvents()
    // {
    //     // Loop::addPeriodicTimer(7, function () {
    //     // });

    //     $eventName = 'Registered';
    //     // $eventSignature = $this->web3->eth->abi->encodeEventSignature($this->contractAbi[$eventName]);
    //     $eventAbi = null;
    //     foreach ($this->contractAbi as $abiObject) {
    //         if (isset($abiObject['type']) && $abiObject['type'] === 'event' && $abiObject['name'] === $eventName) {
    //             $eventAbi = $abiObject;
    //             break;
    //         }
    //     }

    //     if ($eventAbi !== null) {
    //         // Now you can encode the event signature.
    //         $eventSignature = $this->web3->abi->encodeEventSignature($eventAbi);
    //         // Continue with your event listening logic.
    //         dump($eventSignature);
    //     } else {
    //         // Handle the case where the event was not found in the ABI.
    //         echo "Event not found in ABI.";
    //     }
    // }

    protected function listenToContractEvents()
    {
        $eventName = 'Registered';

        $eventAbi = null;
        foreach ($this->contractAbi as $abiObject) {
            if (isset($abiObject['type']) && $abiObject['type'] === 'event' && $abiObject['name'] === $eventName) {
                $eventAbi = $abiObject;
                break;
            }
        }

        if ($eventAbi !== null) {
            // Encode the event signature using web3.php
            $eventSignature = $this->web3->eth->abi->encodeEventSignature($eventAbi);

            // Specify the filter options to get the logs for the "Registered" event.
            $filterOptions = [
                'address' => $this->contractAddress,
                'topics' => [$eventSignature],
            ];

            // Use the getLogs method to retrieve the event logs.
            $logs = $this->web3->eth->getLogs($filterOptions);

            // Process the logs.
            foreach ($logs as $log) {
                // Decode the log data to get the event data.
                $eventData = $this->web3->eth->abi->decodeLog($eventAbi, $log);
                // Handle the event data as needed.
                dump($eventData);
            }
        } else {
            // Handle the case where the event was not found in the ABI.
            echo "Event not found in ABI.";
        }
    }
}
