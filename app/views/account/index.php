<!DOCTYPE html>
<html lang="en">
<head>
  <script src="http://code.jquery.com/jquery-latest.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <link rel="stylesheet" href="/dist/css/styles.css">
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'> 
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">ef="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <body class="background">
    <!-- Login here -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="row">
          <img id="logo" src="https://www.hoasted.com/blog/img/logo-hoasted-2015-159x45.png" class="pull-left">
        </div>
      </div>
    </nav>



    <section id="login" class="login">
      <div class="container">
        <div class="row">
          <div class="col-lg-10 col-lg-offset-1 col-md-8 col-md-offset-2 col-xs-10 col-xs-offset-1 loginbox">
            <h2 class="heading">Create a new dropbox folder where people can drop there stuff</h2>
            <?php if(!!$account->folders): ?>
            <div class="row">
              <div class="col-lg-4 col-lg-offset-4">
                <h3>Folders</h3>
                <ul>
                <?php foreach($account->folders as $_folder): ?>
                <li><a href="<?= router()->generate('folder', ['urlname' => $_folder->urlname]); ?>"><?=$_folder->name ?></a></li>
                <?php endforeach ?>
                </ul>
              </div>
            </div>
            <?php endif ?>
            
            <div class="row">
              <div class="col-lg-4 col-lg-offset-4">
                <form action="<?= $folders_url ?>" method="POST">
                  <div class="input-group">
                    <input type="text" name="name" class="form-control" name="text" placeholder="Enter folder name">
                    <span class="input-group-btn">
                      <input type="submit" value="Create!" class="btn purplecolor">
                    </span>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

<!--    <footer>
      <div class="container">
        <div class="row">
          <p id="footertxt">Want to create your own public drag and drop Dropbox page? <a href="">Click here</a></p>
        </div>
      </div>
    </footer> -->
  </body>
</head>




