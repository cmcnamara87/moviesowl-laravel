<!doctype html>
<html>
<head>
<!-- Begin Inspectlet Embed Code -->
<script type="text/javascript" id="inspectletjs">
window.__insp = window.__insp || [];
__insp.push(['wid', 797628105]);
(function() {
function ldinsp(){if(typeof window.__inspld != "undefined") return; window.__inspld = 1; var insp = document.createElement('script'); insp.type = 'text/javascript'; insp.async = true; insp.id = "inspsync"; insp.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cdn.inspectlet.com/inspectlet.js'; var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(insp, x); };
setTimeout(ldinsp, 500); document.readyState != "complete" ? (window.attachEvent ? window.attachEvent('onload', ldinsp) : window.addEventListener('load', ldinsp, false)) : ldinsp();
})();
</script>
<!-- End Inspectlet Embed Code -->

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

    <div class="container" style="margin-top:50px;">
        <!-- Ad -->
{{--        @if(isset($cinema) && $cinema->country != "Australia")--}}
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Moviesowl -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-8017658135166310"
             data-ad-slot="5578445116"
             data-ad-format="auto"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
        {{--@endif--}}
        <!-- /Ad -->
    </div>
    
    {{--<div class="app-banner" style="margin-top:50px;">--}}
        {{--<h3>Get the MoviesOwl App</h3>--}}
        {{--<p>Movies Times, Reviews and Tickets in your Pocket</p>--}}
        {{--<p style="margin-bottom: 0;">--}}
            {{--<a target="_blank" href="https://itunes.apple.com/au/app/moviesowl-find-great-movies/id1032668935?mt=8"><img src="{{ URL::asset('images/appStore_logo.png') }}" alt=""/></a>--}}
            {{--<a target="_blank" href="https://play.google.com/store/apps/details?id=com.moviesowl&hl=en"><img src="{{ URL::asset('images/playstore.png') }}" alt=""/></a>--}}
        {{--</p>--}}
    {{--</div>--}}
    <footer>
        @include('includes.footer')
    </footer>
</div>
@include('includes.analyticstracking')
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-56cfa57046ca8b21"></script>

</body>
</html>