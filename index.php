<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <form method="post">
        <?php
        include 'koneksi.php';
        //-- menampilkan daftar gejala
        $sql = "SELECT * FROM ds_evidences";
        $result = $db->query($sql);
        while ($row = $result->fetch_object()) {
            echo "<input type='checkbox' name='evidence[]' value='{$row->id}'> {$row->code} {$row->name}<br>";
        }
        ?>
        <input type="submit" value="proses">
    </form>



    <?php
    if (isset($_POST['evidence'])) {
        if (count($_POST['evidence']) < 2) {
            echo "Pilih minimal 2 gejala";
        } else {
            $sql = "SELECT GROUP_CONCAT(b.code), a.cf
			FROM ds_rules a
			JOIN ds_problems b ON a.id_problem=b.id
			WHERE a.id_evidence IN(" . implode(',', $_POST['evidence']) . ") 
			GROUP BY a.id_evidence";
            $result = $db->query($sql);
            $evidence = array();
            while ($row = $result->fetch_row()) {
                $evidence[] = $row;
            }
            
            //--- menentukan environement
            $sql = "SELECT GROUP_CONCAT(code) FROM ds_problems";
            $result = $db->query($sql);
            $row = $result->fetch_row();
            $fod = $row[0];

            //--- menentukan nilai densitas
            $densitas_baru = array();
            while (!empty($evidence)) {
                $densitas1[0] = array_shift($evidence);
                $densitas1[1] = array($fod, 1 - $densitas1[0][1]);
                $densitas2 = array();
                if (empty($densitas_baru)) {
                    $densitas2[0] = array_shift($evidence);
                } else {
                    foreach ($densitas_baru as $k => $r) {
                        if ($k != "&theta;") {
                            $densitas2[] = array($k, $r);
                        }
                    }
                }
                $theta = 1;
                foreach ($densitas2 as $d) $theta -= $d[1];
                $densitas2[] = array($fod, $theta);
                $m = count($densitas2);
                $densitas_baru = array();
                for ($y = 0; $y < $m; $y++) {
                    for ($x = 0; $x < 2; $x++) {
                        if (!($y == $m - 1 && $x == 1)) {
                            $v = explode(',', $densitas1[$x][0]);
                            $w = explode(',', $densitas2[$y][0]);
                            sort($v);
                            sort($w);
                            $vw = array_intersect($v, $w);
                            if (empty($vw)) {
                                $k = "&theta;";
                            } else {
                                $k = implode(',', $vw);
                            }
                            if (!isset($densitas_baru[$k])) {
                                $densitas_baru[$k] = $densitas1[$x][1] * $densitas2[$y][1];
                            } else {
                                $densitas_baru[$k] += $densitas1[$x][1] * $densitas2[$y][1];
                            }
                        }
                    }
                }
                foreach ($densitas_baru as $k => $d) {
                    if ($k != "&theta;") {
                        $densitas_baru[$k] = $d / (1 - (isset($densitas_baru["&theta;"]) ? $densitas_baru["&theta;"] : 0));
                    }
                }
                print_r($densitas_baru);
            }
            //--- perangkingan
            unset($densitas_baru["&theta;"]);
            arsort($densitas_baru);
            print_r($densitas_baru);

            //--- menampilkan hasil akhir
            $codes = array_keys($densitas_baru);
            $sql = "SELECT GROUP_CONCAT(name) 
	FROM ds_problems 
	WHERE code IN('{$codes[0]}')";
            $result = $db->query($sql);
            $row = $result->fetch_row();
            echo "Terdeteksi penyakit <b>{$row[0]}</b> dengan derajat kepercayaan " . round($densitas_baru[$codes[0]] * 100, 2) . "%";
        }
    }
    ?>

</body>

</html>