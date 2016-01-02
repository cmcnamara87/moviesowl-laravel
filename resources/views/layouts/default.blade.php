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

    <footer class="row">
        @include('includes.footer')
    </footer>

</div>
@include('includes.analyticstracking')
</body>
</html>