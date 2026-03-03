<?php
$conn = new mysqli("localhost", "root", "", "photographe_db");
if($conn->connect_error){
    die("Erreur de connexion: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM galerie ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Galerie</title>
<style>
body{ font-family:Arial; background:#f5f5f5; margin:0; padding:20px; }
.gallery{ display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:15px; }
.gallery img{ width:100%; height:250px; object-fit:cover; border-radius:10px; cursor:pointer; transition:0.3s; }
.gallery img:hover{ transform:scale(1.05); }
.lightbox{ display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); justify-content:center; align-items:center; z-index:1000; }
.lightbox img{ max-width:90%; max-height:90%; border-radius:10px; }
.lightbox span{ position:absolute; top:20px; right:40px; font-size:35px; color:white; cursor:pointer; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<h2 style="text-align:center;">Galerie</h2>

<div class="gallery">
<?php 
if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()) { 
        $file = 'uploads/' . $row['images'];
        if(file_exists($file)){ 
?>
            <img src="<?= $file ?>" onclick="openLightbox(this.src)">
<?php 
        }
    } 
} else {
    echo "<p style='text-align:center;'>Aucune photo pour le moment.</p>";
}
?>
</div>

<div class="lightbox" id="lightbox">
    <span onclick="closeLightbox()">&times;</span>
    <img id="lightbox-img">
</div>

<script>
function openLightbox(src){
    document.getElementById("lightbox").style.display="flex";
    document.getElementById("lightbox-img").src = src;
}
function closeLightbox(){
    document.getElementById("lightbox").style.display="none";
}
</script>

</body>
</html>