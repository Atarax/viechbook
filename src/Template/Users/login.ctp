<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0">

    <!-- Always force latest IE rendering engine or request Chrome Frame -->
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,800">

    <!-- Use title if it's in the page YAML frontmatter -->
    <title>Viechbook</title>

    <link href="/core-admin-template/stylesheets/application.css" media="screen" rel="stylesheet" type="text/css" />

    <script src="/core-admin-template/javascripts/application.js" type="text/javascript"></script>
</head>

<body>

<nav class="navbar navbar-default navbar-inverse navbar-static-top" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <a class="navbar-brand" href="#">Viechbook</a>


    </div>

</nav>
<div class="container">

    <div class="col-md-4 col-md-offset-4">
        <div class="pre">
			<?php echo $this->Session->flash(); ?>
        </div>
    </div>

    <div class="col-md-4 col-md-offset-4">


        <div class="padded">
            <div class="login box" style="margin-top: 80px;">

                <div class="box-header">
                        <span class="title">Login</span>
                </div>

                <div class="box-content padded">
			        	<?= $this->Form->create('User', array("class" => "separate-sections")) ?>
                        <div class="input-group">
                            <div class="input-group addon-left">
                                <span class="input-group-addon" href="#">
                                    <i class="icon-user"></i>
                                </span>
                                <input type="text" placeholder="username" name="username">
                            </div>
						</div>

						<div class="input-group">
                            <div class="input-group addon-left">
                                <span class="input-group-addon" href="#">
                                    <i class="icon-key"></i>
                                </span>
                                <input type="password" placeholder="password" name="password">
                            </div>
                        </div>

                        <div>
                            <input type="submit" value="Login" class="btn btn-default btn-block" onclick="document.getElementById('UserLoginForm').submit(); return false;">
                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>
</div>

</body>
</html>
