<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <link rel="stylesheet" href="/dist/css/styles.css">

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'> 
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
          <title>DroptoBox - receive files in your own Dropbox</title>
  </head>
  
  <body class="background">
    <?php include __DIR__ . '/../ga.php'; ?>
    <!-- Login here -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="row">
          <img id="logo" src="/dist/img/logo@2x.png" width="300" class="pull-left">
        </div>
      </div>
    </nav>

    <section id="login" class="login">
      <div class="container">
        <div class="row">
          <div class="col-lg-10 col-lg-offset-1 col-md-8 col-md-offset-2 col-xs-10 col-xs-offset-1 loginbox">
            <h2 class="heading">You've received a password in your mail account enter it below</h2>
            <div class="row">
              <div class="col-lg-4 col-lg-offset-4">
                <form action="<?= $login_url ?>" method="POST">
                  <div class="input-group">
                    <input type="password" class="form-control" name="password" placeholder="Enter the password here">
                    <span class="input-group-btn">
                      <input type="submit" value="Sign in!" class="btn purplecolor">
                    </span>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer>
      <div class="container">
        <div class="row">
          <p id="footertxt">Want to create your own public drag and drop Dropbox page? <a href="">Click here</a></p>
        </div>
      </div>
    </footer>
  </body>
</head>
