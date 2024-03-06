<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/activities') }}">
            {{ config('app.name', 'Recorder') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="{{ url('/activities') }}">Activities</a>
            <a class="nav-link" href="{{ url('/characters') }}">Characters</a>
        </div>
    </div>
</nav>
