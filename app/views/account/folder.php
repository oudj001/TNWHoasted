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
        <title>DroptoBox - receive files in your own Dropbox</title>
        </head>

    <body class="background">
        <!-- Login here -->
        <div class="wrapper">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container">
                    <div class="row">
                        <img id="logo" src="/dist/img/logo@2x.png" width="300" class="pull-left">
                        <a href="<?= $dropbox_url ?>" target="_blank" class="publiclink pull-right">View on Dropbox</a>
                    </div>
                </div>
            </nav>

            <div class="clear"></div>
            <div class="wrappert">

              
                <h1>Who do you want to invite?</h1>
								
								
								
                <div class="invite-fields">
									<form action="<?= $invite_url ?>" class="form-horizontal" method="POST" id="invite-friends">
                        <div class="email-box">
                            <div class="email">
                                <input type="text" name="email[]" placeholder="Add an emailadress"/>
                                <div class="holder plus-holder">
                                    <span class="plus-email glyphicon glyphicon-plus"></span>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-save-invite">Send the invitations!</button>
									</form>
                  
                </div>

                    <form class="form-horizontal" action="<?= $password_url ?>" method="POST" >
                        <div class="password">
                            <h1>Password</h1>
                            <span>Optionally</span>
                            <input type="password" name="password" placeholder="Add a password"/>
                        </div>
                        <button type="submit" class="btn btn-success">Set password</button>
                    </form>



            </div>

            <footer>
                <div class="container">
                    <div class="row">
                        <p id="footertxt">Want to create your own public drag and drop Dropbox page? <a href="">Click here</a></p>
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
        <script src="/dist/js/common.js"></script>

    </body>
</head>
