<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="/dist/css/styles.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'> 
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <script src="/dist/js/dropzone.js"></script>
        <link href="/dist/css/dropzone.css" type="text/css" rel="stylesheet" />
    
        
    <body class="background">
        <!-- Login here -->
        <div class="wrapper">
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="row">
                    <img id="logo" src="/dist/img/logo@2x.png" width="300" class="pull-left">
                    <a href="<?= $dropbox_url ?>" target="_blank" class="publiclink pull-right">View on Dropbox</a>
                    <!-- <a id="uploadbtn" href="" class="publiclink pull-right">Public upload Link</a> -->
                </div>
            </div>
        </nav>

            <div class="clear"></div>
        <div class="wrappert">
            <section id="uploadbox">
                <div class="container">
                    <!--    <div class="row wrappert">
                          <div class="upload">
                          <span class="glyphicon glyphicon-plus-sign"></span>
                          <p id="instructions">Drag your files to upload them to Mentor's Dropbox folder</p>
                          </div>
                        </div>-->
                    <div class="row">
                        <div class="col-lg-12">
                            <form action="<?= $upload_url ?>" class="dropzone" id="myDropzone"></form>
                            <div class="confirmation-box">
                                <h1 class="confirmation">Are you sure you selected every file?</h1>
                                <div class="btn btn-success upload-dropzone pull-right">Upload </div>
                                <div class="upload-box">
                                <!-- <input class="form-control" name="name" type="text" placeholder="Optional: Add a name"/> -->
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>

        <footer>
            <div class="container">
                <div class="row">
                    <p id="footertxt">Want to create your own public drag and drop Dropbox page? <a href="/" target="_blank">Click here</a></p>
                </div>
            </div>
        </footer>
    </div>
<!--        <div id="pop-up">
      <h3>Enter your name</h3>
      <form>
          <input class="form-control" name="name" type="text" placeholder="Your name"/>
      </form>
      <div class="btn btn-success enter-email">Ok!</div>
    </div>-->
        <script src="/dist/js/dropzone-settings.js"></script>

    </body>
</head>
