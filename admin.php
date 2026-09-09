<?php
$config = require __DIR__ . '/config.php';
$dataFile = $config['dataFile'];
$msg = '';

function slugifyVi($s){
    $s = mb_strtolower($s, 'UTF-8');
    $map = [
        'á'=>'a','à'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a','ă'=>'a','ắ'=>'a','ằ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a','â'=>'a','ấ'=>'a','ầ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
        'é'=>'e','è'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e','ê'=>'e','ế'=>'e','ề'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
        'í'=>'i','ì'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
        'ó'=>'o','ò'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o','ô'=>'o','ố'=>'o','ồ'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o','ơ'=>'o','ớ'=>'o','ờ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
        'ú'=>'u','ù'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u','ư'=>'u','ứ'=>'u','ừ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
        'ý'=>'y','ỳ'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
        'đ'=>'d','Á'=>'a','À'=>'a','Ả'=>'a','Ã'=>'a','Ạ'=>'a','Ă'=>'a','Ắ'=>'a','Ằ'=>'a','Ẳ'=>'a','Ẵ'=>'a','Ặ'=>'a','Â'=>'a','Ấ'=>'a','Ầ'=>'a','Ẩ'=>'a','Ẫ'=>'a','Ậ'=>'a',
        'É'=>'e','È'=>'e','Ẻ'=>'e','Ẽ'=>'e','Ẹ'=>'e','Ê'=>'e','Ế'=>'e','Ề'=>'e','Ể'=>'e','Ễ'=>'e','Ệ'=>'e',
        'Í'=>'i','Ì'=>'i','Ỉ'=>'i','Ĩ'=>'i','Ị'=>'i',
        'Ó'=>'o','Ò'=>'o','Ỏ'=>'o','Õ'=>'o','Ọ'=>'o','Ô'=>'o','Ố'=>'o','Ồ'=>'o','Ổ'=>'o','Ỗ'=>'o','Ộ'=>'o','Ơ'=>'o','Ớ'=>'o','Ờ'=>'o','Ở'=>'o','Ỡ'=>'o','Ợ'=>'o',
        'Ú'=>'u','Ù'=>'u','Ủ'=>'u','Ũ'=>'u','Ụ'=>'u','Ư'=>'u','Ứ'=>'u','Ừ'=>'u','Ử'=>'u','Ữ'=>'u','Ự'=>'u',
        'Ý'=>'y','Ỳ'=>'y','Ỷ'=>'y','Ỹ'=>'y','Ỵ'=>'y','Đ'=>'d',
    ];
    $s = strtr($s, $map);
    // iconv fallback
    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    $s = preg_replace('/[^a-z0-9]+/', '-', strtolower($s));
    $s = preg_replace('/-+/', '-', $s);
    $s = trim($s, '-');
    return $s;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $cat = trim($_POST['cat'] ?? 'Nhập vai');
    $size = trim($_POST['size'] ?? '1.28 MB');
    $jar = trim($_POST['jar'] ?? '');
    $thumb = trim($_POST['thumb'] ?? '');
    $shotsRaw = trim($_POST['shots'] ?? '');
    $desc = trim($_POST['desc'] ?? '');
    $vi = isset($_POST['vi']) ? true : false;
    $hot = isset($_POST['hot']) ? true : false;

    if ($name === '' || $jar === '') {
        $msg = 'Nhập tên và link JAR';
    } else {
        $slug = slugifyVi($name);
        $id = $slug . '-' . round(microtime(true)*1000);
        if ($thumb === '') $thumb = 'https://picsum.photos/seed/'.time().'/120/120';
        $shots = array_values(array_filter(array_map('trim', explode("\n", $shotsRaw))));
        if (empty($shots)) $shots = [$thumb];

        $new = [
            'id' => $id,
            'name' => $name,
            'cat' => $cat,
            'dev' => 'Chưa rõ',
            'size' => $size,
            'ver' => '1',
            'rating' => 4.5,
            'downloads' => 0,
            'res' => ['240x320'],
            'hot' => $hot,
            'vi' => $vi,
            'new' => true,
            'desc' => $desc ?: $name,
            'thumb' => $thumb,
            'shots' => $shots,
            'jar' => ['240x320' => $jar]
        ];

        $games = [];
        if (file_exists($dataFile)) {
            $json = file_get_contents($dataFile);
            $games = json_decode($json, true) ?: [];
        }
        array_unshift($games, $new);
        if (file_put_contents($dataFile, json_encode($games, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES))) {
            $msg = 'Đã thêm: <a href="game/'.$id.'.html" target="_blank">game/'.$id.'.html</a> - slug: '.$slug;
        } else {
            $msg = 'Lỗi ghi file - kiểm tra quyền data/games.json';
        }
    }
}

$games = [];
if (file_exists($dataFile)) $games = json_decode(file_get_contents($dataFile), true) ?: [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin PHP - JAVA.WAP.SH</title>
<link rel="stylesheet" href="style.css">
<style>body{padding:12px;background:#fff0f6}.wrap{max-width:720px;margin:0 auto;display:grid;gap:12px}.card{background:#fff;border:2px solid #ff8ec7;border-radius:14px;padding:14px}input,textarea,select{width:100%;padding:8px;border:1.5px solid #ffb3d9;border-radius:8px}label{font-size:12px;font-weight:700;color:#ff4d8d}.btn{background:linear-gradient(180deg,#ff8ec7,#ff4d8d);color:#fff;border:2px solid #fff;padding:10px 16px;border-radius:24px;font-weight:800;cursor:pointer}</style>
</head>
<body>
<div class="wrap">
<h1 style="font-family:Mali,cursive;color:#ff4d8d;text-align:center">Admin PHP (XAMPP)</h1>
<p style="text-align:center;font-size:11px">ID = <code>slugifyVi(ten) + '-' + microtime</code> → <code>kiem-linh-chu-tien-chi-chien-1788855732</code> - rewrite <code>vercel.json:4</code> <code>/game/(.*)</code> → <code>/game.html</code></p>
<?php if($msg): ?><div class="card" style="background:#e8ffe8;border-color:#34d399"><?=$msg?></div><?php endif; ?>
<form method="post" class="card" style="display:grid;gap:8px">
<label>Tên game *</label><input name="name" required placeholder="Kiếm Linh - Chư Tiên Chi Chiến">
<label>Thể loại</label><select name="cat"><option>Nhập vai</option><option>Hành Động</option><option>Đua xe</option><option>Bắn súng</option><option>Trí tuệ</option><option>Thể thao</option><option>Phiêu lưu</option><option>Nông trại</option></select>
<label>Dung lượng</label><input name="size" value="1.28 MB">
<label>Link JAR 240x320 *</label><input name="jar" required placeholder="https://www.mediafire.com/file/.../file">
<label>Thumb URL</label><input name="thumb" placeholder="https://...jpg">
<label>Shots (mỗi dòng 1 URL)</label><textarea name="shots" rows="3"></textarea>
<label>Mô tả</label><textarea name="desc" rows="3"></textarea>
<label><input type="checkbox" name="vi" checked> Việt Hóa</label> <label><input type="checkbox" name="hot"> HOT</label>
<button class="btn" type="submit">Thêm game → data/games.json</button>
</form>
<div class="card"><h3>Games hiện tại (<?=count($games)?>)</h3><pre style="background:#1a1a2e;color:#7fb1ff;padding:12px;border-radius:8px;overflow:auto;max-height:300px"><?=htmlspecialchars(json_encode($games, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT))?></pre></div>
<p style="font-size:11px;text-align:center">XAMPP: đặt <code>blog</code> vào <code>htdocs/blog</code> → <code>http://localhost/blog/admin.php</code> | Sau thêm xong <code>git add data/games.json && git commit && git push</code> để Vercel deploy.</p>
</div>
</body>
</html>
