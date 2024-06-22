<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\ValidationException;
use App\Rules\ValidSymbol;

class ValidationTest extends TestCase
{
    protected $validator;


    public function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->app->make(ValidatorFactory::class);
    }

    public function testValidationSuccess(): void
    {
        $data = [
            'company_symbol' => 'AAPL',
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'email' => 'user@example.com',
        ];

        $rules = [
            'company_symbol' => ['required','string', new ValidSymbol],
            'start_date' => 'required|date|date_format:Y-m-d|before_or_equal:end_date|before_or_equal:today',
            'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date|before_or_equal:today',
            'email' => 'required|email',
        ];

        $validator = $this->validator->make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    public function testValidationFailure(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            'company_symbol' => 'AAXX',
            'start_date' => 'invalid-date',
            'end_date' => '2024-01-31',
            'email' => 'user@example.com',
        ];

        $rules = [
            'company_symbol' => ['required','string', new ValidSymbol],
            'start_date' => 'required|date|date_format:Y-m-d|before_or_equal:end_date|before_or_equal:today',
            'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date|before_or_equal:today',
            'email' => 'required|email',
        ];

        $validator = $this->validator->make($data, $rules);

        $validator->validate();
    }
}
