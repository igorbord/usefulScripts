<?
function autoloadClasses(string $class)
{
    require_once './Classes/' . str_replace('\\', '/', $class) . '.class.php';
}
