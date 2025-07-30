<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\KeywordFilter;
use App\Traits\ApiResponse;

class PostController extends Controller
{
    use AuthorizesRequests, ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = QueryBuilder::for(Post::with(['user', 'comments.user']))
            ->allowedFilters([
                AllowedFilter::custom('keyword', new KeywordFilter()),
            ])
            ->allowedSorts(['created_at'])
            ->paginate(10);

        $data = [
            'posts' => PostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
            'links' => [
                'self' => route('posts.index'),
                'next' => $posts->nextPageUrl(),
                'prev' => $posts->previousPageUrl(),
            ],
        ];

        return $this->success($data, 'Posts retrieved successfully' , 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        // Validate the request data
        $validatedData = $request->validated();


        try{

            if ($request->hasFile('image')) {
                $validatedData['image'] = $request->file('image')->store('posts', 'public');
            } else {
                $validatedData['image'] = null;
            }

            $post = Post::create([
                'title' => $validatedData['title'],
                'image' => $validatedData['image'] , 
                'content' => $validatedData['content'],
                'user_id' => $request->user()->id, 
                
            ]);

            return $this->success(new PostResource($post), 'Post created successfully', 201)
                ->header('Location', route('posts.show', $post->id));

        }catch(\Exception $e){
            return $this->error('Failed to create post', $e->getMessage(), 500);
        }
 
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['user', 'comments.user']);
        return $this->success(new PostResource($post), 'Post retrieved successfully', 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {

        $validatedData = $request->validated();
        try {

            if ($request->boolean('remove_image')) {
                if ($post->image && Storage::disk('public')->exists($post->image)) {
                    Storage::disk('public')->delete($post->image);
                }
                $validatedData['image'] = null;


            } elseif ($request->hasFile('image')) {
                if ($post->image && Storage::disk('public')->exists($post->image)) {
                    Storage::disk('public')->delete($post->image);
                }
                $validatedData['image'] = $request->file('image')->store('posts', 'public');
            }

            $post->update([
                'title' => $validatedData['title'],
                'image' => array_key_exists('image', $validatedData) ? $validatedData['image'] : $post->image,
                'content' => $validatedData['content'],
            ]);

            $post->load(['user', 'comments.user']);
            
            return $this->success(new PostResource($post), 'Post updated successfully', 200)
                ->header('Location', route('posts.show', $post->id));

        }catch(\Exception $e) {
            return $this->error('Failed to update post', $e->getMessage(), 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        
        try{
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }


            $post->delete();
            return $this->success(null, 'Post deleted successfully', 200)
                ->header('Location', route('posts.index'));
    
        }catch(\Exception $e) {
            return $this->error('Failed to delete post', $e->getMessage(), 500);
        }
    }


    public function uploadImage(Request $request,Post $post)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try{
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $newPath = $request->file('image')->store('posts', 'public');
            $post->update(['image' => $newPath]);

            return $this->success(new PostResource($post), 'Image uploaded successfully', 200)
                ->header('Location', route('posts.show', $post->id));

        }catch(\Exception $e) {
            return $this->error('Failed to upload image', $e->getMessage(), 500);
        }
    }

    public function deleteImage(Post $post)
    {
        try{

            if($post->image && Storage::disk('public')->exists($post->image)){
                Storage::disk('public')->delete($post->image);
            }
            $post->update(['image' => null]);

            return $this->success(new PostResource($post), 'Image deleted successfully', 200)
                ->header('Location', route('posts.show', $post->id));
            
        }catch(\Exception $e) {
            return $this->error('Failed to delete image', $e->getMessage(), 500);
        }
    }
}
