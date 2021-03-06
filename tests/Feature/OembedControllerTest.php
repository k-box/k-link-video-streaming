<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\VideoRepository;
use Illuminate\Support\Facades\Storage;

class OembedControllerTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions, WithoutMiddleware;

    private function create_video()
    {
        Storage::fake('videos');
        
        $repository = app(VideoRepository::class);
        
        $video = $repository->create('1', '1', 'test.mp4', 'video/mp4');
    
        // Fake that video upload and processing has been completed
        $video->completed = true;
        $video->save();

        return $video;
    }

    public function generate_invalid_urls(){
        return [
            ['http://localhost/hello'],
            ['http://localhost/hello/1025'],
            ['https://localhost/hello/1025'],
            ['ftp://localhost/hello/1025'],
            ['javascript://localhost/hello/1025'],
            ['javascript:void(0)'],
            ['javascript:void(0)'],
            [ 'http://localhost:8000/play/10?hello=true'],
            [ 'http://localhost:8000/play/10#something'],
            [ 'http://localhost:8000/play/10#\'DROP TABLE *'],
            [ 'http://localhost:8000/play#\'DROP TABLE *'],
            [urlencode('http://localhost/hello')],
        ];
    }

    public function test_oembed_json_is_returned()
    {
        $video = $this->create_video();
        
        $response = $this->json('GET', '/oembed?format=json&url=' . urlencode($video->url));
        
        $response
            ->assertStatus(200)
            ->assertJson([
                'version' => '1.0', 
                'type' => 'video',
                'provider_name' => config('app.name'),
                'provider_url' => config('app.url'),
            ])
            ->assertJsonStructure([
                "version",
                "type",
                "provider_name",
                "provider_url",
                "width",
                "height",
                "title",
                "html",
            ]);
    }

    public function test_oembed_respect_maxwith_and_height()
    {
        $video = $this->create_video();
        
        $response = $this->json('GET', '/oembed?format=json&maxwidth=100&maxheight=100&url=' . urlencode($video->url));
        
        $response
            ->assertStatus(200)
            ->assertJson([
                'version' => '1.0', 
                'type' => 'video',
                'width' => 100,
                'height' => 100,
            ])
            ->assertJsonStructure([
                "version",
                "type",
                "provider_name",
                "provider_url",
                "width",
                "height",
                "title",
                "html",
            ]);
    }
    
    public function test_oembed_return_not_implemented_for_xml_format()
    {        
        $response = $this->json('GET', '/oembed?format=xml&url=' . urlencode('http://localhost/hello'));
        
        $response->assertStatus(501);
    }
    
    /**
     * @dataProvider generate_invalid_urls
     */
    public function test_oembed_return_not_found_for_invalid_urls($url)
    {
        $response = $this->json('GET', '/oembed?format=json&url=' . urlencode($url));
            
        $response->assertStatus(404);
    }
}
