<?php

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';

use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;

$image = "";
$dominantColors = [];
$extraColors = [];

// Only process after form submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])){

    $targetDir = "uploads/";

    // Create uploads folder if it doesn't exist
    if(!file_exists($targetDir)){
        mkdir($targetDir, 0777, true);
    }

    // Create unique filename
    $fileName = time() . "_" . basename($_FILES["image"]["name"]);

    $targetFile = $targetDir . $fileName;

    // Upload image
    if(move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)){

        $image = $targetFile;

        // Check image exists
        if(file_exists($image)){

            // Extract colors
            $palette = Palette::fromFilename($image);

            $extractor = new ColorExtractor($palette);

        //     $colors = $extractor->extract(20);

        //     foreach($colors as $color){

        //         $hex = Color::fromIntToHex($color);

        //         $topExtracted[] = [
        //             'hex' => $hex
        //         ];
        //     }
        // }
        // TOP 10 DOMINANT COLORS
$topColors = $extractor->extract(10);

foreach($topColors as $color){

    $hex = Color::fromIntToHex($color);

    $dominantColors[] = [
        'hex' => $hex
    ];
    }

// EXTRA COLORS
$moreColors = $extractor->extract(60);

foreach($moreColors as $index => $color){

    // Skip first 10 already shown
    if($index < 10){
        continue;
    }

    $hex = Color::fromIntToHex($color);

    $extraColors[] = [
        'hex' => $hex
    ];
}
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Smart Color Palette Extractor</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:linear-gradient(135deg,#0f172a,#1e293b);
    color:white;
    min-height:100vh;
    padding:40px;
}

.container{
    max-width:1200px;
    margin:auto;
}

.title{
    text-align:center;
    margin-bottom:40px;
}

.title h1{
    font-size:52px;
    margin-bottom:12px;
}

.title p{
    color:#cbd5e1;
    font-size:18px;
}

.main-card{
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:25px;
    padding:35px;
    box-shadow:0 10px 30px rgba(0,0,0,0.3);
}

.upload-section{
    text-align:center;
}

.custom-file-upload{
    display:inline-block;
    padding:14px 28px;
    background:#2563eb;
    border-radius:14px;
    cursor:pointer;
    transition:0.3s;
    font-weight:600;
}

.custom-file-upload:hover{
    background:#1d4ed8;
    transform:translateY(-2px);
}

input[type="file"]{
    display:none;
}

.upload-btn{
    margin-top:20px;
    padding:14px 32px;
    border:none;
    border-radius:14px;
    background:#10b981;
    color:white;
    font-size:16px;
    cursor:pointer;
    transition:0.3s;
    font-weight:600;
}

.upload-btn:hover{
    background:#059669;
    transform:translateY(-2px);
}

#file-name{
    margin-top:18px;
    color:#cbd5e1;
    font-size:15px;
}

#preview-container{

    display:none;

    margin-top:25px;

    animation:fadeIn 0.5s ease;
}

#preview-image{

    width:320px;

    border-radius:20px;

    box-shadow:0 10px 25px rgba(0,0,0,0.4);
}

@keyframes fadeIn{

    from{
        opacity:0;
        transform:translateY(10px);
    }

    to{
        opacity:1;
        transform:translateY(0px);
    }
}

.section-title{
    text-align:center;
    font-size:32px;
    margin:45px 0 25px;
}

.colors-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:22px;
}

.color-card{
    background:white;
    border-radius:20px;
    overflow:hidden;
    color:black;
    transition:0.3s;
}

.color-card:hover{
    transform:translateY(-8px);
}

.color-box{
    height:170px;
}

.color-info{
    padding:20px;
    text-align:center;
}

.hex-code{
    font-size:20px;
    font-weight:600;
    margin-bottom:15px;
}

.copy-btn{
    border:none;
    background:#0f172a;
    color:white;
    padding:10px 18px;
    border-radius:10px;
    cursor:pointer;
    transition:0.3s;
}

.copy-btn:hover{
    background:#334155;
}

.footer{
    text-align:center;
    margin-top:40px;
    color:#94a3b8;
}

.spinner{

    width:70px;
    height:70px;

    border:8px solid rgba(255,255,255,0.2);
    border-top:8px solid #38bdf8;

    border-radius:50%;

    margin:auto;

    animation:spin 1s linear infinite;
}

@keyframes spin{

    0%{
        transform:rotate(0deg);
    }

    100%{
        transform:rotate(360deg);
    }
}

#loader{
    display:none;
    text-align:center;
    margin-top:30px;
}

@media(max-width:768px){

    body{
        padding:20px;
    }

    .title h1{
        font-size:36px;
    }

    #preview-image{
        width:100%;
    }
}

</style>

</head>

<body>

<div class="container">

    <div class="title">

        <h1>🎨 Smart Color Palette Extractor</h1>

        <p>
            Upload any image and instantly generate beautiful color palettes.
        </p>

    </div>

    <div class="main-card">

        <div class="upload-section">

            <form 
                method="POST" 
                enctype="multipart/form-data"
                id="uploadForm"
            >

                <label for="file-upload" class="custom-file-upload">
                    Choose Image
                </label>

                <input 
                    id="file-upload"
                    type="file"
                    name="image"
                    accept="image/*"
                    required
                >

                <p id="file-name"></p>

                <!-- IMAGE PREVIEW -->
                <div id="preview-container">

                    <img id="preview-image">

                </div>

                <br>

                <button type="submit" class="upload-btn">
                    Extract Colors
                </button>

            </form>

<!-- LOADER -->
<div id="loader">

    <div class="spinner"></div>

    <p style="margin-top:18px;color:#cbd5e1;">
        Extracting beautiful colors...
    </p>

</div>
<!-- ORIGINAL IMAGE AFTER EXTRACTION -->
<?php if($image != ""): ?>

<div style="margin-top:40px; text-align:center;">

    <h2 class="section-title">
        Original Image
    </h2>

    <img 
        src="<?php echo $image; ?>"
        style="
            width:320px;
            border-radius:20px;
            box-shadow:0 10px 25px rgba(0,0,0,0.4);
        "
    >

</div>

<?php endif; ?>

        </div>

        <?php if($image != ""): ?>
        <!-- TOP 10 COLORS -->
<h2 class="section-title">
    Top 10 Dominant Colors
</h2>

<div class="colors-grid">

    <?php foreach($dominantColors as $c): ?>


        <div class="color-card">

            <div 
                class="color-box"
                style="background: <?php echo $c['hex']; ?>;"
            ></div>

            <div class="color-info">

                <div class="hex-code">
                    <?php echo $c['hex']; ?>
                </div>

                <button 
                    class="copy-btn"
                    onclick="copyColor('<?php echo $c['hex']; ?>')"
                >
                    Copy
                </button>

            </div>

        </div>

    <?php endforeach; ?>

</div>

<!-- EXTRA COLORS -->
<?php if(count($extraColors) > 0): ?>

<h2 class="section-title">
    More Extracted Colors
</h2>

<div class="colors-grid">

    <?php foreach($extraColors as $index => $c): ?>


        <div 
            class="color-card extra-color"
            style="display:none;"
        >

            <div 
                class="color-box"
                style="background: <?php echo $c['hex']; ?>;"
            ></div>

            <div class="color-info">

                <div class="hex-code">
                    <?php echo $c['hex']; ?>
                </div>

                <button 
                    class="copy-btn"
                    onclick="copyColor('<?php echo $c['hex']; ?>')"
                >
                    Copy
                </button>

            </div>

        </div>


    <?php endforeach; ?>

</div>

<div style="text-align:center; margin-top:35px;">

    <button 
        id="loadMoreBtn"
        class="upload-btn"
        onclick="loadMoreColors()"
    >
        ✨ Load More Beautiful Colors
    </button>

</div>

<?php endif; ?>
        

<?php endif; ?>

    </div>

    <div class="footer">
        Built with PHP + League Color Extractor
    </div>

</div>

<script>
function loadMoreColors(){

    const hiddenColors =
        document.querySelectorAll('.extra-color[style*="display:none"]');

    let count = 0;

    hiddenColors.forEach(color => {

        if(count < 10){

            color.style.display = "block";

            count++;
        }
    });

    // Hide button if all colors visible
    if(
        document.querySelectorAll('.extra-color[style*="display:none"]').length === 0
    ){
        document.getElementById("loadMoreBtn").style.display = "none";
    }
}

function copyColor(color){

    navigator.clipboard.writeText(color);

    alert(color + " copied!");
}

// Image Preview
const fileInput = document.getElementById("file-upload");

fileInput.addEventListener("change", function(event){

    const file = event.target.files[0];

    if(file){

        document.getElementById("file-name").innerText =
            "Selected: " + file.name;

        const reader = new FileReader();

        reader.onload = function(e){

            const previewImage =
                document.getElementById("preview-image");

            previewImage.src = e.target.result;

            document.getElementById("preview-container")
                .style.display = "block";
        }

        reader.readAsDataURL(file);
    }
});

// Show Loader
document
.getElementById("uploadForm")
.addEventListener("submit", function(){

    document.getElementById("loader").style.display = "block";

    document.querySelector(".upload-btn").innerText =
        "Extracting...";
});

</script>

</body>
</html>