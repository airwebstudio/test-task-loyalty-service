<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyAccount;
use Illuminate\Http\Request;
use App\Facades\RequestValidator;

class AccountController extends Controller
{
    public function create(Request $request)
    {        
       return LoyaltyAccount::create($request->only(RequestValidator::getFields()));
    }

   
    public function activate($type, $id)
    {
        if (!RequestValidator::validate($type, $id)) {
            throw new \InvalidArgumentException('Wrong parameters');
        }
        
        if ($account = LoyaltyAccount::getByParams($type, $id)) {
            $account->activate();
            
        } else {
            return response()->json(['message' => 'Account is not found'], 400);
        }
    

        return response()->json(['success' => true]);
    }

    public function deactivate($type, $id)
    {
        if (!RequestValidator::validate($type, $id)) {
            throw new \InvalidArgumentException('Wrong parameters');
        }

        if ($account = LoyaltyAccount::getByParams($type, $id)) {
            $account->deactivate();
        } else {
            return response()->json(['message' => 'Account is not found'], 400);
        }
        
        return response()->json(['success' => true]);
    }

    public function balance($type, $id)
    {
        if (!RequestValidator::validate($type, $id)) {
            throw new \InvalidArgumentException('Wrong parameters');
        }

        if ($account = LoyaltyAccount::getByParams($type, $id)) {
            return response()->json(['balance' => $account->getBalance()], 400);

        } else {
            return response()->json(['message' => 'Account is not found'], 400);
        }
        
    }
}
