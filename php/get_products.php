<?php

$servername = "remotemysql.com";
$username = "2brhMowid7";
$password = "sXrXC67iRz";
$dbname = "2brhMowid7";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$content = trim(file_get_contents("php://input"));
$decoded = json_decode($content, true);

$sql = "SELECT * FROM produkty";

if(is_array($decoded)) {
    if(array_key_exists('category', $decoded) && count($decoded['category']) > 0) {
        $sql .= ' WHERE( ';
        for($i = 0; $i < count($decoded['category']); $i++){
            $sql .= ' indeksKategorii='.$decoded['category'][$i];
            if($i != count($decoded['category']) - 1) $sql .= ' OR ';
        }
        $sql .= ') ';
    }
    if(array_key_exists('recommended', $decoded) && $decoded['recommended'] == true){
        if(!array_key_exists('category', $decoded)) $sql .= ' WHERE ';
        else if(array_key_exists('category', $decoded)) $sql .= ' AND ';
        $sql .= ' czyPolecane=1 ';

    }
    if(array_key_exists('sort', $decoded) && strlen($decoded['sort']) > 0) $sql .= " ORDER BY ".explode(",",$decoded['sort'])[0]." ".explode(",",$decoded['sort'])[1];
    if(array_key_exists('limit', $decoded)) $sql .= " LIMIT ".$decoded['limit'];
}

$status = array();
$data;

// $sql = 'SELECT * FROM produkty WHERE indeksKategorii=1 OR indeksKategorii=5 AND czyPolecane=1';
// echo $sql;

if($result = $conn->query($sql)){
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = array(
                'id' => $row["id"],
                'nazwaProduktu' => $row["nazwaProduktu"],
                'cenaProduktu' => $row["cenaProduktu"],
                'adresZdjecia' => $row["adresZdjecia"],
                'opisProduktu' => $row["opisProduktu"]
                //'czyPolecane' => $row["czyPolecane"]    
            );
        }
    } else {
        $status['success'] = false;
        $status['msg'] = 'Brak danych!';
        echo json_encode($status);
        exit();
    }
    echo json_encode($data);
} else {
    $status['success'] = false;
    echo json_encode($status);
}

$conn->close();

?>
