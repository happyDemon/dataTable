	return: '<img src="\'+data+\'"<?php if($param[0] != false) {?> width="<?=$param[0];?>"<?php } if($param[1] != false) {?> height="<?=$param[1] ?>"<?php }?> <?php if(isset($param[2])) {?>class="<?=$param[2];?>"<?php }?> />\';