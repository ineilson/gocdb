<?php
$propertyArray = $params['propArr'];
$service = $params['service'];
?>
<div class="rightPageContainer">
    <h1 class="Success">Deletion Successful</h1><br />
    <p>
        The following properties have been successfully removed from service <?php xecho($service->getHostName());?>:<br/>

    <table>
        <tbody>
        <?php
        //$num = 2;
        foreach($propertyArray as $prop) {
            ?>

            <tr>
                <td style="width: 35%;"><?php xecho($prop->getKeyName()); ?></td>
                <td style="width: 35%;"><?php xecho($prop->getKeyValue()); ?></td>
            </tr>
            <?php
            //if($num == 1) { $num = 2; } else { $num = 1; }
        }
        ?>
        </tbody>
    </table>
    </p>
    <p>
        <a href="index.php?Page_Type=Service&id=<?php echo $service->getId();?>">
            View service</a>
    </p>

</div>