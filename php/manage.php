<?php
include("register.php");
?>
<table>
    <tr>
        <th>Produkt</th>
        <th>Ledige</th>
        <th>Lån</th>
        <th>Reserver</th>
    </tr>
    <?php
    for ($i = 0; $i < count($db["equipment"]); $i++) {
        $product = $db["equipment"][$i];
    ?>
        <tr>
            <td><?php echo $product["name"]; ?></td>
            <td><?php echo $product["amount"] - ($product["reserved"] + $product["rented"]); ?></td>
            <?php if ($product["amount"] - ($product["reserved"] + $product["rented"]) > 0) { ?>
                <td><button>Lån</button></td>
                <td><button>Reserver</button></td>
            <?php } else { ?>
                <td></td>
                <td><button>Reserver</button></td>
            <?php } ?>
        </tr>
    <?php } ?>
</table>
