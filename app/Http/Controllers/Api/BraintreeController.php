<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Userimage;
use App\Service;
use App\Http\Requests;
use App\Http\Requests\ImageuploadRequest;
use App\Http\Requests\ImagelistRequest;
use Tymon\JWTAuth\JWTAuth;
use JWTAuthException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Facades\Voyager;
use Intervention\Image\Facades\Image;
use Intervention\Image\Constraint;
use App\Userlike;
      
use Braintree_Transaction;
use Braintree_Customer;
use Braintree_WebhookNotification;
use Braintree_Subscription;
use Braintree_CreditCard;
use Braintree_ClientToken;
use Braintree_Configuration;

class BraintreeController extends Controller {

  

  public function __construct() {

  }

  public function addOrder()
  {
    $input = Input::all();
    $subscribed= false;
    if(isset($input['subscribed']))
    {
      $subscribed= true;
    }
    $customer_id = $this->registerUserOnBrainTree();
    echo 'customer id - '.$customer_id;/// Create card token
    $card_token = $this->getCardToken($customer_id,$input['cardNumber'],$input['cardExpiry'],$input['cardCVC']);
    echo 'card_token - '.$card_token;
    /// gateway will provide this plan id whenever you creat plans there
    $plan_id = '4hvg';
    $transction_id = $this->createTransaction($card_token,$customer_id,$plan_id,$subscribed);
    dd($transction_id);
  }
 
public function getClientToken(){
  $clientToken = Braintree_ClientToken::generate();
  return $clientToken;
}

public function getTransaction(Request $request){
  $nonceFromTheClient = $request->get('nonce');
  $amount = $request->get('amount');
  $result = Braintree_Transaction::sale([
  'amount' => $amount,
  'paymentMethodNonce' => $nonceFromTheClient,
  'options' => [
    'submitForSettlement' => True
  ]
  ]);

  return $result;
}

public function registerUserOnBrainTree() {
$result = Braintree_Customer::create(array(
'firstName' => 'Saurabh',
'lastName' => 'Sharma',
'email' => 'info@coding4developers.com',
'phone' => '9999999999'
));
if ($result->success) {
return $result->customer->id;
} else {
$errorFound = '';
foreach ($result->errors->deepAll() as $error) {
$errorFound .= $error->message . "<br />";
}
echo $errorFound ;
}
}
 

public function getCardToken($customer_id,$cardNumber,$cardExpiry,$cardCVC)
{
  $card_result = Braintree_CreditCard::create(array(
  //'cardholderName' => mysql_real_escape_string($_POST['full_name']),
  'number' => $cardNumber,
  'expirationDate' => trim($cardExpiry),
  'customerId' => $customer_id,
  'cvv' => $cardCVC
  ));
  if($card_result->success)
  {
    return $card_result->creditCard->token;
  }
  else {
    return false;
  }
}
 

public function createTransaction($creditCardToken,$customerId,$planId,$subscribed){
if($subscribed)
{
$subscriptionData = array(
'paymentMethodToken' => $creditCardToken,
'planId' => $planId
);
$this->cancelSubscription();
$subscription_result = Braintree_Subscription::create($subscriptionData);
echo 'Subscription id'. $subscription_result->subscription->id;
}
else {
$this->cancelSubscription();
}
$result = Braintree_Transaction::sale(
[
'customerId' => $customerId,
'amount' => 20,
'orderId' => '131231'
]
);
if ($result->success) {
return $result->transaction->id;
} else {
$errorFound = '';
foreach ($result->errors->deepAll() as $error1) {
$errorFound .= $error1->message . "<br />";
}
}
}
 

public function cancelSubscription()
{
$gateway_subscription_id = '';
if($gateway_subscription_id!='')
{
Braintree_Subscription::cancel($gateway_subscription_id);
}
}
 

//// for subscription Braintree_WebhookNotification
public function subscription()
{
try{
if(isset($_POST["bt_signature"]) && isset($_POST["bt_payload"])) {
$webhookNotification = Braintree_WebhookNotification::parse(
$_POST["bt_signature"], $_POST["bt_payload"]
);// $message =
// "[Webhook Received " . $webhookNotification->timestamp->format('Y-m-d H:i:s') . "] "
// . "Kind: " . $webhookNotification->kind . " | "
// . "Subscription: " . $webhookNotification->subscription->id . "\n";Log::info("msg " . Log::info("subscription " . json_encode($webhookNotification->subscription));
Log::info("transactions " . json_encode($webhookNotification->subscription->transactions));
Log::info("transactions_id " . json_encode($webhookNotification->subscription->transactions[0]->id));
Log::info("customer_id " . json_encode($webhookNotification->subscription->transactions[0]->customerDetails->id));
Log::info("amount " . json_encode($webhookNotification->subscription->transactions[0]->amount));
}
}
catch (\Exception $ex) {
Log::error("PaymentController::subscription() " . $ex->getMessage());
}
}
}
