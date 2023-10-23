<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CreateRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'search' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'string', 'in:desc,asc'],
        ]);

        /**
         * @var User $user
         */
        $user = $request->user();
        $posts = $user->posts();

        if ($request->filled('search')) {
            $posts->where(function (Builder $query) use ($request) {
                $query->where('title', 'like', '%'.$request->input('search').'%')
                    ->orWhere('body', 'like', '%'.$request->input('search').'%');
            });
        }

        $posts = $posts->orderBy('id', $request->input('sort_order', 'desc'))
            ->paginate();

        return response()->json([
            'data' => $posts,
        ]);
    }

    public function store(CreateRequest $request)
    {
        $post = new Post();
        $post->fill($request->validated());
        $user = $request->user();

        if ($request->boolean('publish')) {
            $subscription = $user->active_subscription();
            if (!$subscription) {
                return response()->json(['message' => 'Немає активної підписки'], 403);
            }
            if($subscription->remaining_publications > 0){
                $subscription->remaining_publications -= 1;
                $subscription->save();

                $post->fill([
                    'published_at' => Date::now(),
                ]);
            }else{
                return response()->json(['message' => 'Недостатньо доступних постів для публікації'], 403);
            }
        }

        $post->user()->associate($user);
        $post->save();

        return response()->json([
            'data' => $post->refresh(),
        ]);
    }

    public function show(Post $post)
    {
        $post->loadMissing('user');

        return response()->json([
            'data' => $post,
        ]);
    }

    public function update(UpdateRequest $request, Post $post)
    {
        $post->update($request->validated());

        return response()->json([
            'data' => $post->refresh(),
        ]);
    }

    public function destroy(Post $post)
    {
        DB::beginTransaction();

        $post->delete();

        DB::commit();

        return response()->noContent();
    }

    public function publish(Post $post)
    {
        $user = $post->user()->first();

        $subscription = $user->active_subscription(); // todo
        if (!$subscription) {
            return response()->json(['message' => 'Немає активної підписки'], 403);
        }
        if($subscription->remaining_publications > 0){
            $subscription->remaining_publications -= 1;
            $subscription->save();

            $post->fill([
                'published_at' => Date::now(),
            ]);
        }else{
            return response()->json(['message' => 'Недостатньо доступних постів для публікації'], 403);
        }
        $post->save();

        return response()->json([
            'data' => $post->refresh(),
        ]);
    }

    public function unPublish(Post $post)
    { // todo
        $post->fill([
            'published_at' => null,
        ]);
        $post->save();

        return response()->json([
            'data' => $post->refresh(),
        ]);
    }
}
