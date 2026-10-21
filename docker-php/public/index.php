<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi primer servidor PHP</title>
</head>
<body>

    <h1>¡Mi servidor PHP funciona!</h1>

    <p>Esta página está siendo ejecutada por PHP dentro de Docker.</p>

    <p>
        <?php
            echo "PHP está funcionando correctamente.<br>";
            $n = 6;
            echo "Esta es es la tabla de multiplicar del número $n: <br>";
            for ($i = 1; $i <= 10; $i++) {
                 echo "$n x $i = ". $n * $i ."<br>";
            }
        ?>
    </p>

</body>
</html>