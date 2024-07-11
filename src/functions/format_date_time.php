<?php
if ($_SERVER['REQUEST_URI'] == '/src/functions/format_date_time.php') { header("Location: /"); exit(); }

function formatDateTime($timestamp, $language = 'en') {
    if (empty($timestamp)) { return 400; }
    
    $dateFormat = ($language === 'tr') ? 'd F Y H:i' : 'F d Y H:i';
    
    $dateTime = new DateTime("@$timestamp");
    $dateTime -> setTimezone(new DateTimeZone('Europe/Istanbul'));

    if ($language === 'tr') {
        $formatter = new IntlDateFormatter('tr_TR', IntlDateFormatter::FULL, IntlDateFormatter::FULL);
        $formatter -> setPattern('d MMMM yyyy HH:mm');
    } else {
        $formatter = new IntlDateFormatter('en_US', IntlDateFormatter::FULL, IntlDateFormatter::FULL);
        $formatter -> setPattern('MMMM d yyyy HH:mm');
    }

    $formattedDate = $formatter -> format($dateTime);
    
    return $formattedDate;
}