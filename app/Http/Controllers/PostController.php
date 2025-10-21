<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\TopicResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use App\Models\Topic;
use Illuminate\Database\Eloquent\Builder;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function __construct() {
        $this->authorizeResource(Post::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ?Topic $topic = null)
    {
        $posts = Post::query()
            ->with(['user', 'topic'])
            ->when($topic, fn (Builder $q) => $q->whereBelongsTo($topic))
            ->when(
                $request->query('query'),
                fn (Builder $q) => $q
                    ->whereAny(['title', 'body'], 'like', '%' . $request->query('query') . '%'),
            )
            ->latest('id')
            ->paginate();

        return Inertia::render('Posts/Index', [
            'posts' => PostResource::collection($posts),
            'topics' => fn () => TopicResource::collection(Topic::all()),
            'selectedTopic' => fn () => $topic ? TopicResource::make($topic) : null,
            'query' => $request->query('query'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Posts/Create', [
            'topics' => fn () => TopicResource::collection(Topic::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'topic_id' => ['required', 'exists:topics,id'],
            'body' => ['required', 'string', 'max:65535'],
        ]);

        $post = Post::create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

        return redirect($post->showRoute());
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Post $post)
    {
        if (! Str::endsWith($post->showRoute(), $request->path())) {
            return redirect($post->showRoute($request->query()), status: 301);
        }

        $post->load('user', 'topic');
        $comments = $post->comments()->with('user')->orderBy('id', 'desc')->paginate(10);

        return Inertia::render('Posts/Show', [
            'post' => fn () => PostResource::make($post)->withLikePermission(),
            'comments' => function () use ($post, $comments) {
                $commentResourceCollection = CommentResource::collection($comments);

                $commentResourceCollection->collection->transform(fn ($resource) => $resource->withLikePermission());

                return $commentResourceCollection;
            },
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
