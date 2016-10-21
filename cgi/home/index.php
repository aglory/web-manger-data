<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>后台管理</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<script src="jquery/jquery.min.js"></script>
		
		<script src="jquery/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="jquery/jquery-ui.theme.min.css" />
		
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css" />
		<script src="bootstrap/js/bootstrap.js"></script>
		
		<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.css" />
		
		<link rel="stylesheet" href="Font-Awesome/css/font-awesome.min.css" />
		<!--[if IE 7]>
		<link rel="stylesheet" href="Font-Awesome/css/font-awesome-ie7.min.css">
		<![endif]-->
		
		<link rel="stylesheet" href="pager/pager.css" />
		<script src="pager/pager.js"></script>
		
		<link rel="stylesheet" href="common/common.css" />
		<script src="common/common.js"></script>
		
		<script src="common/template.js"></script>
		
		<link rel="stylesheet" href="css/home/usermanger.css" />
		<script src="js/home/usermanger.js"></script>
		
		<script>
			$(function(){
				$("#mainForm button[type='submit']").click(function(e){
					e.preventDefault();
					doQuery(null,null,this);
				});
				$("#mainForm button[type='button']").click(function(e){
					e.preventDefault();
					doQuery(1,null,this);
				});
			});
		</script>
	</head>
	<body>
		<?php
			Render('header');
		?>

		<div class="container">
		  <div style="padding-top:120px;">
		  
		  
  <form class="form-inline">
    <fieldset>
      <div id="legend" class="">
        <legend class="">表单名</legend>
      </div>
    

    <div class="control-group">

          <!-- Text input-->
          <label class="control-label" for="input01">Text input</label>
          <div class="controls">
            <input type="text" placeholder="placeholder" class="input-xlarge">
            <p class="help-block">Supporting help text</p>
          </div>
        </div><div class="control-group">

          <!-- Text input-->
          <label class="control-label" for="input01">Text input</label>
          <div class="controls">
            <input type="text" placeholder="placeholder" class="input-xlarge">
            <p class="help-block">Supporting help text</p>
          </div>
        </div>

    <div class="control-group">

          <!-- Text input-->
          <label class="control-label" for="input01">Text input</label>
          <div class="controls">
            <input type="text" placeholder="placeholder" class="input-xlarge">
            <p class="help-block">Supporting help text</p>
          </div>
        </div>

    <div class="control-group">
          <label class="control-label">Checkboxes</label>
          <div class="controls">

            <!-- Multiple Checkboxes -->
            <label class="checkbox">
              <input type="checkbox" value="Option one">
              Option one
            </label>
            <label class="checkbox">
              <input type="checkbox" value="Option two">
              Option two
            </label>
          </div>

        </div>

    <div class="control-group">
          <label class="control-label">Button</label>

          <!-- Button -->
          <div class="controls">
            <button class="btn btn-success">Button</button>
          </div>
        </div>

    

    </fieldset>
  </form>

		  
		</div>
		<?php
			Render('footer');
		?>
	</body>
</html>