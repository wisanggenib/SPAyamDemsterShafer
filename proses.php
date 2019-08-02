<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
	<h1>Sistem Pakar Penyakit Ayam</h1>
    <form method="post" action="hasil.php">
        <?php
        include 'koneksi.php';
        //-- menampilkan daftar gejala
        $sql = "SELECT * FROM ds_evidences";
        $result = $db->query($sql);
        while ($row = $result->fetch_object()) {
            echo "<input type='checkbox' name='evidence[]' value='{$row->id}'> {$row->code} {$row->name}<br>";
        }
        ?>
        <input type="submit" value="Diagnosa" style="margin-left:8%; margin-top:10px;">
    </form>



</body>

</html>