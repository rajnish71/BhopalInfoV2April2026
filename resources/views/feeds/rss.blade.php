<?= '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL ?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <channel>
        <title>Bhopal Info - Civic News Engine</title>
        <link>{{ url('/') }}</link>
        <description>Real-time civic updates for the city of Bhopal.</description>
        <language>en</language>
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
        @foreach($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ route('news.show', $post->slug) }}</link>
            <description><![CDATA[{!! $post->summary !!}]]></description>
            <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
            <guid isPermaLink="false">{{ $post->id }}</guid>
            <dc:creator>{{ $post->creator->name ?? 'Bhopal Info' }}</dc:creator>
        </item>
        @endforeach
    </channel>
</rss>