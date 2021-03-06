<!DOCTYPE html>

<?php

include 'includes/dbConfig.php';
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php?redirect=query.php');
    exit;
}


// create new connection
$conn = new mysqli($servername, $username, $password, $dbname);
//Check connection
if ($conn->connect_error) {
    die("Connection failed ".$conn->connect_error);
}

$fail = $_GET['message'];
if($fail == 'failcode1'){
    $message = '<div class="alert alert-warning" role="alert">
Could not understand that barcode. Please try again. 
</div>';
} else if ($fail == 'failcode2') {
    $message = '<div class="alert alert-warning" role="alert">
Not an EAN-13 code. 
</div>';
}

$thisean = $_GET['ean'];
if ($thisean) {
    $stmt=$conn->prepare('SELECT * FROM wine WHERE ean = ?');
    $stmt->bind_param('s', $thisean);
    $stmt->execute();
    $result = $stmt->get_result();
    $numRows = $result->num_rows;
    if($numRows > 0) {
        
        while ($row = $result->fetch_assoc()) {
            $ean = $row['ean'];
            $name = $row['name'];
            $type = $row['type'];
            $vintage = $row['vintage'];
            $country = $row['cntry'];
            $avg = $row['avgpurprice'];
            $stock = $row['stock'];
            $fave = $row['fave'];
            $wherefrom = $row['wherefrom'];
            $comments = $row['comments'];
        }
        
        $stmt->free_result();
        $stmt->close();
        $queryOutput = '
        <div class="card m-2" id="queryOutputTrue">
        <div class="card-body">
            <h4 class="card-title">
                Query Result
            </h4>
            <hr>

            <div class="row">
                <div class="col-sm-6">
                    <h5>Name: </h5>
                </div>
                <div class="col-sm-6">
                    <p id="outputName">'.$name.' </p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <h5>EAN: </h5>
                </div>
                <div class="col-sm-6">
                    <p id="outputEan">'.$ean.'  </p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <h5>Type: </h5>
                </div>
                <div class="col-sm-6">
                    <p id="outputType">'.$type.'  </p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <h5>Vintage: </h5>
                </div>
                <div class="col-sm-6">
                    <p id="outputVintage">'.$vintage.'  </p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <h5>Country of origin: </h5>
                </div>
                <div class="col-sm-6">
                    <p id="outputCountry">'.$country.'  </p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <h5>Average Purchase Price: </h5>
                </div>
                <div class="col-sm-6">
                    <p id="outputPrice">£'.$avg.'  </p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <h5>Stock: </h5>
                </div>
                <div class="col-sm-6">
                    <p id="outputStock">'.$stock.'  </p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <h5>Favourite: </h5>
                </div>
                <div class="col-sm-6">
                    <p id="outputFave">'.$fave.'  </p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <h5>Last purchased from: </h5>
                </div>
                <div class="col-sm-6">
                    <p id="outputWhereFrom">'.$wherefrom.'  </p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <h5>Comments: </h5>
                </div>
                <div class="col-sm-6">
                    <p id="outputComments">'.$comments.'  </p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <form action="inventory.php" method="get">
                        <input type="hidden" id="search" name="search" value='.$ean.'></input>
                        <input type="hidden" id="editTrue" name="editTrue" value="1"></input>
                        <button type="submit" name="editSubmit" id="editSubmit" value="editSubmit" class="btn btn-block btn-primary my-2 openEditCard"><i class="fas fa-pen"></i> Edit</button>
                    </form>    
                </div>
            </div>
            </div>
        </div>
    </div>
        ';
    } else {
        $queryOutput = '
            <div class="card m-2" id="queryOutputTrue">
            <div class="card-body">
                <h4 class="card-title">
                    Unknown wine
                </h4>
                <hr>
                <p>
                    Looks like this is a wine I don\'t know.
                    <br>
                    Would you like to book it in?
                    <br>
                    <a href="bookin.php?ean='.$thisean.'" role="button" class="btn  btn-primary mt-3">Transfer to Book In</a>
                    <hr>
                    Think this wine should exist in Oinos?
                    <br>
                    Check the barcode has scanned correctly:<strong>   '.$thisean.'</strong>
                </p>
            </div>
        </div>
        ';
    }
}





?>

<html lang=en>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" >
        <meta name="application-name" content="Oinos">
        <meta name="mobile-web-app-capable" content="yes"> 
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script src="https://kit.fontawesome.com/d194762fcf.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <link rel="apple-touch-icon" sizes="150x150" href="/assets/img/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="80x80" href="/assets/img/favicon40x40.png">
        <link rel="icon" type="image/png" sizes="40x40" href="/assets/img/favicon20x20.png">
        <link rel="shortcut-icon" href="favicon40x40.png">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
        <title>Oinos -  Query</title>

        <style>
            #interactive.viewport {position: relative; width: 75%; height: auto; overflow: hidden; text-align: center;}
            #interactive.viewport > canvas, #interactive.viewport > video {max-width: 100%;width: 100%;}
            canvas.drawing, canvas.drawingBuffer {position: absolute; left: 0; top: 0;}
        </style>
    </head>

    <body style="margin-bottom: 70px">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="query.php">
                <i class="fa fa-wine-bottle"></i>
                Query
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li>
                        <a class="nav-link" href="home.php"><i class="fas fa-home"></i>  Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inventory.php"><i class="fas fa-th"></i>  Inventory</a>
                    </li>
                    <li class="nav-item-dropdown">
                        <a class="nav-link dropdown-toggle"href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-camera"></i>  Scan
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="query.php"><i class="fas fa-question"></i>  Query</a>
                            <a class="dropdown-item" href="bookin.php"><i class="fas fa-arrow-down"></i>  Book In</a>
                            <a class="dropdown-item" href="drink.php"><i class="fas fa-glass-cheers"></i>  Drink</a>
                        </div>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a href="javascript:void(Tawk_API.toggle())" class="nav-link"><i class="fas fa-comment-alt" style="color:#ffbf00"></i> Help</a>
                    </li>
                    <li class="nav-item-dropdown">
                        <a class="nav-link dropdown-toggle"href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user"></i>  <?php echo $_SESSION['name']; ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="account.php"><i class="fas fa-user-circle"></i>  Account</a>
                            <?php if($_SESSION['level'] == "S" or "A") {echo '<a class="dropdown-item" href="admin.php"><i class="fas fa-user-shield"></i>  Admin</a>';} ?>
                            <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i>  Log Out</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- //Top navbar ------------------------------------------------------------ -->
        
        
        <div class="container">
            <div class="error-message"><?php if (isset($message)) { echo $message; } ?></div>     
            <div class="query-output"><?php if (isset($queryOutput)) { echo $queryOutput; } ?></div>
            <!-- <div class="hidden_keyIn" style="opacity:0;">
            <form action="" method="get">
                <div class="form-group">
                    <input type="tel" pattern="[0-9]*" maxlength="13" class="form-control mt-2" id="eanHidden" name="ean" placeholder="EAN here...">
                </div>
                
                <div class="row mt-3 p-2">
                    <div class="col-sm-6">
                        <button type="submit" name="submit" id="keyInSubmit" value="Submit" class="btn btn-block btn-primary form-group">Submit</button>
                    </div>
                </form>
                </div>                
            </div> -->

            <div class="modal" id="webscan-modal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header py-2">
                            <h4 class="modal-title" id="webscan-modal-title">
                                Camera scan 
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    
                        <div class="modal-body p-2">
                            <div class="d-flex flex-row justify-content-center">
                                <div class="interactive-container viewport" id="interactive">
                                    <video autoplay="true" preload="auto" src(unknown) muted="true" playsinline="true"></video>
                                    <canvas class="drawingBuffer" width="480" height="480">
                                    <br clear="all">
                                </div>
                            </div>
                            
                            <div id="webscan-result" class="mt-2 small text-muted text-center"></div>
                            <div class="row mt-3 p-2">
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-block btn-danger" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="keyEntry-modal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header py-2">
                            <h4 class="modal-title" id="keyEntry-modal-title">
                                Key In
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-2">
                            <form action="" method="get">
                                <div class="form-group">
                                    <input type="tel" pattern="[0-9]*" maxlength="13" class="form-control mt-2" id="ean" name="ean" placeholder="EAN here...">
                                </div>
                                <p style="color: red"id="keyInMessage"></p>
                                <div class="row mt-3 p-2">
                                    <div class="col-sm-6">
                                        <button type="submit" name="submit" id="keyInSubmit" value="Submit" class="btn btn-block btn-primary form-group">Submit</button>
                                    </div>
                                
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-block btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                                </div>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer" style=" position: fixed;  bottom: 0;  width: 100%;  height: 60px;  line-height: 60px;  background-color: #f5f5f5;">
            <div class="px-0">
                <div class="row no-gutters mt-2">
                <div class="col-6 px-2">
                        <button id="openKeyInModal" class="mt-1 btn btn-block btn-outline-secondary openKeyInModal" data-toggle="modal" data-target="#keyEntry-modal"><i class="fas fa-keyboard"></i> Key in</button>
                    </div>
                    <div class="col-6 px-2">
                        <button id="btn-webscan" class="mt-1 btn btn-primary btn-block" data-toggle="modal" data-target="#webscan-modal"><i class="fas fa-camera"></i> Scan</button>
                    </div>
                    
                </div>
            </div>
        </footer>
        
        

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>   

        <script type="text/javascript">                   
            $(function() {
                var App = {
                    init: function() {
                        var self = this;
                        Quagga.init(this.state, function(err) {
                            if (err) {
                                return self.handleError(err);
                            }
                            
                            Quagga.start();
                            console.log("App started");
                            $('#webscan-result').text('');
                            var drawingCtx = Quagga.canvas.ctx.overlay,drawingCanvas = Quagga.canvas.dom.overlay;
                            drawingCtx.fillStyle = 'rgba(0,0,0,0.6)';
                            drawingCtx.fillRect (0,0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));

                            drawingCtx.clearRect (
					        parseFloat(App.state.inputStream.area.left)*0.01 *  parseInt(drawingCanvas.getAttribute("width")),
					        parseFloat(App.state.inputStream.area.top)*0.01 * parseInt(drawingCanvas.getAttribute("height")),
					        (1-parseFloat(App.state.inputStream.area.left)*0.01-parseFloat(App.state.inputStream.area.right)*0.01)* parseInt(drawingCanvas.getAttribute("width")),
					        (1-parseFloat(App.state.inputStream.area.bottom)*0.01-parseFloat(App.state.inputStream.area.top)*0.01)* parseInt(drawingCanvas.getAttribute("height"))-2
                            );
                            
                            //Draw finder box!
				            drawingCtx.strokeStyle = '#c41f4b';
				            drawingCtx.lineWidth = 3;
				            drawingCtx.strokeRect (
					        parseFloat(App.state.inputStream.area.left)*0.01 *  parseInt(drawingCanvas.getAttribute("width")),
					        parseFloat(App.state.inputStream.area.top)*0.01 * parseInt(drawingCanvas.getAttribute("height")),
					        (1-parseFloat(App.state.inputStream.area.left)*0.01-parseFloat(App.state.inputStream.area.right)*0.01)* parseInt(drawingCanvas.getAttribute("width")),
					        (1-parseFloat(App.state.inputStream.area.bottom)*0.01-parseFloat(App.state.inputStream.area.top)*0.01)* parseInt(drawingCanvas.getAttribute("height"))-2
				            );

                        });
                    },

                    restart: function() {
                        Quagga.stop();
                        console.log("App restarting");
                        App.init();
                    },

                    stop: function() {
                        Quagga.stop();
                        console.log("App stopped");
                    },

                    handleError: function(err) {
                        console.log(err);
                    },

                    state: {
                        inputStream: {
                            name: "Live",
                            type : "LiveStream",
                            constraints: {
                                width: 640,
                                height: 640,
                                facingMode: "environment" 
                            },
                            area: { // defines rectangle of the detection/localization area
                                top: "30%",    // top offset
                                right: "10%",  // right offset
                                left: "10%",   // left offset
                                bottom: "30%"  // bottom offset
                            },
                            singleChannel: true,
                        },

                        locator: {
                            patchSize: "small",
                            halfSample: "true"
                        },
                        numOfWorkers: 2,
                        frequency: 10,
                        decoder: {
                            readers: ['ean_reader'],
                            debug: {
                                drawBoundingBox: false,
                                showFrequency: false,
                                drawScanline: false,
                                showPattern: false
                            },
                            multiple: false
                        },
                        locate: false
                    },
                };

                

                Quagga.onDetected(function(result) {
                    if(result.codeResult.code) {
                        console.log("Code detected - Quagga stopping...");
                        console.log(result.codeResult.code);
                        var code = result.codeResult.code;
                        validateEan(code);
                        App.stop();
                        setTimeout(function(){ $('#webscan-modal').modal('hide'); }, 100);
                        //window.location.href="query.php?ean=" + code;			
                    
                    }

                    
                });

                
                    $('input[id=ean]').keyup(function(){
                        const len = 13;
                        const str = document.getElementById("ean").value;
                        if(this.value.length == len) {
                            validateEan2(str);
                        };
                       
                        });
                    
          

                //function to check ean13 validity from scan
                function validateEan(barcode) {
                    var lastDigit = Number(barcode.substring(barcode.length - 1));
                    var checkSum = 0;
                    if (isNaN(lastDigit)) { console.log('Last digit NaN'); } // not a valid upc/ean

                    var arr = barcode.substring(0,barcode.length - 1).split("").reverse();
                    var oddTotal = 0, evenTotal = 0;

                    for (var i=0; i<arr.length; i++) {
                        if (isNaN(arr[i])) { console.log('NaN found in string'); } // can't be a valid upc/ean we're checking for

                        if (i % 2 == 0) { oddTotal += Number(arr[i]) * 3; }
                        else { evenTotal += Number(arr[i]); }
                    }
                    checkSum = (10 - ((evenTotal + oddTotal) % 10)) % 10;

                    if (checkSum == lastDigit) {
                        var ean = barcode;
                        console.log("Barcode good!");
                        window.location.href="query.php?ean=" + ean;
                    } else {
                        console.log("Couldn't understand barcode");
                        window.location.href="query.php?message=failcode1"
                    
                    }
                };


                
                
                    //function to check ean13 validity from key in
                    function validateEan2(barcode) {
                    var lastDigit = Number(barcode.substring(barcode.length - 1));
                    var checkSum = 0;
                    if (isNaN(lastDigit)) { console.log('Last digit NaN'); } // not a valid upc/ean

                    var arr = barcode.substring(0,barcode.length - 1).split("").reverse();
                    var oddTotal = 0, evenTotal = 0;

                    for (var i=0; i<arr.length; i++) {
                        if (isNaN(arr[i])) { console.log('NaN found in string'); } // can't be a valid upc/ean we're checking for

                        if (i % 2 == 0) { oddTotal += Number(arr[i]) * 3; }
                        else { evenTotal += Number(arr[i]); }
                    }
                    checkSum = (10 - ((evenTotal + oddTotal) % 10)) % 10;

                    if (checkSum == lastDigit) {
                        console.log("Barcode good!");
                        document.getElementById("keyInMessage").innerHTML = "";
                        document.getElementById("keyInSubmit").disabled = false;
                    } else {
                        console.log("Couldn't understand barcode");
                        document.getElementById("keyInMessage").innerHTML = "Couldn't understand barcode";
                        document.getElementById("keyInSubmit").disabled = true;
                    
                    }
                };
                
                    
                

                $('#webscan-modal').on('shown.bs.modal', function (e) {
                    App.init();
                });

                $('#webscan-modal').on('hide.bs.modal', function() {
                    if(Quagga){
                        App.stop();
                    }
                });

            });
        </script>

        <!-- <script type="text/javascript">
            $(document).ready(function() {
                document.getElementById("eanHidden").focus();
                
            }); -->

            

            
        </script>
        
        <!-- Additional Bootstrap scripts ---------------------------------------------------------------- -->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <!-- //Additional Bootstrap scripts -------------------------------------------------------------- -->
        
        <!--Start of Tawk.to Script-->
        <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5e6f918e8d24fc226587deae/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
        </script>
        <!--End of Tawk.to Script-->
        
    </body>
</html>