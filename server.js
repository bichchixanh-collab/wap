const http = require('http');
const fs = require('fs');
const path = require('path');
const PORT = 3000;
const ROOT = __dirname;

const MIME = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'text/javascript; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.webp': 'image/webp',
  '.svg': 'image/svg+xml',
  '.txt': 'text/plain',
  '.xml': 'text/xml'
};

function serveFile(res, filePath) {
  if (!fs.existsSync(filePath) || fs.statSync(filePath).isDirectory()) return false;
  const ext = path.extname(filePath).toLowerCase();
  res.writeHead(200, { 'Content-Type': MIME[ext] || 'application/octet-stream' });
  fs.createReadStream(filePath).pipe(res);
  return true;
}

const server = http.createServer((req, res) => {
  let url = req.url.split('?')[0].split('#')[0];
  url = decodeURI(url);

  // rewrite /game/* và /blog/game/* -> game.html (giữ URL đẹp)
  if (/^\/game\//.test(url) || /^\/blog\/game\//.test(url)) {
    const file = path.join(ROOT, 'game.html');
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    fs.createReadStream(file).pipe(res);
    return;
  }

  // chuẩn hoá /blog/* -> /* khi chạy localhost/blog/
  if (url.startsWith('/blog/')) url = url.replace(/^\/blog/, '') || '/';
  if (url === '/') url = '/index.html';

  const filePath = path.join(ROOT, url.replace(/^\//, ''));
  // nếu là file tồn tại thì serve, không thì thử 404.html (SPA fallback)
  if (serveFile(res, filePath)) return;

  // fallback: nếu là /game/* đã handle ở trên, còn lại trả 404.html để test SPA
  const fallback = path.join(ROOT, '404.html');
  if (fs.existsSync(fallback)) {
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    fs.createReadStream(fallback).pipe(res);
    return;
  }
  res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
  res.end('404 Not Found: ' + req.url);
});

server.listen(PORT, () => {
  console.log(`Local test: http://localhost:${PORT}/`);
  console.log(`Test rewrite: http://localhost:${PORT}/game/kiem-linh-chu-tien-chi-chien-1788855732.html`);
  console.log(`Test blog prefix: http://localhost:${PORT}/blog/game/kiem-linh-chu-tien-chi-chien-1788855732.html`);
});
