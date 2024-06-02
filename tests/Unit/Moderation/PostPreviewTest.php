<?php
namespace Tests\Unit\Moderation;

use Carbon\Carbon;
use Coyote\Domain\Administrator\Activity\Post;
use PHPUnit\Framework\TestCase;
use Tests\Unit\BaseFixture\Server\Laravel\Application;

class PostPreviewTest extends TestCase
{
    use Application;

    /**
     * @test
     */
    public function empty(): void
    {
        $this->assertPreview('  ', '');
    }

    /**
     * @test
     */
    public function test(): void
    {
        $this->assertPreview(
            'Short text.',
            '<p>Short text.</p>',
        );
    }

    /**
     * @test
     */
    public function ignoreQuote(): void
    {
        $this->assertPreview(
            "> quote\n\ntext",
            '<p>text</p>',
        );
    }

    /**
     * @test
     */
    public function firstParagraph(): void
    {
        $this->assertPreview(
            '<p>One</p> <p>Two</p>',
            '<p>One</p>',
        );
    }

    /**
     * @test
     */
    public function paragraphInList(): void
    {
        $this->assertPreviewNone("> - one\n    two");
    }

    /**
     * @test
     */
    public function unicode(): void
    {
        $this->assertPreview('Łódź.', '<p>Łódź.</p>');
    }

    private function assertPreviewNone(string $postContent): void
    {
        $this->assertSame('', (string)$this->newPost($postContent)->previewHtml());
    }

    private function assertPreview(string $postContent, string $expectedPreview): void
    {
        $previewHtml = (string)$this->newPost($postContent)->previewHtml();
        $this->assertSame($expectedPreview, \trim($previewHtml));
    }

    private function newPost(string $postContent): Post
    {
        return new Post($postContent,
            '', '', '', '',
            new Carbon(), false, false);
    }
}
