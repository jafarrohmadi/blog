<?php

namespace App\Http\Controllers\Home;

use Agent;
use App;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\Store;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Note;
use App\Models\SocialiteUser;
use App\Models\Tag;
use Cache;
use Illuminate\Http\Request;
use Str;

class IndexController extends Controller
{
    /**
     * 首页
     *
     * @throws \Exception
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $articles = Article::select(
                'id', 'category_id', 'title',
                'slug', 'author', 'description',
                'cover', 'is_top', 'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->with(['category', 'tags'])
            ->paginate(10);
        $head = [
            'title'       => config('bjyblog.head.title'),
            'keywords'    => config('bjyblog.head.keywords'),
            'description' => config('bjyblog.head.description'),
        ];
        $assign = [
            'category_id'  => 'index',
            'articles'     => $articles,
            'head'         => $head,
            'tagName'      => '',
        ];

        return view('home.index.index', $assign);
    }

    public function article(Article $article, Request $request, Comment $commentModel)
    {
        $ipAndId = 'articleRequestList' . $request->ip() . ':' . $article->id;
        if (!Cache::has($ipAndId)) {
            cache([$ipAndId => ''], 1440);
            $article->increment('click');
        }

        $prev = Article::select('id', 'title', 'slug')
            ->orderBy('created_at', 'desc')
            ->where('id', '<', $article->id)
            ->limit(1)
            ->first();

        $next = Article::select('id', 'title', 'slug')
            ->orderBy('created_at', 'asc')
            ->where('id', '>', $article->id)
            ->limit(1)
            ->first();

        $comment     = $commentModel->getDataByArticleId($article->id);
        $category_id = $article->category->id;

        // Like
        $user = auth()->guard('socialite')->user();

        if ($user === null) {
            $is_liked = false;
        } else {
            $is_liked = $user->hasLiked($article);
        }

        $likes       = $article->likers()->get();
        $assign      = compact('category_id', 'article', 'prev', 'next', 'comment', 'is_liked', 'likes');

        return view('home.index.article', $assign);
    }

    public function category(Category $category)
    {
        $articles = $category->articles()
            ->orderBy('created_at', 'desc')
            ->with('tags')
            ->paginate(10);

        if ($articles->isNotEmpty()) {
            $articles->setCollection(
                collect(
                    $articles->items()
                )->map(function ($v) use ($category) {
                    $v->category = $category;

                    return $v;
                })
            );
        }

        $head = [
            'title'       => $category->name,
            'keywords'    => $category->keywords,
            'description' => $category->description,
        ];
        $assign = [
            'category_id'  => $category->id,
            'articles'     => $articles,
            'tagName'      => '',
            'title'        => $category->name,
            'head'         => $head,
        ];

        return view('home.index.index', $assign);
    }

    public function tag(Tag $tag)
    {
        $articles = $tag->articles()
            ->orderBy('created_at', 'desc')
            ->with(['category', 'tags'])
            ->paginate(10);
        $head = [
            'title'       => $tag->name,
            'keywords'    => $tag->keywords,
            'description' => $tag->description,
        ];
        $assign = [
            'category_id'  => 'index',
            'articles'     => $articles,
            'tagName'      => $tag->name,
            'title'        => $tag->name,
            'head'         => $head,
        ];

        return view('home.index.index', $assign);
    }

    /**
     * 随言碎语
     *
     * @return mixed
     */
    public function note()
    {
        $notes   = Note::orderBy('created_at', 'desc')->get();
        $assign  = [
            'category_id'  => 'note',
            'notes'        => $notes,
            'title'        => 'note',
        ];

        return view('home.index.note', $assign);
    }

    /**
     * 开源项目
     *
     * @return mixed
     */
    public function git()
    {
        $assign = [
            'category_id' => 'git',
            'title'       => 'git',
        ];

        return view('home.index.git', $assign);
    }

    /**
     * 文章评论
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function comment(Store $request, Comment $commentModel, SocialiteUser $socialiteUserModel)
    {
        $userId = auth()->guard('socialite')->user()->id;
        $email = $request->input('email', '');

        if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
            // 修改邮箱
            SocialiteUser::where('id', $userId)->update([
                'email' => $email,
            ]);
        }

        $comment = Comment::create($request->only('article_id', 'content', 'pid') + [
            'socialite_user_id' => $userId,
            'type'              => 1,
            'is_audited'        => Str::isTrue(config('bjyblog.comment_audit')) ? 0 : 1,
        ]);

        return response()->json(['id' => $comment->id]);
    }

    /**
     * 检测是否登录
     */
    public function checkLogin()
    {
        return response()->json([
            'status' => (int) auth()->guard('socialite')->check(),
        ]);
    }

    /**
     * 搜索文章
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request, Article $articleModel)
    {
        if (Agent::isRobot()) {
            abort(404);
        }

        $wd = clean($request->input('wd'));

        $id = $articleModel->searchArticleGetId($wd);

        $articles = Article::select(
                'id', 'category_id', 'title',
                'author', 'description', 'cover',
                'is_top', 'created_at'
            )
            ->whereIn('id', $id)
            ->orderBy('created_at', 'desc')
            ->with(['category', 'tags'])
            ->paginate(10);
        $head = [
            'title'       => $wd,
            'keywords'    => '',
            'description' => '',
        ];
        $assign = [
            'category_id'  => 'index',
            'articles'     => $articles,
            'tagName'      => '',
            'title'        => $wd,
            'head'         => $head,
        ];

        return response()->view('home.index.index', $assign)
            ->header('X-Robots-Tag', 'noindex');
    }

    /**
     * feed
     *
     * @return \Illuminate\Support\Facades\View
     */
    public function feed()
    {
        // 获取文章
        $article = Article::select('id', 'author', 'title', 'description', 'html', 'created_at')
            ->latest()
            ->get();

        $feed              = App::make('feed');
        $feed->title       = config('app.name');
        $feed->description = config('bjyblog.head.description');
        $feed->logo        = asset('uploads/avatar/1.jpg');
        $feed->link        = url('feed');
        $feed->setDateFormat('carbon');
        $feed->pubdate     = $article->first()->created_at;
        $feed->lang        = config('app.locale');
        $feed->ctype       = 'application/xml';

        foreach ($article as $v) {
            $feed->add($v->title, $v->author, url('article', $v->id), $v->created_at, $v->description);
        }

        return $feed->render('atom');
    }
}
