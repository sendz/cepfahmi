<?php
  include("config.php");
?>
<html>
  <head>
    <title>Email Config</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen" title="no title" charset="utf-8">
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-xs-12 col-md-8 col-md-offset-2">
          <h2>Email Setting</h2>
          <form class="form" action="save-setting.php" method="post">
            <div class="form-group">
              <label for="username">GMail Username</label>
              <input <?php if ($username) echo "value='$username'"; ?> class="form-control" type="username" name="username" id="email">
            </div>
            <div class="form-group">
              <label for="password">GMail Password</label>
              <input class="form-control" type="password" name="password" value="">
            </div>
            <div class="form-group right">
              <button type="submit" name="button" class="btn btn-primary">Save</button>
              <a type="submit" name="button" class="btn btn-default">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
