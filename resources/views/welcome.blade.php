@extends('templates.layouts.app')

@section('title', config('app.name') . " - Homepage")

@section('content')
    <div class="col-6 offset-3">
        <p><b>GitHub repository</b> <a href="https://github.com/pyskunov/crawler-test-assignment">Click me</a></p>

        <h1>Test Task Page</h1>

        <form action="{{ route('crawler') }}" method="POST">
            @csrf

            <label for="target" class="mb-2">
                <b>Input your target</b>
            </label>

            <input
                id="target"
                name="target"
                type="text"
                class="@error('target') is-invalid @enderror mb-2 form-control"
                value="{{ old('target', 'https://agencyanalytics.com') }}"
            >
            @error('target')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <button onclick="revealSecret()" type="submit" class="btn btn-primary">
                Let's Rock!
            </button>

            <script>
                const revealSecret = () => {
                    alert(`You can do the same by running CLI \n"php artisan crawler:parse ${document.querySelector('input#target').value || "{target}"}"`)
                }
            </script>
        </form>
    </div>
@endsection
