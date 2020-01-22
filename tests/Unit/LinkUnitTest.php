<?php

namespace Tests\Unit;

use App\Models\Deliverable;
use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LinkUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_given_youtube_url_empty_get_empty_string()
    {
        $deliverable = factory(Deliverable::class)->create();
        $link = factory(Link::class)->create([
            'url' => '',
            'deliverable_id' => $deliverable,
        ]);
        $this->assertEquals('', $link->getYoutubeEmbedUrl());
    }

    public function test_given_youtube_url_is_valid_get_embed_url()
    {
        $deliverable = factory(Deliverable::class)->create();
        $link = factory(Link::class)->create([
            'url' => 'https://www.youtube.com/watch?v=-htlGs4SCxE',
            'deliverable_id' => $deliverable,
        ]);
        $this->assertEquals('https://www.youtube.com/embed/-htlGs4SCxE', $link->getYoutubeEmbedUrl());
    }

    public function test_given_youtube_url_is_valid_with_other_query_get_embed_url()
    {
        $deliverable = factory(Deliverable::class)->create();
        $link = factory(Link::class)->create([
            'url' => 'https://www.youtube.com/watch?v=-htlGs4SCxE&feature=youtu.be',
            'deliverable_id' => $deliverable,
        ]);
        $this->assertEquals('https://www.youtube.com/embed/-htlGs4SCxE', $link->getYoutubeEmbedUrl());
    }

    public function test_given_youtube_url_is_invalid_get_empty_string()
    {
        $deliverable = factory(Deliverable::class)->create();
        $link = factory(Link::class)->create([
            'url' => 'https://www.ytube.com/watch?v=-htlGs4SCxE&feature=youtu.be',
            'deliverable_id' => $deliverable,
        ]);
        $this->assertEquals('', $link->getYoutubeEmbedUrl());
    }

    public function test_is_facebook_post_normal()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/posts/100462391327119',
        ]);

        $this->assertTrue($link->isFacebookPost());
    }

    public function test_is_facebook_post_with_query_string()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/posts/100462391327119?extra=1&weird=abc',
        ]);

        $this->assertTrue($link->isFacebookPost());
    }

    public function test_is_facebook_post_wrong_url()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebooks.com/testyesonly/posts/100462391327119?extra=1&weird=abc',
        ]);

        $this->assertFalse($link->isFacebookPost());
    }

    public function test_is_facebook_photo_is_not_post()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/photos/a.100462367993788/100473814659310',
        ]);

        $this->assertFalse($link->isFacebookPost());
    }

    public function test_is_facebook_photo()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/photos/a.100462367993788/100473814659310',
        ]);

        $this->assertTrue($link->isFacebookPhoto());
    }

    public function test_is_facebook_photo_wrong_url()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/photoss/a.100462367993788/100473814659310',
        ]);

        $this->assertFalse($link->isFacebookPhoto());
    }

    public function test_is_facebook_video_normal()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/videos/100462391327119',
        ]);

        $this->assertTrue($link->isFacebookVideo());
    }

    public function test_is_facebook_video_with_query_string()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/videos/100462391327119?extra=1&weird=abc',
        ]);

        $this->assertTrue($link->isFacebookVideo());
    }

    public function test_is_facebook_video_wrong_url()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebooks.com/testyesonly/videos/100462391327119?extra=1&weird=abc',
        ]);

        $this->assertFalse($link->isFacebookVideo());
    }

    public function test_is_instagram_story_normal()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.instagram.com/stories/testyesonly/100462391327119',
        ]);

        $this->assertTrue($link->isInstagramStory());
    }

    public function test_is_instagram_story_with_query_string()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.instagram.com/stories/testyesonly/100462391327119?extra=1&weird=abc',
        ]);

        $this->assertTrue($link->isInstagramStory());
    }

    public function test_is_instagram_story_wrong_url()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.instagrams.com/stories/testyesonly/100462391327119?extra=1&weird=abc',
        ]);

        $this->assertFalse($link->isInstagramStory());
    }

    public function test_get_facebook_post_id_normal()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/posts/100462391327119',
        ]);

        $this->assertEquals($link->getPostId(), '100462391327119');
    }

    public function test_get_facebook_post_id_with_query_string()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/posts/100462391327119?hello',
        ]);

        $this->assertEquals($link->getPostId(), '100462391327119');
    }

    public function test_get_facebook_post_id_ending_with_slash()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/posts/100462391327119/',
        ]);

        $this->assertEquals($link->getPostId(), '100462391327119');
    }

    public function test_get_facebook_photo_normal()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/photos/a.100462367993788/100473814659310',
        ]);

        $this->assertEquals($link->getPostId(), '100473814659310');
    }

    public function test_get_facebook_photo_with_query_string()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/photos/a.100462367993788/100473814659310?hello=1&hehe=2',
        ]);

        $this->assertEquals($link->getPostId(), '100473814659310');
    }

    public function test_get_facebook_photo_with_slash()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/photos/a.100462367993788/100473814659310/?hello=1&hehe=2',
        ]);

        $this->assertEquals($link->getPostId(), '100473814659310');
    }

    public function test_get_facebook_photo_with_wrong_url()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/photoss/a.100462367993788/100473814659310/?hello=1&hehe=2',
        ]);

        $this->assertEquals($link->getPostId(), '');
    }

    public function test_get_facebook_video_id_normal()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/videos/100462391327119',
        ]);

        $this->assertEquals($link->getPostId(), '100462391327119');
    }

    public function test_get_facebook_video_id_with_query_string()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/videos/100462391327119?hello',
        ]);

        $this->assertEquals($link->getPostId(), '100462391327119');
    }

    public function test_get_facebook_video_id_ending_with_slash()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.facebook.com/testyesonly/videos/100462391327119/',
        ]);

        $this->assertEquals($link->getPostId(), '100462391327119');
    }

    public function test_get_instagram_story_id_normal()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.instagram.com/stories/testyesonly/100462391327119',
        ]);

        $this->assertEquals($link->getPostId(), '100462391327119');
    }

    public function test_get_instagram_story_id_with_query_string()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.instagram.com/stories/testyesonly/100462391327119?hello',
        ]);

        $this->assertEquals($link->getPostId(), '100462391327119');
    }

    public function test_get_instagram_story_id_ending_with_slash()
    {
        $link = factory(Link::class)->create([
            'url' => 'https://www.instagram.com/stories/testyesonly/100462391327119/',
        ]);

        $this->assertEquals($link->getPostId(), '100462391327119');
    }
}
