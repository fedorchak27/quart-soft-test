<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::query()->published();

        if ($request->filled('search')) {
            $posts->where(function (Builder $query) use ($request) {
                $query->where('title', 'like', '%'.$request->input('search').'%')
                    ->orWhere('body', 'like', '%'.$request->input('search').'%');
            });
        }
        if ($request->filled('author')) {
            $posts->where(function (Builder $query) use ($request) {
                $query->where('user_id', '=', $request->input('author'));
            });
        }
        if ($request->filled('id')) {
            $posts->where(function (Builder $query) use ($request) {
                $query->where('id', '=', $request->input('id'));
            });
        }
        if ($request->filled('date_start') && $request->filled('date_end')) {
            $posts->where(function (Builder $query) use ($request) {
                $query->whereBetween('published_at', [$request->input('date_start'), $request->input('date_end')]);
            });
        }
        //todo date

        $posts = $posts->orderBy('id', $request->input('sort_order', 'desc'))
            ->paginate();

        return response()->json([
            'data' => $posts,
        ]);
    }


    public function show(Post $post)
    {
        $post->loadMissing('user');

        return response()->json([
            'data' => $post,
        ]);
    }
}
