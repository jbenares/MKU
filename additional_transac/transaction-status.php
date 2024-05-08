<style type="text/css">
    
</style>
<div class="module_actions status">
    <p>
        Status <br>
        <b><?=lib::getAttribute('transaction_status','status_id',$aVal['status'],'status')?></b>
    </p>
    <p>
        Prepared Date &amp; Time <br>
        <b><?=$aVal['prepared_time']?></b>
    </p>
    <p>
        Prepared by <br>
        <b><?=lib::getUserFullName($aVal['prepared_by'])?>&nbsp;</b>
    </p>

    <?php if( $aVal['edited_by'] ) { ?>
    <p>
        Edited by <br>
        <b><?=lib::getUserFullName($aVal['edited_by'])?></b>
    </p>
    <?php } ?>

    <?php if( $aVal['last_edit_time'] ) { ?>
    <p>
        Last Edit <br>
        <b><?=$aVal['last_edit_time']?></b>
    </p>
    <?php } ?>

</div>