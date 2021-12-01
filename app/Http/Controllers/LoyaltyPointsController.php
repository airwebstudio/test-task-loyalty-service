<?php
namespace App\Http\Controllers;

use App\Mail\LoyaltyPointsReceived;
use App\Models\LoyaltyAccount;
use App\Models\LoyaltyPointsTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Facades\RequestValidator;

class LoyaltyPointsController extends Controller
{

    protected $data;

    protected $type;
    protected $id;

    public function __construct(Request $request)
    {
        $this->type = $request->input('account_type');
        $this->id = $request->input('account_id');        

        $this->data = $request->all();        
    }

    public function deposit()
    {

        Log::info('Deposit transaction input: ' . print_r($this->data, true));

        if (!RequestValidator::validate($this->type, $this->id)) {
            Log::info('Wrong account parameters');
            throw new \InvalidArgumentException('Wrong account parameters');
        }

        $account = LoyaltyAccount::getByParams($this->type, $this->id);
           
        if (!$account) {
            Log::info('Account is not found');
            return response()->json(['message' => 'Account is not found'], 400);
        }

        if (!$account->active) {            
            Log::info('Account is not active');
            return response()->json(['message' => 'Account is not active'], 400);                
        }
            
        $transaction =  LoyaltyPointsTransaction::performPaymentLoyaltyPoints($account->id, $this->data);
        Log::info($transaction);
        
        if (!empty($account->email) && $account->email_notification) {
            Mail::to($account)->send(new LoyaltyPointsReceived($transaction->points_amount, $account->getBalance()));
        }
        if (!empty($account->phone) && $account->phone_notification) {
            // instead SMS component
            Log::info('You received' . $transaction->points_amount . 'Your balance' . $account->getBalance());
        }
        return response()->json($transaction);
    }         
     
    public function cancel()
    {        

        if (empty($this->data['cancelation_reason'])) {
            return response()->json(['message' => 'Cancellation reason is not specified'], 400);
        }
        $transaction = LoyaltyPointsTransaction::where('id', '=', $this->data['transaction_id'])->where('canceled', '=', 0)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction is not found'], 400);
        }

        $transaction->cancel();
        
    }

    public function withdraw()
    {

        Log::info('Withdraw loyalty points transaction input: ' . print_r($this->data, true));
        
        if (!RequestValidator::validate($this->type, $this->id)) {
            Log::info('Wrong account parameters');
            throw new \InvalidArgumentException('Wrong account parameters');
        }
        $account = LoyaltyAccount::getByParams($this->type, $this->id);

        if (!$account) {
            Log::info('Account is not found:' . $this->type . ' ' . $this->id);
            return response()->json(['message' => 'Account is not found'], 400);
        }

        if (!$account->active) {
            Log::info('Account is not active: ' . $this->type . ' ' . $this->id);
            return response()->json(['message' => 'Account is not active'], 400);
        }
                
        if ($this->data['points_amount'] <= 0) {
            Log::info('Wrong loyalty points amount: ' . $this->data['points_amount']);
            return response()->json(['message' => 'Wrong loyalty points amount'], 400);
        }

        if ($account->getBalance() < $this->data['points_amount']) {
            Log::info('Insufficient funds: ' . $this->data['points_amount']);
            return response()->json(['message' => 'Insufficient funds'], 400);
        }

        $transaction = LoyaltyPointsTransaction::withdrawLoyaltyPoints($account->id, $data['points_amount'], $data['description']);
        Log::info($transaction);
        return response()->json($transaction);                
        
    }
}
