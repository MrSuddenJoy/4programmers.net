<?php
namespace Coyote\Http\Controllers\Profile;

use Coyote\Domain\Chart;
use Coyote\Http\Controllers\Controller;
use Coyote\Http\Controllers\User\Menu\ProfileNavigation;
use Coyote\Http\Requests\SkillsRequest;
use Coyote\Http\Resources\MicroblogCollection;
use Coyote\Http\Resources\TagResource;
use Coyote\Http\Resources\UserResource;
use Coyote\Microblog;
use Coyote\Post;
use Coyote\Repositories\Contracts\PostRepositoryInterface as PostRepository;
use Coyote\Repositories\Contracts\ReputationRepositoryInterface as ReputationRepository;
use Coyote\Repositories\Contracts\UserRepositoryInterface as UserRepository;
use Coyote\Repositories\Criteria\Forum\OnlyThoseWithAccess;
use Coyote\Repositories\Eloquent\MicroblogRepository;
use Coyote\Services\Microblogs\Builder;
use Coyote\Services\Parser\Extensions\Emoji;
use Coyote\User;
use Coyote\View\Twig\TwigLiteral;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    use ProfileNavigation;

    public function __construct(
        private UserRepository       $user,
        private ReputationRepository $reputation,
        private PostRepository       $post,
        private MicroblogRepository  $microblog,
    )
    {
        parent::__construct();

        $this->middleware(function (Request $request, $next) {
            /** @var User $user */
            $user = $request->route('user_trashed');
            abort_if($user->deleted_at && (!$this->userId || $this->auth->cannot('adm-access')), 404);
            return $next($request);
        });
    }

    public function index(User $user, string $tab = 'reputation'): View
    {
        $this->breadcrumb->push($user->name, route('profile', ['user_trashed' => $user->id]));

        $menu = $this->getUserMenu();

        if ($menu->get('profile')) {
            // activate "Profile" tab no matter what.
            $menu->get('profile')->activate();
        }

        return $this->view('profile.home')->with([
            'top_menu'    => $menu,
            'user'        => new UserResource($user),
            'skills'      => TagResource::collection($user->skills->load('category')),
            'rate_labels' => SkillsRequest::RATE_LABELS,
            'tab'         => strtolower($tab),
            'module'      => $this->$tab($user),
        ]);
    }

    public function history(User $user, Request $request): View
    {
        return view('profile.partials.reputation_list', [
            'reputation' => $this->reputation->history($user->id, $request->input('offset')),
        ]);
    }

    private function chart(User $user): Chart
    {
        $chart = $this->reputation->chart($user->id);
        return new Chart(
            \array_map(fn(array $item) => $item['label'], $chart),
            \array_map(fn(array $rec) => $rec['value'], $chart),
            ['#ff9f40'],
            'reputation-chart',
        );
    }
}
