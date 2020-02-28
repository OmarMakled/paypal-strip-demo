<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\InputFields;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\WebProfile;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function paypalCreate(Request $request)
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                config('services.paypal.id'),
                config('services.paypal.secret')
            )
        );
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $item1 = new Item();
        $item1->setName('Ground Coffee 40 oz')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setSku("123123") // Similar to `item_number` in Classic API
            ->setPrice(7.5);
        $item2 = new Item();
        $item2->setName('Granola bars')
            ->setCurrency('USD')
            ->setQuantity(5)
            ->setSku("321321") // Similar to `item_number` in Classic API
            ->setPrice(2);

        $itemList = new ItemList();
        $itemList->setItems(array($item1, $item2));

        $details = new Details();
        $details->setShipping(1.2)
            ->setTax(1.3)
            ->setSubtotal(17.50);

        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal(20)
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl("http://laravel-paypal-example.test")
            ->setCancelUrl("http://laravel-paypal-example.test");

        $inputFields = new InputFields();
        $inputFields->setNoShipping(1);

        $webProfile = new WebProfile();
        $webProfile->setName('test' . uniqid())->setInputFields($inputFields);

        $webProfileId = $webProfile->create($apiContext)->getId();

        $payment = new Payment();
        $payment->setExperienceProfileId($webProfileId); // no shipping
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));

        $payment->create($apiContext);
        return $payment;
    }

    public function paypalExecute(Request $request)
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                config('services.paypal.id'),
                config('services.paypal.secret')
            )
        );

        $paymentId = $request->paymentID;
        $payment = Payment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($request->payerID);

        $result = $payment->execute($execution, $apiContext);
        return $result;
    }

    public function stripCreate(Request $request)
    {
        return response()->json(
            ['publishableKey' => config('services.stripe.public_key')]
        );
    }

    public function stripExecute(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));

        $intent = PaymentIntent::create([
            "amount" => 1400,
            "currency" => $request->currency,
            "payment_method" => $request->paymentMethodId,
            "confirmation_method" => "manual",
            "confirm" => true,
        ]);

        return response()->json($this->stripResponse($intent));
    }

    private function stripResponse($intent)
    {
        switch ($intent->status) {
            case "requires_action":
            case "requires_source_action":
                return [
                    'requiresAction' => true,
                    'paymentIntentId' => $intent->id,
                    'clientSecret' => $intent->client_secret,
                ];
            case "requires_payment_method":
            case "requires_source":
                return [
                    error => "Your card was denied, please provide a new payment method",
                ];
            case "succeeded":
                return ['clientSecret' => $intent->client_secret];
        }
    }

}
