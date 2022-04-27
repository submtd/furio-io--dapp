<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <title>{{ config('app.name') }}</title>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZRMHZNBSNS"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-ZRMHZNBSNS');
        </script>
    </head>
    <body class="bg-dark text-light">
        <div id="app">
            <App/>
        </div>
        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
