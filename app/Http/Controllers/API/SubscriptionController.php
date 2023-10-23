<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $request->user();
        $subscriptions = $user->subscriptions();
        return response()->json([
            'data' => $subscriptions->paginate(),
        ]);
    }
    public function show(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $request->user();
        return response()->json([
            'data' => $user->active_subscription(),
        ]);
    }

    public function list()
    {
        $subscriptions = Subscription::query()->active()->paginate();
        return response()->json([
            'data' => $subscriptions,
        ]);
    }

    public function choose(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $request->user();
        if($user->active_subscription()){
            return response()->json(['message' => 'Post already have active subscription']);
        }
        // generate payment link
        return response()->json([
            'url' => route('home'),
        ]);
    }

}
