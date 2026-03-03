<?php
$conn = new mysqli("localhost","root","","photographe_db");
if($conn->connect_error) die("Erreur de connexion: ".$conn->connect_error);

$result = $conn->query("SELECT * FROM tarifs ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tarifs - Sixteen Prod</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        body{font-family:Arial; margin:0; padding:0; background:url('images/fond.jpg') no-repeat center center fixed; background-size:cover; color:#fff;}
        h1{text-align:center; margin-top:30px; text-shadow:2px 2px 4px rgba(0,0,0,0.7);}
        .tarif-table-container{width:90%; max-width:900px; margin:40px auto; overflow-x:auto; background:rgba(0,0,0,0.5); border-radius:10px; padding:20px;}
        table{width:100%; border-collapse:collapse; color:#fff;}
        th, td{padding:12px 15px; border-bottom:1px solid rgba(255,255,255,0.3);}
        th{background:rgba(106,13,173,0.8);}
        tr:hover{background:rgba(255,255,255,0.1);}
        td{font-size:15px;}
        @media screen and (max-width:768px){th, td{padding:10px; font-size:14px;}}
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<h1>Nos Tarifs</h1>

<div class="tarif-table-container">
    <table>
        <thead>
            <tr>
                <th>Pack</th>
                <th>Détails</th>
                <th>Prix</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($result && $result->num_rows>0){
                while($row = $result->fetch_assoc()){
                    echo "<tr>";
                    echo "<td>".htmlspecialchars($row['pack'])."</td>";
                    echo "<td>".htmlspecialchars($row['description'])."</td>";
                    echo "<td>".htmlspecialchars($row['prix'])."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3' style='text-align:center;'>Aucun tarif disponible.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>