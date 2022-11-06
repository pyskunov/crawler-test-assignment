@extends('templates.layouts.app')

@section('title', config('app.name') . " - Results")

<div class="col-10 offset-1">
    <a class="btn btn-primary mb-4" href="/">Try again</a>

    <p class="h1 mb-4">Target: <i>{{ request()->input('target') }}</i></p>

    <strong class="h1">Total stats</strong>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Number of pages crawled</th>
                <th>Number of a unique images (in all the pages)</th>
                <th>Number of unique internal links (in all the pages)</th>
                <th>Number of unique external links (in all the pages)</th>
                <th>Average page load in seconds</th>
                <th>Average word count</th>
                <th>Average title length</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>{{ $data['total']['pages'] }}</td>
                <td>{{ $data['total']['unique_images'] }}</td>
                <td>{{ $data['total']['unique_internal_urls'] }}</td>
                <td>{{ $data['total']['unique_external_urls'] }}</td>
                <td>{{ $data['total']['avg_page_load_speed'] }} ms</td>
                <td>{{ $data['total']['avg_words_count'] }}</td>
                <td>{{ $data['total']['avg_title_length'] }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="col-10 offset-1">
    <strong class="h1">Per task stats</strong>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>URL</th>
                <th>HTTP Code</th>
                <th>Page Load Speed</th>
                <th>Unique Images</th>
                <th>Unique Internal Links</th>
                <th>Unique External Links</th>
                <th>Words count</th>
                <th>Title Length</th>
            </tr>
        </thead>

        <tbody>
            @foreach($data['per_task'] as $task)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $task['url'] }}</td>
                    <td>{{ $task['http_code'] }}</td>
                    <td>{{ $task['page_load_speed'] }} ms</td>
                    <td>{{ $task['unique_images'] }}</td>
                    <td>{{ $task['unique_internal_links'] }}</td>
                    <td>{{ $task['unique_external_links'] }}</td>
                    <td>{{ $task['words_count'] }}</td>
                    <td>{{ $task['title_length'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @empty($data['errors'])
        <div class="col-10 offset-1">
            <span>No errors :)</span>
        </div>
    @else
        <p>I believe we can debug with dumps here for this time.</p>

        <p>Errors: @dump($data['errors'])</p>
    @endempty
</div>
