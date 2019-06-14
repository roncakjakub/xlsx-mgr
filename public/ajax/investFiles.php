<?php 
if (isset($_POST["nazov"],$_POST["xlsxs"],$_POST["area"])){
	$filesFolder = NULL; 
	echo '<div class="container">
		<div class="row">
		<div class="col-8">Názov</div>
		<div class="col-2">Dátum vytvorenia</div>
		<div class="col-2">Veľkosť</div></div>';

	echo '<div class="row">
		<div class="col-8">'.basename($_POST["xlsxs"]).'</div>
		<div class="col-2">'.date("d.m.Y", filectime($_POST["xlsxs"])).'</div>
		<div class="col-2">'.filesize($_POST["xlsxs"]).'</div>
	</div>
	';
	if (isset($_POST["subory"])) {
		foreach ($_POST["subory"] as $key => $value) {
			if (basename($value)=="downloadedFiles.xml") continue;
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
	<?php $xlsxsFolder = explode("/", $_POST["xlsxs"]); 
	$xlsxsFolder=$xlsxsFolder[sizeof($xlsxsFolder)-2];
	?>
		<a href="/download?area=<?php echo $_POST["area"] ?>&name=<?php echo $_POST["nazov"]?>" class="col-3 clr-black">Stiahni všetky súbory</a>
		<?php /*if ($_POST["permitAble"]) { ?>
		<form id="confForm" method="post" action="/admin/fileConfirm">
			<input type="hidden" name="confName" value="<?php echo $_POST["nazov"] ?>">
		</form>
		<button onclick="$('#confForm').submit();" class="text-center btn btn-primary">Potvrď</button>
		<?php } */?>
</div>
</div>
<?php } ?>