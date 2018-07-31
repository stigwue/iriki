<?php
//config file for this app
require_once(__DIR__ . '/config.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="assets/img/favicon.png">

        <title>Aladi :: Iriki Application</title>

        <link type='text/css' href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,400italic,500' rel='stylesheet'>

        <!-- Bootstrap core CSS -->
        <link href="assets/css/bootstrap.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="assets/css/narrow-jumbotron.css" rel="stylesheet">

        <link href="assets/css/style.css" rel="stylesheet">

        <script src="assets/js/jquery.min.js"></script>

        <script src="assets/js/wheel/d3.v4.min.js"></script>
        <script src="assets/js/wheel/d3.dependencyWheel.js"></script>
        <script type="text/javascript">
            function drawWheel() {
                //var chart = d3.chart.dependencyWheel();
                // You can customize the chart width, margin (used to display package names),
                // and padding (separating groups in the wheel)
                var chart = d3.chart.dependencyWheel().width(650).margin(150).padding(.02);

                // Data must be a matrix of dependencies. The first item must be the main package.
                // For instance, if the main package depends on packages A and B, and package A
                // also depends on package B, you should build the data as follows:
                // var data = {
                //   packageNames: ['Main', 'A', 'B'],
                //   matrix: [[0, 1, 1], // Main depends on A and B
                //            [0, 0, 1], // A depends on B
                //            [0, 0, 0]] // B doesn't depend on A or Main
                // };

                var data = {
                    matrix: [],
                    packageNames: []
                };

                d3.json("<?php echo IRIKI_URL ?>app/wheel", function (models) {
                    data.packageNames = models.data.list;
                    data.matrix = models.data.matrix

                    d3.select('#app_wheel')
                        .datum(data)
                        .call(chart);
                });
            }

            $(document).ready(function() {
                drawWheel();
            });
        </script>

    </head>

    <body>
        <div class="container">
            <?php $tab = 'app'; ?>
            <div class="header clearfix" >
                <nav>
                    <ul class="nav nav-pills float-xs-right">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Dependency wheel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://github.com/stigwue/iriki">Github</a>
                        </li>
                    </ul>
                </nav>
                <h3 class="text-muted">Iriki API framework</h3>
            </div>


            <div class="row marketing">
                <div class="col-lg-12">
                    <div id="app_wheel">
                    </div>
                </div>
            </div>


            <?php date_default_timezone_set('Africa/Lagos'); ?>
            <footer class="footer">
                <p>&copy; 2016 - <?php echo date("Y"); ?> Eyeti. <!--a href="https://github.com/stigwue/iriki/archive/master.zip">Download Iriki.</a--></p>
            </footer>

        </div> <!-- /container -->

        <!-- Bootstrap core JavaScript
================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="assets/js/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>
