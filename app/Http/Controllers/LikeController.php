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
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $type, int $id)
    {
        $likeable = $this->findLikeable($type, $id);
        Gate::authorize('delete', [Like::class, $likeable]);

        $likeable->likes()->whereBelongsTo($request->user())->delete();
        $likeable->decrement('likes_count');

        return back();
    }

    protected function findLikeable(string $likeableType, int $likeableId) {
        $modelName = Relation::getMorphedModel($likeableType);

        if ($modelName === null) {
            throw new ModelNotFoundException();
        }

        return $modelName::findOrFail($likeableId);
    }
}
