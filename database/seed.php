<?php
// database/seed.php

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance();

    // Disable foreign key checks for truncation
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    $db->exec("TRUNCATE TABLE reserved_seats");
    $db->exec("TRUNCATE TABLE reservations");
    $db->exec("TRUNCATE TABLE showtimes");
    $db->exec("TRUNCATE TABLE seats");
    $db->exec("TRUNCATE TABLE halls");
    $db->exec("TRUNCATE TABLE movies");
    $db->exec("TRUNCATE TABLE users");
    $db->exec("TRUNCATE TABLE promo_codes");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "Tables truncated successfully.\n";

    // 0. Insert Admin User
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $db->prepare("INSERT INTO users (username, password, role) VALUES ('admin', :password, 'admin')")
       ->execute(['password' => $adminPassword]);
    echo "Admin user created (admin / admin123).\n";

    // 0.5. Insert Promo Code
    $db->exec("INSERT INTO promo_codes (code, discount_percent, valid_until) VALUES ('PROMOCODE', 50, '2030-12-31')");
    echo "Promo code inserted.\n";

    // 1. Insert Halls
    $halls = [
        ['name' => 'Зала 1 • IMAX', 'capacity' => 80],
        ['name' => 'Зала 2 • 4DX', 'capacity' => 60],
        ['name' => 'Зала 3 • LUXE', 'capacity' => 100],
    ];

    $hall_ids = [];
    $stmt = $db->prepare("INSERT INTO halls (name, capacity) VALUES (:name, :capacity)");
    foreach ($halls as $hall) {
        $stmt->execute($hall);
        $hall_ids[] = $db->lastInsertId();
    }
    echo "Halls inserted.\n";

    // 2. Insert Seats for each Hall
    $seat_stmt = $db->prepare("INSERT INTO seats (hall_id, row_num, seat_num, type) VALUES (:hall_id, :row_num, :seat_num, :type)");
    foreach ($hall_ids as $index => $hall_id) {
        $rows = 8;
        $cols = ($index == 1) ? 8 : 12;
        for ($r = 1; $r <= $rows; $r++) {
            for ($s = 1; $s <= $cols; $s++) {
                $type = ($r == $rows) ? 'vip' : 'standard';
                $seat_stmt->execute([
                    'hall_id' => $hall_id,
                    'row_num' => $r,
                    'seat_num' => $s,
                    'type' => $type
                ]);
            }
        }
    }
    echo "Seats inserted.\n";

    // 3. Insert Movies
    $movies = [
        [
            'title' => 'Гладиатор II',
            'description' => 'Епичното продължение на легендарната сага.',
            'duration' => 150,
            'genre' => 'Екшън, Драма',
            'rating' => '16+',
            'release_date' => '2024-11-15',
            'director' => 'Ридли Скот',
            'cast' => json_encode([
                ['name' => 'Пол Мескал', 'character' => 'Луций', 'image' => 'https://image.tmdb.org/t/p/w500/vrzZ41TGNAFgfmZjC2sOJySzBLd.jpg'],
                ['name' => 'Педро Паскал', 'character' => 'Марк Акаций', 'image' => 'https://image.tmdb.org/t/p/w500/9VYK7oxcqhjd5LAH6ZFJ3XzOlID.jpg'],
                ['name' => 'Дензъл Уошингтън', 'character' => 'Макрин', 'image' => 'https://image.tmdb.org/t/p/w500/jj2Gcobpopokal0YstuCQW0ldJ4.jpg']
            ]),
            'trailer_url' => 'https://www.youtube.com/embed/4rgYUipGJNo',
            'poster_path' => 'public/assets/images/img_15.jpg',
            'status' => 'now playing'
        ],
        [
            'title' => 'Дюн: Част Втора',
            'description' => 'Пол Атреидски се обединява с Чани и свободните хора.',
            'duration' => 166,
            'genre' => 'Фантастика, Екшън',
            'rating' => '12+',
            'release_date' => '2024-03-01',
            'director' => 'Дени Вилньов',
            'cast' => json_encode([
                ['name' => 'Тимъти Шаламе', 'character' => 'Пол Атреидски', 'image' => 'https://image.tmdb.org/t/p/w500/dFxpwRpmzpVfP1zjluH68DeQhyj.jpg'],
                ['name' => 'Зендая', 'character' => 'Чани', 'image' => 'https://image.tmdb.org/t/p/w500/uD5a0CsVbR0phlUvHXLlKntAIXS.jpg'],
                ['name' => 'Остин Бътлър', 'character' => 'Фейд-Рота', 'image' => 'https://image.tmdb.org/t/p/w500/atdAs4pFGjUQ4m2W8kJYly7N6cC.jpg']
            ]),
            'trailer_url' => 'https://www.youtube.com/embed/Way9Dexny3w',
            'poster_path' => 'public/assets/images/img_16.jpg',
            'status' => 'now playing'
        ],
        [
            'title' => 'Дивият Робот',
            'description' => 'Един робот претърпява корабокрушение на необитаем остров.',
            'duration' => 102,
            'genre' => 'Анимация',
            'rating' => 'B',
            'release_date' => '2024-09-27',
            'director' => 'Крис Сандърс',
            'cast' => json_encode([
                ['name' => 'Лупита Нионго', 'character' => 'Роз (глас)', 'image' => 'https://image.tmdb.org/t/p/w500/y40Wu1T742kynOqtwXASc5Qgm49.jpg'],
                ['name' => 'Педро Паскал', 'character' => 'Финк (глас)', 'image' => 'https://image.tmdb.org/t/p/w500/9VYK7oxcqhjd5LAH6ZFJ3XzOlID.jpg']
            ]),
            'trailer_url' => 'https://www.youtube.com/embed/67vbA5ZJdK8',
            'poster_path' => 'public/assets/images/img_17.jpg',
            'status' => 'now playing'
        ],
        [
            'title' => 'Усмивка 2',
            'description' => 'Глобалната поп сензация Скай Райли преживява ужасяващи събития.',
            'duration' => 127,
            'genre' => 'Ужаси',
            'rating' => '18+',
            'release_date' => '2024-10-18',
            'director' => 'Паркър Фин',
            'cast' => json_encode([
                ['name' => 'Наоми Скот', 'character' => 'Скай Райли', 'image' => 'https://image.tmdb.org/t/p/w500/knSGMaEaH6CZaYw7GQpvxyJcsz7.jpg']
            ]),
            'trailer_url' => 'https://www.youtube.com/embed/0hSJN8m_ZpM',
            'poster_path' => 'public/assets/images/img_18.jpg',
            'status' => 'now playing'
        ],
        [
            'title' => 'Жокера: Лудост за двама',
            'description' => 'Артър Флек е настанен в Аркам.',
            'duration' => 138,
            'genre' => 'Драма, Криминален',
            'rating' => '16+',
            'release_date' => '2024-10-04',
            'director' => 'Тод Филипс',
            'cast' => json_encode([
                ['name' => 'Хоакин Финикс', 'character' => 'Артър Флек', 'image' => 'https://image.tmdb.org/t/p/w500/u38k3hQBDwNX0VA22aQceDp9Iyv.jpg'],
                ['name' => 'Лейди Гага', 'character' => 'Лий Куинзел', 'image' => 'https://image.tmdb.org/t/p/w500/9Y4Pz7AEXhB9qNar2tMsx5EVXML.jpg']
            ]),
            'trailer_url' => 'https://www.youtube.com/embed/_OKAwz2MsJs',
            'poster_path' => 'public/assets/images/img_19.jpg',
            'status' => 'now playing'
        ],
        [
            'title' => 'Дедпул и Върколака',
            'description' => 'Уейд Уилсън е принуден да се върне в действие.',
            'duration' => 128,
            'genre' => 'Екшън, Комедия',
            'rating' => '16+',
            'release_date' => '2024-07-26',
            'director' => 'Шон Леви',
            'cast' => json_encode([
                ['name' => 'Райън Рейнолдс', 'character' => 'Уейд Уилсън', 'image' => 'https://image.tmdb.org/t/p/w500/trzgptffGvAlAT6MEu01fz47cLW.jpg'],
                ['name' => 'Хю Джакман', 'character' => 'Лоуган', 'image' => 'https://image.tmdb.org/t/p/w500/oX6CpXmnXCHLyqsa4NEed1DZAKx.jpg']
            ]),
            'trailer_url' => 'https://www.youtube.com/embed/73_1biulk6g',
            'poster_path' => 'public/assets/images/img_20.jpg',
            'status' => 'now playing'
        ],
        [
            'title' => 'Муфаса: Цар Лъв',
            'description' => 'Историята за възхода на един от най-великите крале.',
            'duration' => 120,
            'genre' => 'Анимация',
            'rating' => 'B',
            'release_date' => '2024-12-20',
            'director' => 'Бари Дженкинс',
            'cast' => json_encode([
                ['name' => 'Арън Пиер', 'character' => 'Муфаса (глас)', 'image' => 'https://image.tmdb.org/t/p/w500/z2cMMZyWzv5ztT6pFdAAjB3u7CQ.jpg']
            ]),
            'trailer_url' => '',
            'poster_path' => 'public/assets/images/img_21.jpg',
            'status' => 'now playing'
        ]
    ];

    $movie_ids = [];
    $movie_stmt = $db->prepare("INSERT INTO movies (title, description, duration, genre, rating, release_date, director, cast, trailer_url, poster_path, status, user_rating) 
                               VALUES (:title, :description, :duration, :genre, :rating, :release_date, :director, :cast, :trailer_url, :poster_path, :status, :user_rating)");
    foreach ($movies as $movie) {
        $movie['user_rating'] = rand(80, 99) / 10;
        $movie_stmt->execute($movie);
        $movie_ids[] = $db->lastInsertId();
    }
    echo "Movies inserted.\n";

    // 4. Insert Showtimes for next 14 days
    $showtime_stmt = $db->prepare("INSERT INTO showtimes (movie_id, hall_id, start_time, base_price) VALUES (:movie_id, :hall_id, :start_time, :base_price)");
    
    $startDate = new DateTime();
    $startDate->setTime(10, 0, 0);

    for ($i = 0; $i < 14; $i++) {
        $currentDate = clone $startDate;
        $currentDate->modify("+$i days");
        
        foreach ($movie_ids as $m_index => $movie_id) {
            $hall_id = $hall_ids[$m_index % count($hall_ids)];
            
            // Showtime 1
            $time1 = clone $currentDate;
            $time1->modify("+" . ($m_index * 2) . " hours");
            $showtime_stmt->execute([
                'movie_id' => $movie_id,
                'hall_id' => $hall_id,
                'start_time' => $time1->format('Y-m-d H:i:s'),
                'base_price' => 12.00 + ($m_index * 2)
            ]);
            seedReservations($db, $db->lastInsertId(), $hall_id);

            // Showtime 2
            $time2 = clone $time1;
            $time2->modify("+4 hours");
            $showtime_stmt->execute([
                'movie_id' => $movie_id,
                'hall_id' => $hall_id,
                'start_time' => $time2->format('Y-m-d H:i:s'),
                'base_price' => 14.00 + ($m_index * 2)
            ]);
            seedReservations($db, $db->lastInsertId(), $hall_id);
        }
    }
    echo "Showtimes and reservations inserted.\n";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}

function seedReservations($db, $showtime_id, $hall_id) {
    $stmt = $db->prepare("SELECT id FROM seats WHERE hall_id = :hall_id");
    $stmt->execute(['hall_id' => $hall_id]);
    $seats = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $totalSeats = count($seats);
    $numToReserve = rand(ceil($totalSeats * 0.3), ceil($totalSeats * 0.6));
    
    shuffle($seats);
    $reservedSeats = array_slice($seats, 0, $numToReserve);

    $customerNames = ['Ivan Ivanov', 'Maria Petrova', 'Georgi Georgiev', 'Elena Stoianova', 'Dimitur Dimitrov'];
    
    $i = 0;
    while ($i < count($reservedSeats)) {
        $groupSize = rand(1, 4);
        $group = array_slice($reservedSeats, $i, $groupSize);
        $i += $groupSize;

        if (empty($group)) break;

        $uid = bin2hex(random_bytes(4));
        $res_stmt = $db->prepare("INSERT INTO reservations (uid, showtime_id, customer_name, customer_email, total_price) 
                                 VALUES (:uid, :showtime_id, :customer_name, :customer_email, :total_price)");
        
        $name = $customerNames[array_rand($customerNames)];
        $email = strtolower(str_replace(' ', '.', $name)) . "@example.com";
        
        $res_stmt->execute([
            'uid' => $uid,
            'showtime_id' => $showtime_id,
            'customer_name' => $name,
            'customer_email' => $email,
            'total_price' => count($group) * 15.00
        ]);
        $res_id = $db->lastInsertId();

        $seat_res_stmt = $db->prepare("INSERT INTO reserved_seats (uid, reservation_id, seat_id, price) VALUES (:uid, :reservation_id, :seat_id, :price)");
        foreach ($group as $seat_id) {
            $seat_res_stmt->execute([
                'uid' => bin2hex(random_bytes(8)),
                'reservation_id' => $res_id,
                'seat_id' => $seat_id,
                'price' => 15.00
            ]);
        }
    }
}
