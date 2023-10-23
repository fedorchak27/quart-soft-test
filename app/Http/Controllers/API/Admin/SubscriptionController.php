<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\CreateRequest;
use App\Http\Requests\Subscription\UpdateRequest;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::query()->paginate();
        return response()->json([
            'data' => $subscriptions,
        ]);
    }

    public function store(CreateRequest $request)
    {
        $subscription = new Subscription();
        $subscription->fill($request->validated());
        $subscription->save();

        return response()->json([
            'data' => $subscription->refresh(),
        ]);
    }

    public function show(Subscription $subscription)
    {
//        $subscription->loadMissing('user_subscriptions');

        return response()->json([
            'data' => $subscription,
        ]);
    }

    public function update(UpdateRequest $request, Subscription $subscription)
    {
        $subscription->update($request->validated());

        return response()->json([
            'data' => $subscription->refresh(),
        ]);
    }

    public function destroy(Subscription $subscription)
    {
        DB::beginTransaction();

        $subscription->delete();

        DB::commit();

        return response()->noContent();
    }
}
