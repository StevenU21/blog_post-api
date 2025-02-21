<?php
namespace App\Http\Middleware;

use App\Models\Post;
use Closure;
use Illuminate\Http\Request;

class TrackViewsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $postParam = $request->route('post');

        if ($postParam instanceof Post) {
            $post = $postParam;
        } elseif (is_numeric($postParam)) {
            $post = Post::find($postParam);
        } else {
            $post = null;
        }

        if ($post) {
            $post->increment('views');
        }

        return $next($request);
    }
}
