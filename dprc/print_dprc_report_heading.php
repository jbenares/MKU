<table class="table-header">
    <tr>
        <td>Account Name:</td>
        <td colspan="3" style="font-weight:bold;"><?=htmlentities($options->getAttribute('customer','customer_id',$r['customer_id'],'customer_last_name'))?>, <?=htmlentities($options->getAttribute('customer','customer_id',$r['customer_id'],'customer_first_name'))?></td>
    </tr>
    <tr>
        <td>Application No.:</td>
        <td><?=str_pad($r['application_id'],7,0,STR_PAD_LEFT)?></td>
        
        <td>Due Every:</td>
        <td><?=($r['date_due']) ? "$r[date_due]th of the month" : "" ?></td>
    </tr>
    <tr>
        <td>Loan Status:</td>
        <td></td>
        
        <td>Interest Rate:</td>
        <td><?=$r['interest_rate']?>%</td>
    </tr>
    <tr>
        <td>Date Approved:</td>
        <td><?=($r['date_approved'] != "0000-00-00") ? $r['date_approved'] : ""?></td>
        
        <td>Loan Value:</td>
        <td><?=number_format($r['loan_value'],2)?></td>
    </tr>
    <tr>
        <td>Loan Term:</td>
        <td><?=$r['loan_term']?> YEARS</td>
        
        <td>Amortization:</td>
        <td><?=number_format($r['amortization'],2)?></td>
    </tr>
</table>
<table class="table-header">
    <tr>
        <td>Package Code:</td>
        <td><?=$options->getAttribute('dprc_package_types','package_type_id',$r['package_type_id'],'package_type')?></td>
    </tr>
    <tr>
        <td>Project Code:</td>
        <td><?=$options->getAttribute('subd','subd_id',$r['subd_id'],'subd')?></td>
    </tr>
    <tr>
        <td>Payment Code:</td>
        <td><?=$options->getAttribute('dprc_payment_codes','payment_code',$r['payment_code'],'payment_type')?></td>
    </tr>
    <tr>
        <td>DP Code:</td>
        <td><?=$r['dp_code']?> MONTHS</td>
    </tr>
    <tr>
        <td>Model No:</td>
        <td><?=$options->getAttribute('model','model_id',$r['model_id'],'model')?></td>
    </tr>
</table>

<table class="table-header">
    <tr>
        <td>Phase:</td>
        <td><?=$r['phase']?></td>
    </tr>
    <tr>
        <td>Block/Lot:</td>
        <td><?=$r['block']?>/<?=$r['lot']?></td>
    </tr>
    <tr>
        <td>Total Liability:</td>
        <td><?=number_format($r['net_loan'],2)?></td>
    </tr>
    <tr>
        <td>Penalized? :</td>
        <td><?=($r['penalized']) ? "Yes" : "No" ?></td>
    </tr>
    <tr>
        <td>Penalty per day:</td>
        <td><?=number_format($r['penalty_per_day'] * 100,8)?>%</td>
    </tr>
</table>