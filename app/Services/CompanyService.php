<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompanyDataMail;
use App\Mail\CompanyFailureMail;

class CompanyService
{
    protected $rapidApiKey;

    public function __construct($rapidApiKey)
    {
        $this->rapidApiKey = $rapidApiKey;
    }

    public function fetchHistoricalData($symbol, $startDate, $endDate): mixed
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Key' => $this->rapidApiKey,
            'X-RapidAPI-Host' => 'yh-finance.p.rapidapi.com'
        ])->get('https://yh-finance.p.rapidapi.com/stock/v3/get-historical-data', [
            'symbol' => $symbol,
            'region' => 'US'
        ]);
        
        $data = $response->json();
        
        if (!isset($data['prices'])) {
            return $data['message'];
        }
        return array_filter($data, function ($item) use ($startDate, $endDate) {
            $date = date('Y-m-d', $item['date']);
            return $date >= $startDate && $date <= $endDate;
        });
    }

    public function sendHistoricalDataEmail($companyName, $startDate, $endDate, $email, $filteredData): void
    {
        $csvData = "Date,Open,High,Low,Close,Volume\n";
        foreach ($filteredData as $row) {
            $csvData .= date('Y-m-d', $row['date']) . ",{$row['open']},{$row['high']},{$row['low']},{$row['close']},{$row['volume']}\n";
        }

        $filePath = storage_path('app/public/stock_data.csv');
        file_put_contents($filePath, $csvData);

        Mail::to($email)->send(new CompanyDataMail($companyName, $startDate, $endDate, $filePath));
    }

    public function sendFailureEmail($companyName, $startDate, $endDate, $email): void
    {
        Mail::to($email)->send(new CompanyFailureMail($companyName, $startDate, $endDate));
    }
}
