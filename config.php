<?php
// config chạy local XAMPP - JAVA.WAP.SH
return [
    'siteName' => 'JAVA.WAP.SH',
    'domain' => 'https://java.wap.sh',
    'vercelUrl' => 'https://j2mewap.vercel.app',
    'dataFile' => __DIR__ . '/data/games.json',
    'slugMode' => 'vietnamese', // slugifyVi
    'fieldsRemoved' => ['dev','ver','rating','downloads','format'],
    'adminPass' => '', // để trống = không cần pass, đặt '123456' nếu cần
];
