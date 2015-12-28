<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>@yield('title', 'MoviesOwl')</title>

<!-- load bootstrap from a cdn -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

<!-- webmaster verification -->
<meta name="google-site-verification" content="5qA-_e3WDYgZXrQAjROFaafiyXPLWsWTQ5Xu75rq154" />



<style>
    body {
        background-color: #313748;
        color: white;
    }
    h1, h2, h3, h4 {
        text-align: center;
    }
    .container {
        max-width: 760px;
    }

    .thumbnail {
        border: none;
    }
    .thumbnail .meter {
        display: inline-block;
        padding: 3px 5px;
        border-radius: 3px;
    }
    .thumbnail.good .meter {
        background-color: rgb(128, 205, 66);
        color: white;
    }
    .thumbnail.average .meter {
        background-color: yellow;
        color: black;
        font-weight: bold;
    }
    .thumbnail.bad .meter {
        background-color: red;
        color: black;
        font-weight: bold;
    }
    .thumbnail img {
        border-radius: 5px;;
    }

    .thumbnail h3 {
        font-size: 16px;
        margin: 0;
        margin-bottom: 7px;;
    }
    .thumbnail p {
        font-size: 14px;
        color: #aaa;
    }
</style>