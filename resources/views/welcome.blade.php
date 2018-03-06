<!doctype html>
<html lang="{{ app()->getLocale() }}" infinite-wrapper ref="htmlTag">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Moonre</title>

        <!-- Icons -->
        <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="css/app.css" rel="stylesheet" type="text/css">

    </head>
    <body>
            <div id="moonre">
                    <router-view></router-view>
            </div>

            <!-- Javascript/Vuejs --> 
            <script id="dsq-count-scr" src="//moonre-com.disqus.com/count.js" async></script>
            <script type="text/javascript" src="../js/app.js"></script>
    </body>
</html>
