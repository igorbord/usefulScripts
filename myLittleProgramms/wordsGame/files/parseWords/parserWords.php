<?
require_once 'phpQuery-onefile.php';
set_time_limit(0); // убирает ограничение сервера в 60 секунд
// file_put_contents('all-words.txt', ''); // Очистка файла перед каждым запуском скрипта

$mainPage = getPage('https://wordsonline.ru/');
$htmlMainPage = phpQuery::newDocument($mainPage);
$letters = $htmlMainPage->find('.alphabet a');

foreach ($letters as $key => $letter) { // Перебор букв
    unset($page);

    do { //Перебор страниц пагинации по каждой букве
        $url = 'https://wordsonline.ru' . pq($letter)->attr('href') . (isset($page) ? '?page=' . $page : '');
        echo $url . PHP_EOL;
        $letterPage = getPage($url);
        $htmlLetterPage = phpQuery::newDocument($letterPage);
        $words = $htmlLetterPage->find('.list-words a');
        $pagesCount = $htmlLetterPage->find('.pagination:last li:last-child')->text();

        foreach ($words as $word) {
            $text = pq($word)->text();
            if (mb_strlen($text) === 5) {
                file_put_contents('all-words.txt', mb_strtolower($text) . PHP_EOL, FILE_APPEND);
            }
        }

        $page = isset($page) ? $page + 1 : 2;
    } while ($page <= $pagesCount);
    sleep(1);
}

echo 'finish';


function getPage($url)
{
    $cn = curl_init();
    curl_setopt_array($cn, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $result = curl_exec($cn);
    curl_close($cn);
    return $result;
}
