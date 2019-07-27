	<div class="side_nav ff_cg">
        <div class="logo_div">
            <img src="/public/img/logo.png" alt="LOGO">
            <div>
                <p class="brand_logo">Probim s.r.o.</p>
            </div>
        </div>
        
        <div class="menu_div">
            <div class="menu_item ">
            	<a href="<?php echo $data["baseurl"];?>/admin/home">
            		<i class="fas fa-home"></i><p>Domov</p>
            	</a>
            </div>
            <div class="menu_item ">
            	<a href="<?php echo $data["baseurl"];?>/admin/charts">
            		<i class="fas fa-chart-line"></i> <p>Štatistiky</p>
            	</a>
            </div>
            <div class="menu_item ">
            	<a href="<?php echo $data["baseurl"];?>/admin/book">
            		<i class="fas fa-book position-relative">
            			<?php echo ($data["bookCount"])?'<div class="badge position-absolute">'.$data["bookCount"].'</div>':"";?>
            		</i> <p>Archív</p>
            	</a>
            </div>
            <div class="menu_item ">
            	<a href="<?php echo $data["baseurl"];?>/admin/expired">
            		<i class="fas fa-exclamation-triangle position-relative"><?php echo ($data["expirCount"])?'<div class="badge position-absolute">'.$data["expirCount"].'</div>':"";?>
            		</i> <p>Expirované</p>
            	</a>
            </div>
            <div class="menu_item ">
            	<a href="<?php echo $data["baseurl"]; ?>/admin/settings">
            		<i class="fas fa-cog"></i> <p>Nastavenia</p>
            	</a>
            </div>
            <div class="menu_item ">
                <a href="<?php echo $data["baseurl"]; ?>/admin/update">
                    <i class="fas fa-file-upload"></i> <p>Aktualizácia</p>
                </a>
            </div>
        </div>
        <div class="menu_item logout">
        	<a href="<?php echo $data["baseurl"]; ?>/admin/logout">
        		<i class="fas fa-sign-out-alt"></i><p>Odhlásiť sa</p>
        	</a>
        </div>
    </div>
