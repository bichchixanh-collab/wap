# JAVA.WAP.SH — Blog Game Java J2ME

Kho game Java `.JAR` giao diện WAP Anime, rewrite URL đẹp tiếng Việt, deploy Vercel + GitHub Pages.

## 1. Cấu trúc

```
blog/
├── index.html          # trang chủ
├── game.html           # chi tiết game (render theo slug)
├── 404.html            # SPA fallback cho GitHub Pages (copy game.html)
├── category.html
├── vercel.json         # rewrite /game/:slug -> /game.html
├── server.js           # server test localhost (không cần Vercel)
├── data/games.json     # data game, id = slug-việt-hóa + timestamp
├── assets/ , sitemap.xml , style.css , script.js
```

**Rewrite:** `vercel.json:4`:
```json
{ "source": "/game/(.*)", "destination": "/game.html" },
{ "source": "/blog/game/(.*)", "destination": "/game.html" }
```
`game.html:211` `slugifyVi()` chuẩn tiếng Việt (`đ→d`, `NFD` bỏ dấu) → `data/games.json:3` `kiem-linh-chu-tien-chi-chien-1788855732` thay vì `-i-m-inh...html`. `game.html:227` `getId()` đọc cả `?id=` và `/game/slug.html`, fallback suffix(timestamp).

## 2. Upload lên GitHub

### Lần đầu (thư mục chưa có git)
```cmd
cd /d C:\Users\Admin\Documents\blog
git init
git add .
git commit -m "init blog"
git branch -M main
git remote add origin https://github.com/<user>/blog.git
git push -u origin main
```

### Đã có repo `bichchixanh-collab/blog` trên GitHub và local mới `git init`
```cmd
cd /d C:\Users\Admin\Documents\blog
git remote add origin https://github.com/bichchixanh-collab/blog.git
git fetch origin
git add assets/ robots.txt
git commit -m "add assets robots"  :: nếu báo untracked
git pull origin main --allow-unrelated-histories --rebase
:: nếu CONFLICT data/games.json: mở file, giữ id đẹp, xóa <<<<< ===== >>>>>, lưu
git add data/games.json
git rebase --continue
git push -u origin main
```

**Đơn giản nhất (ghi đè remote bằng local):**
```cmd
git rebase --abort  :: nếu đang rebase dở
git push -f origin main
```

### Cập nhật sau khi sửa
```cmd
cd /d C:\Users\Admin\Documents\blog
git add vercel.json 404.html game.html
git commit -m "fix rewrite"
git push
```

## 3. Deploy lên Vercel

1. vercel.com > Add New > Project > Import `bichchixanh-collab/blog`
2. Framework Preset: `Other`, Root Directory: `./` (để `vercel.json` ở root repo), Build Command để trống, Output Directory để trống.
3. Deploy → đợi `Ready`.
4. Custom domain `java.wap.sh`: Vercel > Settings > Domains > Add `java.wap.sh` → trỏ DNS theo hướng dẫn.
5. Mỗi `git push` Vercel tự redeploy. Kiểm tra `https://java.wap.sh/game/kiem-linh-chu-tien-chi-chien-1788855732.html` (Ctrl+F5).

> GitHub Pages tự serve `404.html:1` cho mọi `/game/slug.html` không có file thật nên cũng hết 404 mà không cần `vercel.json`.

## 4. Test trên localhost

`http://localhost/blog/game/...html` không đọc `vercel.json` nếu dùng `file://` hay `python -m http.server` → luôn 404.

Dùng server kèm rewrite:

```cmd
cd /d C:\Users\Admin\Documents\blog
node server.js
```
Mở:
- `http://localhost:3000/`
- `http://localhost:3000/game/kiem-linh-chu-tien-chi-chien-1788855732.html`
- `http://localhost:3000/blog/game/kiem-linh-chu-tien-chi-chien-1788855732.html`

`server.js:14` rewrite giống `vercel.json`. Dừng: `Ctrl+C`.

Hoặc: `npx vercel dev` (đọc `vercel.json` thật).

## 5. Xóa / làm lại git

**Xóa remote trên GitHub (xóa repo):** GitHub > repo > Settings > Danger Zone > Delete this repository.

**Xóa git local, làm lại:**
```cmd
cd /d C:\Users\Admin\Documents\blog
rmdir /s /q .git
git init
git add .
git commit -m "restart"
git branch -M main
git remote add origin https://github.com/<user>/blog.git
git push -f origin main
```

**Chỉ đổi remote:**
```cmd
git remote remove origin
git remote add origin https://github.com/<user>/moi.git
git push -u origin main
```

## 6. FAQ

- **Vẫn 404 sau push?** Kiểm tra `vercel.json` có ở root repo GitHub không (không nằm trong subfolder), Vercel Deployments có báo `Ready` không, test bằng tab ẩn danh.
- **Slug xấu `-i-m-inh...`?** Do tạo `id` chưa qua `slugifyVi()` `game.html:211`. Tạo id mới: `slugifyVi(tenGame) + '-' + Date.now()`.
- **Mất CSS khi vào `/game/...`?** Đã fix bằng `<base>` động `game.html:4` (`/blog/` vs `/`), dùng `href="style.css"` tương đối `game.html:25`.

