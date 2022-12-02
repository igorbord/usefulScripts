<?
require_once __DIR__ . '/functions/debug.php';
require_once __DIR__ . '/functions/functions.php';

spl_autoload_register('autoloadClasses');
(new ErrorProcessing)->register(); // Собственный отлавливать ошибок

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>training-oop</title>
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <? require_once('functions/code.php'); ?>
</body>

</html>
<?
