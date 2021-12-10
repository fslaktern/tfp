<?php
echo "php/manage.php";
echo "<br><br>";
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
                <td><button>Lån</button></td>
                <td></td>
            <?php } ?>
        </tr>
    <?php } ?>
</table>
