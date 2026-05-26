<?php
require __DIR__ . '/vendor/autoload.php';
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;

//---------------league/color-extractor----------------------

$palette = Palette::fromFilename('test.jpg'); //gives an iterator to loop over
$extractor = new ColorExtractor($palette);
// top 10 representative colors
$extracted = $extractor->extract(10);
$topExtracted = [];
foreach ($extracted as $color) {
    $hex = Color::fromIntToHex($color);

    $topExtracted[] = [
        'hex' => $hex
    ];
}
// all colors
$colors = [];
foreach($palette as $color => $count) {
    $hex = Color::fromIntToHex($color);
    $colors[] = [
        'hex' => $hex,
        // 'count' => $count
    ];
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Color Extract Test</title>
</head>
<body>

<h3>Original Image</h3>
<img src="test.jpg" width="300">

<h3>Top 10 Extracted (Prominent) Colors</h3>
<table border="1" cellpadding="10">
<tr>
    <th>Color</th>
    <th>Color Code</th>
</tr>

<?php foreach($topExtracted as $c): ?>
<tr>
    <td style="background-color: <?php echo $c['hex']; ?>; width:120px; height:40px;"></td>
    <td><?php echo $c['hex']; ?></td>
</tr>
<?php endforeach; ?>
</table>

<h3>ALL Extracted Colors</h3>
<table border="1" cellpadding="10">
<tr>
    <th>Color</th>
    <th>Color Code</th>
</tr>

<?php foreach($colors as $c): ?>
<tr>
    <td style="background: <?php echo $c['hex']; ?>; width:100px;"></td>
    <td><?php echo $c['hex']; ?></td>
    <!-- <td><?php echo number_format($c['count'], 6);?></td> -->
</tr>
<?php endforeach; ?>

</table>

</body>
</html> 