<?php

namespace Thuleen\Ssomfa\Composers;

use Illuminate\View\View;
use React\EventLoop\Loop;


class ContractComposer
{
    protected $contractAddress;
    protected $gethRpcUrl;

    public function __construct()
    {
        $this->contractAddress = env('MFA_ADDRESS');
        $this->gethRpcUrl = env('GETH_RPC_URL');
        $this->listenToContractEvents();
    }

    public function compose(View $view)
    {
        $contractAddress = $this->contractAddress;
        $view->with(compact('contractAddress'));
    }

    protected function listenToContractEvents()
    {
        Loop::addPeriodicTimer(7, function () {
            $url = 'http://localhost:9000/version'; // The URL of your REST API
            $ch = curl_init($url);

            // Execute the cURL request
            $response = curl_exec($ch);

            // Check for cURL errors and handle the response as needed
            if ($response === false) {
                echo 'cURL error: ' . curl_error($ch);
            } else {
                // Handle the response here
                echo 'API Response: ' . $response;
            }

            curl_close($ch);
        });
    }
}
