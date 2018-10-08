<?php

// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:lamparinas.database.windows.net,1433; Database = DBLamparinas", "valmaregia", "cValToChar16");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

$sql = ("SELECT * FROM Usuario");
$stmt = $conn->query("$sql");

While ($row = $stmt->fetch()){
	echo "$row[0] - $row[1]<br>";
}
$conn = NULL;

?>
