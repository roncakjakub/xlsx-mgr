<body class="position-relative admin">
	<div class="row h-100vh m-0">
		
	<?php 	include_once '../resources/inc/_nav.php'; ?>

	<main class="col-12 col-md-11 font_w_reg offset-md-1 pt-4 pl-3">
		
		<div class="content font_Arial bg-white container pt-3 h-100">
		<input class="w-100" oninput="ajaxSearch(this,url)" placeholder="Zadaj hľadaný výraz"></input>

	<?php 
	if (!empty($data["preparedData"])) { ?>		
	<div id="searchAnswer">
		
	</div>		
<?php } else{ ?>
	<div class="row pt-2">
		<div class="col-12 text-center fw-bold ff-cg">
			Práve tu nie je žiadny obsah
		</div>
	</div>
	<?php } ?>
		</div>
	</main>
	</div>

