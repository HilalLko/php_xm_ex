<?php

namespace Tests\Unit;

use Tests\TestCase;
// use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\StockDataMail;


class CampanyFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testHistoricalDataEndpoint()
    {
        Mail::fake();

        Http::fake([
            'https://yh-finance.p.rapidapi.com/stock/v3/get-historical-data*' => Http::response([
                'prices' => [
                    ['date' => strtotime('2024-05-01'), 'open' => 150, 'high' => 155, 'low' => 148, 'close' => 152, 'volume' => 1000000],
                    ['date' => strtotime('2024-05-02'), 'open' => 153, 'high' => 158, 'low' => 151, 'close' => 157, 'volume' => 1100000],
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/stock/historical-data', [
            'company_symbol' => 'AAPL',
            'start_date' => '2024-05-01',
            'end_date' => '2024-05-31',
            'email' => 'user@example.com',
        ]);

        $response->assertStatus(200);

        Mail::assertSent(StockDataMail::class, function ($mail) {
            return $mail->hasTo('user@example.com') &&
                   $mail->companyName === 'Apple Inc.' &&
                   $mail->startDate === '2024-05-01' &&
                   $mail->endDate === '2024-05-31';
        });
    }
}
