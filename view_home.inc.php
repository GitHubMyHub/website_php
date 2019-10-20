

<div class="row text-center">
    <div class="jumbotron home">
      <h1><?php echo $lang[$_SESSION['speak']]['Enter your article!']; ?></h1>
      <div class="col-lg-3">
      </div><!-- /.col-lg-3 -->
      <div class="col-lg-6">
        <div class="input-group">
          <input type="text" class="form-control search" id="searchid" placeholder="<?php echo $lang[$_SESSION['speak']]['Enter your article!']; ?>">
          <span class="input-group-btn">
            <button class="btn btn-default" type="button"><?php echo $lang[$_SESSION['speak']]['Go!']; ?></button>
          </span>
          
        </div><!-- /input-group -->
        <div id="result"></div><br />
		  <p><span class="input-group-addon"><input type="radio" id="first" name="numbers"><?php echo $lang[$_SESSION['speak']]['8 digits']; ?>
     	  <input type="radio" id="second" name="numbers"><?php echo $lang[$_SESSION['speak']]['13 digits']; ?>
			  <input type="radio" id="third" name="numbers"><?php echo $lang[$_SESSION['speak']]['Text']; ?></span></p>
      </div><!-- /.col-lg-6 -->
    </div>
</div>