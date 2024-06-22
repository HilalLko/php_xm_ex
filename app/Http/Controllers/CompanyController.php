<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\CompanyService;
use App\Rules\ValidSymbol;

class CompanyController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * @OA\Post(
     *     path="/api/stock/historical-data",
     *     summary="Get historical stock data",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="company_symbol", type="string", example="AAPL"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2023-01-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2023-01-31"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function getHistoricalData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_symbol' => ['required','string', new ValidSymbol],
            'start_date' => 'required|date|date_format:Y-m-d|before_or_equal:end_date|before_or_equal:today',
            'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date|before_or_equal:today',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $companySymbol = $request->input('company_symbol');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $email = $request->input('email');        

         // Get company name from the validation rule data
        $companyName = collect(json_decode(file_get_contents('https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json'), true))
            ->firstWhere('Symbol', $companySymbol)['Company Name'];

        $filteredData = $this->companyService->fetchHistoricalData($companySymbol, $startDate, $endDate);

        if(!is_array($filteredData)) {
            return response()->json(['message' => $filteredData]);
        }

        $this->companyService->sendHistoricalDataEmail($company['Company Name'], $startDate, $endDate, $email, $filteredData);

        return response()->json(['message' => 'Historical data sent to email']);
    }
}
