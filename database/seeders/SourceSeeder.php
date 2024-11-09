<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            [
                'name' => 'the_guardian',
                'title' => 'The Guardian',
                'rss_feed_link' => 'https://www.theguardian.com/uk/rss',
            ],
            [
                'name' => 'new_york_times',
                'title' => 'New York Times',
                'rss_feed_link' => 'https://rss.nytimes.com/services/xml/rss/nyt/World.xml',
            ],
            [
                'name' => 'open_news',
                'title' => 'Open News',
                'rss_feed_link' => 'https://source.opennews.org/rss/',
            ],
        ];

        foreach ($sources as $source) {
            Source::create($source);
        }
    }
}
