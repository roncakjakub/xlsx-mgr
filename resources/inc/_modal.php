<div class="modal fade <?php echo(isset($data["clrMod"]))?$data["clrMod"]:"" ?>" id="<?php echo $data["modalID"]; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header ff_cg">
<h5 class="modal-title" id="exampleModalLabel"><?php if(isset($data["modalName"]))echo $data["modalName"]; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body m-0 pt-1" id="modal-body">
        <?php echo (isset($data["modalCont"])?$data["modalCont"]:"");?>
      </div>
    </div>
  </div>
</div>
