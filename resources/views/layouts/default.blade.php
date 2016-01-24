<!doctype html>
<html>
<head>
    @include('includes.head')
</head>
<body>
<header>
    @include('includes.header')
</header>
<div>
    <div id="main">
        @yield('content')
    </div>
    <footer style="margin-bottom: 130px;">
        @include('includes.footer')
    </footer>
    <div class="app-banner">
        <h3>Get the MoviesOwl App</h3>
        <p>Movies Times, Reviews and Tickets in your Pocket</p>
        <p style="margin-bottom: 0;">
            <a target="_blank" href="https://itunes.apple.com/au/app/moviesowl-find-great-movies/id1032668935?mt=8"><img src="{{ URL::asset('images/appStore_logo.png') }}" alt=""/></a>
            <a target="_blank" href="https://play.google.com/store/apps/details?id=com.moviesowl&hl=en"><img src="{{ URL::asset('images/playstore.png') }}" alt=""/></a>
        </p>
    </div>
</div>
@include('includes.analyticstracking')
</body>
</html>