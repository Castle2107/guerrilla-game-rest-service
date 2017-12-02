<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Guerrilla Game Rest Service</title>

        <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
        
        <style type="text/css" media="screen">

            .affix {
                width: 25%;
            }

            .btn-link {
                display: block;
                text-align: left;
            }

            kbd {
                margin-top: .5em;
                display: block;
            }

            .relevant-content p {
                margin-bottom: 0px;
            }

            /*====================================================
             TEXT EDITOR
            ====================================================*/
            .relevant-content {
                background-color: #F5F5F5;
                border-radius: .25em;
                padding: .25em;
                margin-top: 1em;
            }

            .cell {
                border-bottom: 0.5px solid #DDD;
                border-right: 0.5px solid #DDD;
            }

            .border-left { border-left: 0.5px solid #DDD; }

            .comment { color: #BDBDBD; }

            .indent { text-indent: 3%; }

            .indent-double { text-indent: 6%; }

            .indent-triple { text-indent: 9%; }

            .keyword, .operator { color: #63A35C; }

            .method, .attribute { color: #795DA3; }

            .value { color: #183691; }
        </style>

    </head>
    <body>

        <div class="container">
            <div class="row">

                <div class="col-sm-4 hidden-xs" style="margin-top: 2em;">
                    <div class="affix">
                        <div class="panel" style="padding: .5em; margin-bottom: 0;">
                            Guerrilla
                        </div>
                        <blockquote>
                            <a href="#attackGuerrilla" class="btn btn-link">Attack Guerrilla</a>
                            <a href="#buyGuerrilla" class="btn btn-link">Buy Guerrilla</a>
                            <a href="#createGuerrilla" class="btn btn-link">Create Guerrilla</a>
                            <a href="#inspectGuerrilla" class="btn btn-link">Inspect Guerrilla</a>
                            <a href="#listGuerrilla" class="btn btn-link">List Guerrilla</a>
                            <a href="#assaultReportsList" class="btn btn-link">Assault Reports List</a>
                            <a href="#assaultReport" class="btn btn-link">Assault Report</a>
                        </blockquote>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-8" style="background: #FFF;">
                    <!-- Header -->
                    <div class="page-header">
                        <h1>
                            Guerrilla Game <small>Rest Service</small>
                            <small class="pull-right">
                                <span class="label label-default pull-right">1.0</span>
                            </small>
                        </h1>
                    </div>
                    <!-- Attack Guerrilla -->
                    <div id="attackGuerrilla">
                        @include('attack-guerrilla')
                    </div>
                    <!-- Buy Guerrilla -->
                    <div id="buyGuerrilla">
                        @include('buy-guerrilla')
                    </div>
                    <!-- Create Guerrilla -->
                    <div id="createGuerrilla">
                        @include('create-guerrilla')
                    </div>
                    <!-- Inspect Guerrilla -->
                    <div id="inspectGuerrilla">
                        @include('inspect-guerrilla')
                    </div>
                    <!-- List Guerrilla -->
                    <div id="listGuerrilla">
                        @include('list-guerrilla')
                    </div>
                    <!-- Assault Reports List -->
                    <div id="assaultReportsList">
                        @include('assault_reports_list')
                    </div>
                    <!-- Assault Report -->
                    <div id="assaultReport">
                        @include('assault_reports')
                    </div>
                </div>

            </div>
        </div>
        
        <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>

    </body>
</html>
