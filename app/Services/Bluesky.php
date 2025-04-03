<?php

namespace App\Services;

use App\Data\Bluesky\Conversation;
use App\Data\Bluesky\Member;
use App\Data\Bluesky\Message;
use App\Data\BskyImageData;
use App\Models\Group;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Parser\MarkdownParser;
use Revolution\Bluesky\Embed\External;
use Revolution\Bluesky\RichText\TextBuilder;
use Throwable;
use UnexpectedValueException;

class Bluesky
{
    protected $env;

    public function getProfile(Group $forGroup)
    {
        return Member::from($forGroup->bsky()->client()->getProfile($forGroup->bsky_did)->json());
    }

    public function sendMessage(Group $forGroup, string $convoId, string $message)
    {
        $bsky = $forGroup->bsky()->client();

        $response = $bsky->chat()->sendMessage($convoId, [
            'text' => $message
        ]);
    }

    public function getMessages(Group $forGroup, string $convoId, bool $noDTO = false)
    {
        $data = $forGroup->bsky()->client()->chat()->getMessages($convoId)->json('messages');
        if ($noDTO) return $data;
        return Message::collect($data, Collection::class);
    }

    public function getConversation(Group $forGroup, string $id)
    {
        return Cache::remember('group-' . $forGroup->id . '-convos-' . $id, 10, function () use ($forGroup, $id) {
            $bsky = $forGroup->bsky()->client();
            $data = $bsky->chat()->getConvo($id)->json('convo');
            $data['messages'] = $this->getMessages($forGroup, $id, true);
            return Conversation::from($data);
        });
    }

    public function getConversations(Group $forGroup)
    {
        return Cache::remember('group-' . $forGroup->id . '-convos', 10, function () use ($forGroup) {
            $bsky = $forGroup->bsky()->client();
            return Conversation::collect($bsky->chat()->listConvos()->json('convos'), Collection::class);
        });
    }

    public function markdownParser()
    {
        $this->env = new Environment([
            'allow_unsafe_links' => true,
        ]);
        return new MarkdownParser($this->env);
    }

    protected function convertMarkdownNode(Node $node, TextBuilder $onPost, bool $lastNode = false): TextBuilder
    {
        if ($node->hasChildren()) {
            foreach ($node->children() as $child) {
                $onPost = $this->convertMarkdownNode($child, $onPost);
            }
            if (get_class($node) == Paragraph::class && ! $lastNode) {
                $onPost = $onPost->newLine(2);
            }
        } else {
            switch (get_class($node)) {
                case Text::class:
                    if ($node->parent() && get_class($node->parent()) == Link::class) {
                        $link = $node->parent()->getUrl();
                        $title = $node->getLiteral();
                        $onPost->link($title, $link);
                        break;
                    }
                    $onPost = $onPost->text($node->getLiteral());

                    break;
                case Newline::class:
                    $onPost = $onPost->newLine();
                    break;
                default:
                    throw new Exception('Unknown node type ' . get_class($node));
            }
        }
        return $onPost;
    }

    public function convertMarkdown(string $markdown): TextBuilder
    {
        $converter = app(\League\CommonMark\ConverterInterface::class);
        $messageData = $converter->convert($markdown);

        $post = TextBuilder::make();
        $kids = $messageData->getDocument()->children();
        foreach ($kids as $index => $child) {
            $post = $this->convertMarkdownNode($child, $post, $index == count($kids) - 1);
        }

        return $post;
    }

    public function post(Group $forGroup, $markdown, $tags = [], ?BskyImageData $image = null)
    {
        $post = $this->convertMarkdown($markdown);
        if (count($tags) > 0) {
            $post->newLine();
            foreach ($tags as $tag) {
                $tagName = Str::startsWith($tag, '#') ? $tag : '#' . $tag;
                $tag = Str::startsWith($tag, '#') ? Str::after($tag, '#') : $tag;
                $post->tag(text: $tagName, tag: $tag)->text(' ');
            }
        }

        $post = $post->toPost();

        $post->createdAt(now()->toRfc3339String());
        $bsky = $forGroup->bsky();

        if ($image) {
            $blob = Storage::get($image->imageFile);
            $thumbnail = $bsky->uploadBlob($blob);
            $post->embed(External::create(
                title: $image->imageTitle,
                description: $image->imageDescription,
                uri: $image->imageUri,
                thumb: $thumbnail->json('blob'),
            ));
        }

        $response = $bsky->post($post);
        $uri = str($response->json('uri'));

        try {
            [$did, $collection, $rkey] = $uri->after('at://')->explode('/');
        } catch (Throwable) {
            throw new UnexpectedValueException("Did not get a valid post: {$response->body()}");
        }

        if ('app.bsky.feed.post' !== $collection) {
            throw new UnexpectedValueException("Did not get a post: {$response->body()}");
        }

        return "https://bsky.app/profile/{$did}/post/{$rkey}";
    }
}
