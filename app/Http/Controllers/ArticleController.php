<?php

namespace App\Http\Controllers;

use App\Article;
use App\Policies\ArticlePolicy;
use Illuminate\Http\Request;
use App\User;
use App\Comment;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\CommentResource;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny', Article::class);
        return ArticleResource::collection(Article::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|object
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', Article::class);

        $article = $request->user()->articles()->create($this->validateData());
        ArticleResource::withoutWrapping();

        return (new ArticleResource($article))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Article $article
     * @return ArticleResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Article $article)
    {
        $this->authorize('view', $article);

        ArticleResource::withoutWrapping();
        return new ArticleResource($article);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Article $article
     * @return \Illuminate\Http\JsonResponse|object
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);
        $article->update($this->validateData());
        return (new ArticleResource($article))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article $article
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);
        $article->delete();
        return response([], Response::HTTP_NO_CONTENT);
    }

    public function myArticles()
    {
        $user = Auth::user();
        return ArticleResource::collection($user->articles()->paginate(3));

    }

    public function validateData()
    {
       return request()->validate([
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'required',
        ]);
    }
}
