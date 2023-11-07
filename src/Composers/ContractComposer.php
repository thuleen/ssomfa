<?php

namespace Thuleen\Ssomfa\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Web3\Contract;

class ContractComposer
{
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
            $abiFilePath = config('ssomfa.smart-contract.contract_json');
            $abiJson = file_get_contents($abiFilePath);
            $abi = json_decode($abiJson, true);
            $contractAbi = $abi['abi'];
            $contract = new Contract('http://127.0.0.1:8545', $contractAbi);
            $contract->at('0x73511669fd4dE447feD18BB79bAFeAC93aB7F31f');
            return true; // Contract is successfully loaded
        } catch (\Exception $e) {
            return false; // Contract loading failed
        }
    }
}
