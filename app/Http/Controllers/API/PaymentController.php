<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        /**
         * @var User $user
         */
        $user_id = $request->input('user_id');
        $user = User::query()->find($user_id);
        if($user->active_subscription()){
            return response()->json(['message' => 'User already have active subscription'], 500);
        }
        $subscription_id = $request->input('subscription_id');
        $subscription = Subscription::query()->find($subscription_id);
        $status = $request->input('status');
//        $time = $user->active_subscription() ? Carbon::parse($user->latest_subscription->available_to) : now();
        if($status == 'paid' && $user && $subscription){
            $user_subscription = new UserSubscription();
            $user_subscription->user()->associate($user);
            $user_subscription->subscription()->associate($subscription);
            $user_subscription->fill([
                'remaining_publications' => $subscription->remaining_publications,
                'available_to' => now()->addMonth(),
            ]);
            $user_subscription->save();
        }
        $payment = new Payment([
            'user_id' => $user_id,
            'subscription_id' => $subscription_id,
//            'user_subscription' => $user_subscription->id,
            'price' => $request->input('price', 0),
            'session_id' => $request->input('session_id', ''),
            'is_paid' => $status == 'paid' ? 1 : 0,
        ]);
        $payment->save();
        // Поверніть відповідь користувачеві, підтверджуючи факт оплати або вказавши іншу необхідну інформацію.
        return response()->json(['message' => 'Payment processed successfully']);
    }
}
