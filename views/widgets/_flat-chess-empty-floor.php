<?php
use app\components\widgets\FlatsChess;
?>
<tr> 
    <td>
        <?= $floor ?>
    </td>
    <?php for($i = 0; $i < $maxRoomsOnFloor; $i++): ?>
        <td class="<?= FlatsChess::NO_FLAT_CLASS ?>"></td>
    <?php endfor ?>
</tr>