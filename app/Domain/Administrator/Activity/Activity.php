<?php
namespace Coyote\Domain\Administrator\Activity;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Coyote\Domain\Chart;
use Coyote\Domain\PostStatistic;
use Coyote\User;
use Coyote\View\Twig\TwigLiteral;

readonly class Activity
{
    public TwigLiteral $categoriesChart;
    public TwigLiteral $deleteReasonsChart;
    public TwigLiteral $chartLibrarySourceHtml;
    private Mention $mention;
    public array $categories;
    public array $deleteReasons;

    /**
     * @param Category[] $categories
     * @param Post[] $posts
     */
    public function __construct(
        private User         $user,
        public array         $posts,
        array                $categories,
        private array        $deleteReasons,
        public PostStatistic $postsStatistic,
    )
    {
        $this->mention = Mention::of($user);
        $this->categoriesChart = new TwigLiteral($this->categoriesChart($this->categoriesSliced($this->categoriesSorted($categories), 10)));
        $this->deleteReasonsChart = new TwigLiteral($this->deleteReasonsChart($this->reasonsSorted($deleteReasons)));
        $this->chartLibrarySourceHtml = new TwigLiteral(Chart::librarySourceHtml());

        array                $deleteReasons,
        public PostStatistic $postsStatistic,
    )
    {
        $this->mention = new Mention($user);

        \uSort($categories, fn(Category $a, Category $b): int => $b->posts - $a->posts);
        $this->categories = $categories;
        $this->deleteReasons = $this->sorted($deleteReasons);

        $forumNames = $this->extracted($this->categories, 'forumName');
        $categoriesChart = new Chart(
            $forumNames,
            $this->extracted($this->categories, 'posts'),
            \array_map($this->categoryColor(...), $forumNames),
            'categories-chart',
            baseline:40,
            horizontal:true,
        );

        $deleteReasonsChart = new Chart(
            \array_map(
                fn(?string $reason) => $reason ?? '(nie podano powodu)',
                $this->extracted($this->deleteReasons, 'reason'),
            ),
            $this->extracted($this->deleteReasons, 'posts'),
            \array_map($this->deleteReasonColor(...), $this->deleteReasons),
            'reasons-chart',
            baseline:10,
            horizontal:true,
        );

        $this->categoriesChart = new TwigLiteral($categoriesChart);
        $this->deleteReasonsChart = new TwigLiteral($deleteReasonsChart);

        $this->chartLibrarySourceHtml = new TwigLiteral($deleteReasonsChart->librarySourceHtml());
    }

    public function hasDeleteReasons(): bool
    {
        return \count($this->deleteReasons) > 0;
    }

    public function hasAnyPosts(): bool
    {
        return \count($this->posts) > 0;
    }

    public function username(): string
    {
        return $this->user->name;
    }

    public function mention(): TwigLiteral
    {
        return $this->mention->mention();
    }

    public function accountCreatedAt(): string
    {
        return $this->user->created_at->format('Y-m-d H:i:s');
    }

    public function createdAgoMajor(): string
    {
        [$number, $unit] = \explode(' ', $this->createdAgo(), 3);
        return "$number $unit";
    }

    public function createdAgoMinor(): string
    {
        return \subStr($this->createdAgo(), \strLen($this->createdAgoMajor()));
    }

    private function createdAgo(): string
    {
        $carbonInterval = $this->user->created_at->diff(Carbon::now());
        return $this->firstWords($carbonInterval, 6) . " temu";
    }

    private function firstWords(CarbonInterval $interval, int $words): string
    {
        return \implode(' ',
            \array_slice(
                \explode(' ', $interval),
                0, $words));
    }

    private function deleteReasonsChart(array $array): Chart
    {
        return new Chart(
            \array_map(
                fn(?string $reason) => $reason ?? '(nie podano powodu)',
                array_map(fn($object) => $object->reason, $array),
            ),
            array_map(fn($object) => $object->posts, $array),
            \array_map($this->deleteReasonColor(...), $array),
            'reasons-chart',
            baseline:40,
            horizontal:true,
        );
    }

    private function categoriesChart(array $categories): Chart
    {
        return new Chart(
            array_map(fn($category) => $category->forumName ?? '(pozostałe)', $categories),
            array_map(fn($category) => $category->posts, $categories),
            \array_map($this->categoryColor(...), $categories),
            'categories-chart',
            baseline:40,
            horizontal:true,
        );
    }

    private function categoryColor(Category $category): string
    {
        if ($category->forumName === null) {
            return '#c9cbcf'; // gray
        }
        $forumName = $category->forumName;
        if (\in_array($forumName, ['Flame', 'Off-Topic', 'Kosz', 'Spolecznosc', 'Spolecznosc/Perełki', 'Moderatorzy/Kapownik'])) {
            return '#ff6384'; // red
        }
        if (\in_array($forumName, ['Kadra', 'Moderatorzy', 'Moderatorzy/Administracja', 'Moderatorzy/Kartoteka'])) {
            return '#9966ff'; // purple
        }
        if (\in_array($forumName, ['Kariera', 'Opinie_o_pracodawcach', 'CV_do_oceny'])) {
            return '#4bc0c0'; // cyan
        }
        if (\in_array($forumName, ['Archiwum', 'Archiwum/Yosemite', 'Archiwum/RoadRunner', 'Coyote', 'Coyote/Test', 'Spolecznosc/Projekty', 'Moderatorzy/Zapomniane'])) {
            return '#80a41a'; // gray
        }
        if ($forumName === 'Ogłoszenia_drobne') {
            return '#36a2eb'; // blue
        }
        return '#ff9f40'; // orange
    }

    /**
     * @param DeleteReason[] $reasons
     */
    private function reasonsSorted(array $reasons): array
    {
        \uSort($reasons, function (DeleteReason $a, DeleteReason $b): int {
            if ($a->reason === null) {
                return 1;
            }
            if ($b->reason === null) {
                return -1;
            }
            return $b->posts - $a->posts;
        });
        return $reasons;
    }

    private function deleteReasonColor(DeleteReason $reason): string
    {
        if ($reason->reason === null) {
            return '#c9cbcf'; // gray
        }
        if (\in_array($reason->reason, [
            'Spam', 'Trolling', 'Wulgaryzmy', 'Omijanie bana',
            'Wycieczki osobiste i/lub obrażanie innych użytkowników',
        ])) {
            return '#ff6384'; // red 
        }
        return '#ff9f40'; // orange
    }

    private function categoriesSorted(array $categories): array
    {
        \uSort($categories, fn(Category $a, Category $b): int => $b->posts - $a->posts);
        return $categories;
    }

    private function categoriesSliced(array $categories, int $importantAmount): array
    {
        if (\count($categories) < $importantAmount + 2) {
            return $categories;
        }
        $dumped = \array_slice($categories, $importantAmount);
        $remaining = 0;
        foreach ($dumped as $category) {
            $remaining += $category->posts;
        }
        $important = \array_slice($categories, 0, $importantAmount);
        $important[] = new Category(null, $remaining);
        return $important;
    }
}
