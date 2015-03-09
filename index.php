<?php

if (isset($_GET['from'])) {
    $from = $_GET['from'];
} else {
    $from = '2015-01-01';
}
if (isset($_GET['to'])) {
    $to = $_GET['to'];
} else {
    $to = date('Y-m-d');
}

$page = file_get_contents("http://libgen.org/json.php?fields=Author,Title,Year,Pages,Identifier,Language,Publisher,Extension,MD5,Filesize,Edition,Coverurl&mode=last&timefirst=$from&timelast=$to");
#file_put_contents('cache.json', $page);
#$page = file_get_contents('cache.json');

$books = json_decode($page, true);

for ($key = 0; $key < count($books); $key++) {
    $book = $books[$key];
    $score = 0;
    # PUBLISHER
    $publisher = strtolower($book['Publisher']);
    foreach (['springer', 'routledge'] as $w) {
        if (strpos($publisher, $w) !== FALSE) $score += 40;
    }
    foreach (['wiley', 'blackwell', 'cambridge', 'oxford', 'cengage', 'prentice', 'apress', 'reilly', 'palgrave', 'university', 'bloomsbury', 'packt'] as $w) {
        if (strpos($publisher, $w) !== FALSE) $score += 30;
    }
    foreach (['lippincott','brill'] as $w) {
        if (strpos($publisher, $w) !== FALSE) $score += 10;
    }
    # TITLE
    $title = strtolower($book['Title']);
    foreach (['neutzsky'] as $w) {
        if (strpos($title, $w) !== FALSE) $score += 100;
    }
    foreach (['heidegger', 'husserl', 'hegel', 'phenomenol', 'psychiatr', 'hallucination', 'psychos'] as $w) {
        if (strpos($title, $w) !== FALSE) $score += 75;
    }
    foreach (['philosophy', 'cognitive', 'introduction', 'understanding', 'handbook', 'principles', 'comprehensive', 'textbook', 'consciousness', 'mind'] as $w) {
        if (strpos($title, $w) !== FALSE) $score += 50;
    }
    foreach (['psych', 'biolog', 'astronom', 'economy', 'math', 'geography', 'geolog', 'medicine', 'surgery', 'cardiol', 'dermatol', 'pharmacol', 'gastroen', 'rheuma', 'orthoped', 'radiol'] as $w) {
        if (strpos($title, $w) !== FALSE) $score += 10;
    }
    # LANGUAGE
    if (strpos(strtolower($book['Language']), 'english') === FALSE) $score -= 50;
    # EXTENSION
    $extension = strtolower($book['Extension']);
    if ($extension == 'pdf') {
        $score += 50;
    } elseif ($extension == 'chm') {
        $score += 10;
    } elseif ($extension == 'epub') {
        $score += 20;
    }
    # YEAR
    if (is_numeric($book['Year'])) {
        $score += intval($book['Year']) - 1980;
    }
    # SCORE OG q
    $books[$key]['score'] = $score;
    if ($book['Identifier']) {
        $books[$key]['q'] = preg_replace('/,.*/', '', $book['Identifier']);
    } else {
        $books[$key]['q'] = preg_replace("/[^a-z0-9\.]/", "", strtolower($book['Title']));
    }
}

usort ($books, function($a, $b) { return $a['score'] < $b['score']; });

echo "<!doctype html>\n";
echo "<head>\n";
echo "<meta charset=\"utf-8\" />\n";
echo "<title>Libgen News</title>";
echo "<link rel=\"icon\" href=\"favicon.png\"/>\n";
echo "<link rel=\"apple-touch-icon\" href=\"favicon.png\"/>\n";
echo "<link rel=\"stylesheet\" href=\"styles.css\" />\n";
echo "<script src=\"/lib/jQuery/jquery-2.1.3.min.js\"></script>\n";
echo "<script src=\"script.js\"></script>\n";
echo "</head>\n\n";

echo "<table>";
foreach ($books as $book) {
    if ($book['score'] > 50) {
        echo "<tr>";
        echo '<td class="cover"><a href="http://www.amazon.com/s/ref=nb_sb_noss?url=search-alias%3Dstripbooks&field-keywords=' . $book['q'] . '"><img src="http://libgen.org/covers/' . $book['Coverurl'] . '"></a>';
        echo '<td>';
        echo '<div class="score">[' . htmlspecialchars($book['score']) . "]</div>";
        echo '<div class="title">' . htmlspecialchars(strlen($book['Title']) > 130 ? substr($book['Title'],0,130)."..." : $book['Title']) . "</div>";
        echo '<div class="author">' . htmlspecialchars(strlen($book['Author']) > 130 ? substr($book['Author'],0,130)."..." : $book['Author']) . "</div>";
        echo '<div class="publishereditionyear">';
        echo '<span class="publisher">' . htmlspecialchars($book['Publisher']) . "</span>";
        if ($book['Edition']) echo '<span class="edition">' . htmlspecialchars($book['Edition']) . "</span>";
        echo '<span class="year">' . htmlspecialchars($book['Year']) . "</span>";
        echo '<span class="filesize">' . htmlspecialchars($book['Filesize']) . "</span>";
        echo '</div>';
        echo '<div><input type="text" value="' . htmlspecialchars($book['q'])  . '.' . $book['Extension'] . '"></div>';
        if ($book['Identifier']) echo '<div class="knap lookup" extension="' . $book['Extension'] . '" isbn="' . $book['q'] . '">lookup</div>';
        echo '<div class="knap fuzzfind">fuzzfind</div>';
        echo '<div class="knap down" md5="' . $book['MD5'] . '">down</div>';
    }
}
echo "</table>";