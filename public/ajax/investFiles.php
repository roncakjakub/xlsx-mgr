<?php 
if (isset($_POST["nazov"],$_POST["area"])){
	$filesFolder = NULL; 
	echo '<div class="container">
		<div class="row">
		<div class="col-8">Názov</div>
		<div class="col-2">Dátum vytvorenia</div>
		<div class="col-2">Veľkosť</div></div>';

	if (isset($_POST["subory"])) {
		foreach ($_POST["subory"] as $key => $value) {
			echo '<div class="row">
				<div class="col-8">'.basename($value).'</div>
				<div class="col-2">'.date("d.m.Y", filectime($value)).'</div>
				<div class="col-2">'.filesize($value).'</div>
			</div>';
		}
		$filesFolder = explode("/", $_POST["subory"][0]);
		$filesFolder=$filesFolder[sizeof($filesFolder)-4];
		$filesFolder="&payF=".$filesFolder;
	}
?>
<br class="w-100">
<div class="row align-items-center justify-content-between">
	
		<a href="/download?area=<?php echo $_POST["area"] ?>&name=<?php echo $_POST["nazov"]?>" class="col-3 clr-black">Stiahni všetky súbory</a>
</div>
</div>
<?php } ?>