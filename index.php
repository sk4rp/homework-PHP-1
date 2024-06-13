<?php

use Random\RandomException;

$example_persons_array = [
    [
        'fullName' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullName' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullName' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullName' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullName' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullName' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullName' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullName' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullName' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullName' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullName' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];


// Разбиение и объединение ФИО
function getFullNameFromParts($surname, $name, $patronymic): string
{
    return "$surname $name $patronymic";
}

function getPartsFromFullName($fullName): array
{
    $parts = explode(' ', $fullName);

    $surname = $parts[0] ?? null;
    $name = $parts[1] ?? null;
    $patronymic = $parts[2] ?? null;

    return [
        'surname' => $surname,
        'name' => $name,
        'patronymic' => $patronymic,
    ];
}

$fullName = getFullNameFromParts('Иванов', 'Иван', 'Иванович');
echo $fullName;
echo "\n";

$parts = getPartsFromFullName('Иванов Иван Иванович');
echo "\n";

print_r($parts);

echo "\n";

// Сокращение ФИО
function getShortName($fullName): string
{
    $parts = getPartsFromFullName($fullName);

    $shortName = $parts['name'];
    $shortSurname = mb_substr($parts['surname'], 0, 1) . '.';

    return "$shortName $shortSurname";
}


$fullName = 'Иванов Иван Иванович';
$shortName = getShortName($fullName);
echo $shortName;

echo "\n";

// Функция определения пола по ФИО
function determineGender($fullName): int
{
    $parts = getPartsFromFullName($fullName);

    $genderSum = 0;

    $surname = mb_strtolower($parts['surname']);
    if (mb_substr($surname, -2) === 'ва') {
        --$genderSum; // Женщина
    } elseif (mb_substr($surname, -1) === 'в') {
        ++$genderSum; // Мужчина
    }

    $name = mb_strtolower($parts['name']);
    if (mb_substr($name, -1) === 'а') {
        --$genderSum; // Женщина
    } elseif (in_array(mb_substr($name, -1), ['й', 'н'])) {
        ++$genderSum; // Мужчина
    }

    $patronymic = mb_strtolower($parts['patronymic']);
    if (mb_substr($patronymic, -3) === 'вна') {
        --$genderSum; // Женщина
    } elseif (mb_substr($patronymic, -3) === 'ич') {
        ++$genderSum; // Мужчина
    }

    if ($genderSum > 0) {
        return 1; // Мужчина
    }

    if ($genderSum < 0) {
        return -1; // Женщина
    }

    return 0; // Undefined / Неизвестно
}

echo "\n";

foreach ($example_persons_array as $person) {
    $fullName = $person['fullName'];
    $shortName = getShortName($fullName);
    $gender = determineGender($fullName);

    echo match ($gender) {
        1 => "$shortName - это мужчина\n",
        -1 => "$shortName - это женщина\n",
        0 => "$shortName - неизвестно\n",
        default => "Невозможно определить пол человека: $shortName.\n",
    };
}

// Определение возрастно-полового состава
function getGenderDescription($example_persons_array): string
{
    $totalPersons = count($example_persons_array);
    $maleCount = 0;
    $femaleCount = 0;
    $unknownCount = 0;

    foreach ($example_persons_array as $person) {
        $gender = determineGender($person['fullName']);
        switch ($gender) {
            case 1:
                $maleCount++;
                break;
            case -1:
                $femaleCount++;
                break;
            default:
                $unknownCount++;
                break;
        }
    }

    $totalPercentage = $totalPersons > 0 ? 100 / $totalPersons : 0;
    $malePercentage = round($maleCount * $totalPercentage, 1);
    $femalePercentage = round($femaleCount * $totalPercentage, 1);
    $unknownPercentage = round($unknownCount * $totalPercentage, 1);

    echo "\n";

    $result = "Гендерный состав аудитории:\n";
    $result .= "---------------------------\n";
    $result .= "Мужчины - $malePercentage%\n";
    $result .= "Женщины - $femalePercentage%\n";
    $result .= "Не удалось определить - $unknownPercentage%\n";

    return $result;
}

echo getGenderDescription($example_persons_array);
echo "\n";

// Идеальный подбор пары
/**
 * @throws RandomException
 */
function getPerfectPartner($surname, $name, $patronymic, $personsArray): string
{
    $fio = getFullNameFromParts($surname, $name, $patronymic);
    $gender = determineGender($fio);

    $potentialPartners = array_filter($personsArray, static function ($person) use ($gender) {
        $partnerGender = determineGender($person['fullName']);
        return ($gender === 1 && $partnerGender === -1) || ($gender === -1 && $partnerGender === 1);
    });

    if (empty($potentialPartners)) {
        throw new RandomException("Не найдено подходящего партнера");
    }

    $randomIndex = array_rand($potentialPartners);
    $partner = $potentialPartners[$randomIndex];

    $partnerFio = $partner['fullName'];
    $compatibilityPercentage = random_int(5000, 10000) / 100;
    $message = getShortName($fio) . ' + ' . getShortName($partnerFio) . ' = ' . "\n";
    $message .= '♡ Идеально на ' . number_format($compatibilityPercentage, 2) . '% ♡';

    return $message;
}

try {
    echo getPerfectPartner('Иванов', 'Иван', 'Иванович', $example_persons_array);
} catch (RandomException $e) {
    echo $e->getMessage();
}

