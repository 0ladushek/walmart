<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>


<table>
    <tr>
        <td>size</td>
        <td>color</td>
        <td>price</td>
        <td>in stock</td>
        <td>fulfillment price</td>
        <td>fulfillment date</td>
    </tr>

    <?php foreach ($products as $key => $val) : ?>
        <tr>
            <td><?= $val->variants->size; ?></td>
            <td><?= $val->variants->actual_color; ?></td>

            <td>$<?= $val->offers->pricesInfo->priceMap->CURRENT->price; ?></td>
            <td><?= $val->offers->productAvailability->availabilityStatus; ?></td>
            <?php $shippingOpt = $val->offers->fulfillment->shippingOptions ?>

            <?php if($val->offers->productAvailability->availabilityStatus != 'OUT_OF_STOCK') : ?>
                <td>
                <?php foreach($shippingOpt as $opt) : ?>
                $<?= $opt->fulfillmentPrice->price ?>,
                <? endforeach; ?>
                </td>
                <?php foreach($shippingOpt as $opt) : ?>
                <td><?= $opt->fulfillmentDateRange->exactDeliveryDate ?></td>
                <? endforeach; ?>
            <?php endif; ?>

        </tr>
    <? endforeach; ?>

</table>


</body>
</html>