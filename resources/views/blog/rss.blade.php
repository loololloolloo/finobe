@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<rss version="2.0">
<channel>
    <title>{{ $settings['site_title'] }}</title>
    <link>{{ $blogUrl }}/</link>
    <description>{{ $settings['site_description'] }}</description>
    <language>en-us</language>
    @foreach($posts as $post)
    <item>
        <title>{{ $post->title }}</title>
        <link>{{ $blogUrl }}/{{ \Carbon\Carbon::parse($post->published_at)->format('Y/m/d') }}/{{ $post->slug }}/</link>
        <guid isPermaLink="true">{{ $blogUrl }}/{{ \Carbon\Carbon::parse($post->published_at)->format('Y/m/d') }}/{{ $post->slug }}/</guid>
        <pubDate>{{ \Carbon\Carbon::parse($post->published_at)->toRssString() }}</pubDate>
        <description>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags(\App\Blog\Publisher::html($post->body)), 240) }}</description>
    </item>
    @endforeach
</channel>
</rss>
