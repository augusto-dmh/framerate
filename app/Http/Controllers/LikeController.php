<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $type, int $id)
    {
        $likeable = $this->findLikeable($type, $id);
        Gate::authorize('create', [Like::class, $likeable]);

        $likeable->likes()->create([
            'user_id' => $request->user()->id,
            'likeable_id' => $id,
            'likeable_type' => $type,
        ]);
        $likeable->increment('likes_count');

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Like $like)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Like $like)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Like $like)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Like $like)
    {
        //
    }

    protected function findLikeable(string $likeableType, int $likeableId) {
        $modelName = Relation::getMorphedModel($likeableType);

        if ($modelName === null) {
            throw new ModelNotFoundException();
        }

        return $modelName::findOrFail($likeableId);
    }
}
