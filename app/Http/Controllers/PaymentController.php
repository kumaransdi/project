<?php
   
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Omnipay\Omnipay;
use App\Models\Payment;
use Session;
   
class PaymentController extends Controller
{
   
    private $gateway;
   
    public function __construct()
    {
        //$this->middleware('auth', ['except'=>['createPayment', 'executePayment']],"Authorization: Bearer myaccesstokenishere");
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId(env('PAYPAL_CLIENT_ID'));
        $this->gateway->setSecret(env('PAYPAL_CLIENT_SECRET'));
        $this->gateway->setTestMode(true); //set it to 'false' when go live
    }
   
    /**
     * Call a view.
     */
    public function index()
    {
        return view('payment');
    }
    public function curlerror()
    {
        return view('curlerror');
    }
    public function success_url()
    {
        return view('success_url');
    }
    
    /**
     * Initiate a payment on PayPal.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function charge(Request $request)
    {
        if($request->input('submit'))
        {
            try {
                $response = $this->gateway->purchase(array(
                    'amount' => $request->input('amount'),
                    'currency' => env('PAYPAL_CURRENCY'),
                    'returnUrl' => url('success'),
                    'cancelUrl' => url('error'),
                ))->send();
            
                if ($response->isRedirect()) {
                    $response->redirect(); // this will automatically forward the customer
                } else {
                    // not successful
                    return $response->getMessage();
                }
            } catch(Exception $e) {
                return $e->getMessage();
            }
        }
    }
   
    /**
     * Charge a payment and store the transaction.
     *
     * @param  \Illuminate\Http\Request  $request
     */


   
    public function success(Request $request)
    {
        // Once the transaction has been approved, we need to complete it.
        if ($request->input('paymentId') && $request->input('PayerID'))
        {
            $transaction = $this->gateway->completePurchase(array(
                'payer_id'             => $request->input('PayerID'),
                'transactionReference' => $request->input('paymentId'),
            ));
            $response = $transaction->send();
           
            if ($response->isSuccessful())
            {
                // The customer has successfully paid.
                $arr_body = $response->getData();
           
                // Insert transaction data into the database
                $payment = new Payment;
                $payment->payment_id = $arr_body['id'];
                $payment->payer_id = $arr_body['payer']['payer_info']['payer_id'];
                $payment->payment_email = $arr_body['payer']['payer_info']['email'];
                $payment->amount = $arr_body['transactions'][0]['amount']['total'];
                $payment->currency = env('PAYPAL_CURRENCY');
                $payment->payment_status = $arr_body['state'];
                $payment->save();
           
                return "Payment is successful. Your transaction id is: ". $arr_body['id'];
            } else {
                return $response->getMessage();
            }
        } else {
            return 'Transaction is declined';
        }
    }
    
    public function userdetails(Request $request){
        $sessionId = Session::getId();
          echo "<pre>"; print_r('sessionIdssssss'); 
          echo "<pre>"; print_r($sessionId); //die;
        $request_data = array(
            'merchant_id'=>'1201',
            'username' => 'test',
            'password'=>stripslashes('test'), 'api_key'=>'jtest123', // in sandbox request
            'order_id'=>time(), // MIN 30 characters with strong unique function (like hashing function with time)
            'total_price'=>$request->get('amount'),
            'CurrencyCode'=>'KWD',//only works in production mode
            'CstFName'=>'Test Name',
            'CstEmail'=>'test@test.com',
            'CstMobile'=>'12345678',
            'payment_gateway'=>'knet',// only works in production mod
            'ProductName'=>json_encode(['computer','television']),
            'ProductQty'=>json_encode([2,1]),
            'ProductPrice'=>json_encode([150,1500]),
            'reference'=>'Ref00001',
           'error_url' =>'http://127.0.0.1:8000/curlerror',
           'success_url'=>'http://127.0.0.1:8000/success_url',
            'notifyURL'=>url('upayment/payment/notify'),
       );
            $fields_string = http_build_query($request_data);
            
            $ch = curl_init();
       
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     
                    
            curl_setopt($ch, CURLOPT_URL,"https://api.upayments.com/v2/payments/authorizations/".$sessionId); //Test Request URL
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
            // receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close ($ch);
            $server_output = json_decode($server_output,true);
            echo "<pre>"; print_r('jsossssssssssssn'); 
            echo "<pre>"; print_r(json_decode($server_output)); die;



            

    }    

    public function curltest(Request $request){
        
     //echo "<pre>"; print_r($sessionId); die;
        $request_data = array(
            'merchant_id'=>'1201',
            'username' => 'test',
            'password'=>stripslashes('test'), 'api_key'=>'jtest123', // in sandbox request
           // 'api_key' =>password_hash('API_KEY',PASSWORD_BCRYPT), //In production mode, please pass API_KEY with BCRYPT function
            'order_id'=>time(), // MIN 30 characters with strong unique function (like hashing function with time)
            //'total_price'=>'10',
            'total_price'=>$request->get('amount'),
            'CurrencyCode'=>'KWD',//only works in production mode
            'CstFName'=>'Test Name',
            'CstEmail'=>'test@test.com',
            'CstMobile'=>'12345678',
          //  'test_mode'=>1, // test mode enabled
            'payment_gateway'=>'knet',// only works in production mod
            'ProductName'=>json_encode(['computer','television']),
            'ProductQty'=>json_encode([2,1]),
            'ProductPrice'=>json_encode([150,1500]),
            'reference'=>'Ref00001',
            //'success_url'=>'https://example.com/success.html',
           // 'error_url'=>'https://example.com/error.html', 
           'error_url' =>'http://127.0.0.1:8000/curlerror',
           'success_url'=>'http://127.0.0.1:8000/success_url',
            'notifyURL'=>url('upayment/payment/notify'),
       );
       
      // echo "<pre>"; print_r($request_data); die;
       $fields_string = http_build_query($request_data);
            
       $ch = curl_init();
       
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       
         // if (env('PU_TEST_MODE') == 1) {
                    
                    curl_setopt($ch, CURLOPT_URL,"https://api.upayments.com/test-payment"); //Test Request URL
            //    }else{
            
            //         curl_setopt($ch, CURLOPT_URL,"https://api.upayments.com/payment-request");// Production Request URL
            //    }
            
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
            // receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close ($ch);
            $server_output = json_decode($server_output,true);
            echo "<pre>"; print_r('testinggg'); 
         
       
    }


    /**
     * Error Handling.
     */
    public function error()
    {
        return 'User cancelled the payment.';
    }
}