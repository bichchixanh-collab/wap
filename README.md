# JAVA.WAP.SH - Blog Game Java

Đã fix 4 lỗi: rewrite 404, slug tiếng Việt xấu, xóa NPH/Ver/rate/định dạng/lượt tải, lightbox vuốt, popup SOS treo.

## File đã fix
- `vercel.json:4` rewrite `/game/(.*)` -> `/game.html`
- `game.html:211` `slugifyVi()` + `game.html:227` `getId()` + `data/games.json:3` slug đẹp `kiem-linh-chu-tien-chi-chien-1788855732`
- `game.html:136` xóa NPH/Ver/rate/lượt tải/định dạng
- `game.html:315` lightbox vuốt
- `game.html:286` popup fix `overflow`
- `404.html:1` copy `game.html` cho GitHub Pages
- `server.js:1` test localhost

## 1. Push lên GitHub (làm 1 lần)

**Nếu báo `remote origin already exists` hoặc muốn xóa hết làm lại:**
```cmd
cd /d C:\Users\Admin\Documents\blog
rmdir /s /q .git
git init
git add .
git commit -m "full code"
git branch -M main
git remote add origin https://github.com/bichchixanh-collab/blog.git
git push -f origin main
```

**Cập nhật sau này:**
```cmd
cd /d C:\Users\Admin\Documents\blog
git add .
git commit -m "update"
git push
```

## 2. Deploy Vercel
1. vercel.com > Add New Project > Import `bichchixanh-collab/blog` > Framework `Other` > Deploy
2. Đợi `Ready` > test `https://j2mewap.vercel.app/game/kiem-linh-chu-tien-chi-chien-1788855732.html`
3. Mỗi `git push` Vercel tự deploy lại

## 3. Test localhost
```cmd
cd /d C:\Users\Admin\Documents\blog
node server.js
```
Mở `http://localhost:3000/game/kiem-linh-chu-tien-chi-chien-1788855732.html`
Dừng: `Ctrl+C`

Không dùng `file://` hay `python -m http.server` sẽ 404 do không có rewrite.
