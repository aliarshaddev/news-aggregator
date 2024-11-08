<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use App\Models\Author;
use Carbon\Carbon;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from News API by categories in the database and save them to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sources = Source::whereNotNull('rss_feed_link')->get();

        foreach ($sources as $source) {
            $rssUrl = $source->rss_feed_link;
            $response = Http::get($rssUrl);

            if ($response->failed()) {
                $this->error("Failed to fetch RSS feed from URL: {$rssUrl}");
                continue;
            }

            $rssFeed = simplexml_load_string($response->body());
            if (!$rssFeed || !$rssFeed->channel->item) {
                $this->error("Failed to fetch or parse RSS feed from URL: {$rssUrl}");
                continue;
            }
            foreach ($rssFeed->channel->item as $item) {
                $this->parseArticle($item, $source);
            }
        }
    }
    protected function parseArticle($item, $source)
    {
        $title = (string) $item->title;
        $description = (string) $item->description;
        $link = (string) $item->link;
        $pubDate = (string) $item->pubDate;
        $categoryName = (string) $item->category ?? null;
        $articleExists = Article::where('link', $link)->where('source_id', $source->id)->exists();
        if ($articleExists) {
            return;
        }
        $authorName = null;
        if ($dcCreator = $item->children('http://purl.org/dc/elements/1.1/')->creator) {
            $authorName = (string) $dcCreator;
        }
        $author = $authorName ? Author::firstOrCreate(['name' => $authorName, 'source_id' => $source->id]) : null;
        $category = $categoryName ? Category::firstOrCreate(['name' => $categoryName, 'source_id' => $source->id]) : null;
        Article::create([
            'title' => $title,
            'description' => $description ?? '',
            'link' => $link,
            'source_id' => $source->id,
            'category_id' => $category->id ?? null,
            'author_id' => $author->id ?? null,
            'published_at' => Carbon::parse($pubDate),
        ]);
        $this->info("Article '{$title}' from source '{$source->name}' fetched and stored successfully.");
    }
}
