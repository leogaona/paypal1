<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Service\PayPal;
use App\Models\Order;
use DB;


class PayPalController extends Controller
{
    //
    public function create(Request $request){
        $data = json_decode($request->getContent(), true);

        //inicializar
        $provider = \PayPal::setProvider(); 
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        $provider->setAccessToken($token);

        $price = Order::getProductPrice($data['value']);
        $description = Order::getProductDescription($data['value']);


        //crear orden
        $order = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $price
                    ],
                    "description" => $description

                ]

            ]
        ]);

        //guardar datos
        Order::create([
            'price' => $price,
            'description' => $description,
            'status' => $order['status'],
            'reference_number' => $order['id']
        ]);


        return response()->json($order);
    }
    public function capture(Request $request){
        $data = json_decode($request->getContent(), true);
        $orderId = $data['orderId'];
        
        //inicializar
        $provider = \PayPal::setProvider(); 
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        $provider->setAccessToken($token);


        $result = $provider->capturePaymentOrder($orderId);

        //update database
        if($result['status'] == "COMPLETED"){
            DB::table('orders')
                ->where('reference_number', $result['id'])
                ->update(['status' => 'COMPLETED', 'updated_at' => \Carbon\Carbon::now()]);
        }

        return response()->json($result);
    }
}
