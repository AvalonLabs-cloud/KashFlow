<?php
namespace App\Http\Middleware\Services\ShapeValueVerifiers;

use App\ClientProvider\FlutterwaveClient;
use Illuminate\Support\Facades\Context;

class VerifyDataBillShapeValue
{
    public function __construct(
        public FlutterwaveClient $flutterwaveClient
    ) {}

    public function execute(array $data)
    {
        $result =  $this->flutterwaveClient->validateCustomerDetailsForBillPayment(itemCode: $data['itemCode'], customerId: $data['phoneNumber']);
        if ($result['status'] !== 'success') {
            throw new \InvalidArgumentException(
                'Invalid data bill request data'
            );
        }
        Context::add('billerCode', $result['data']['biller_code']);
    }
}
