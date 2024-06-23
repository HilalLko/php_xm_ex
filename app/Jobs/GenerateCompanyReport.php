<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateCompanyReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $companyService;
    protected $email;
    protected $companySymbol;
    protected $startDate;
    protected $endDate;
    protected $companyName;
    
    /**
     * Create a new job instance.
     */
    public function __construct($companyService, String $email, String $companySymbol, String $startDate, String $endDate, String $companyName)
    {
        $this->companyService = $companyService;
        $this->email = $email;
        $this->companySymbol = $companySymbol;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->companyName = $companyName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filteredData = $this->companyService->fetchHistoricalData($this->companySymbol, $this->startDate, $this->endDate);

        if(is_array($filteredData)) {
            $this->companyService->sendHistoricalDataEmail($this->companyName, $this->startDate, $this->endDate, $this->email, $filteredData);
        } else {
            $this->companyService->sendFailureEmail($this->companyName, $this->startDate, $this->endDate, $this->email);
        }
    }
}
