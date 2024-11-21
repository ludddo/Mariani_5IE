<?php
require "classes.php";
require "../db.php";

$query = new Query($servername, $dbname, $username, $password);
$risultati = $query->get_voto()
                   ->get_data()
                   ->esegui();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Risultati Interrogazioni</title>
    <style>
        table { 
            border-collapse: collapse; 
            width: 100%; 
        }
        th, td { 
            padding: 8px; 
            text-align: left; 
            border: 1px solid #ddd; 
        }
        th { 
            background-color: #f2f2f2; 
        }
    </style>
</head>
<body>
    <h2>Risultati della ricerca</h2>
    <?php if (!empty($risultati)): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Voto</th>
            </tr>
            <?php foreach ($risultati as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['ID']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['data'])); ?></td>
                    <td><?php echo htmlspecialchars($row['voto']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nessun risultato trovato</p>
    <?php endif; ?>
    
    <p><a href="index.html">Torna alla ricerca</a></p>
</body>
</html>