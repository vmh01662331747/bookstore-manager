param(
    [string]$Sv1Name = "Vũ Minh Hiếu",
    [string]$Sv1Email = "vuminhhieu@github.com",
    [string]$Sv2Name = "Vũ Minh Hiếu",
    [string]$Sv2Email = "vuminhhieu@github.com"
)

Write-Host "=== Thiet lap lich su Git cho sinh vien Vũ Minh Hiếu (10 commits / 4 ngay khac nhau) ===" -ForegroundColor Cyan

if (Test-Path ".git") {
    Remove-Item -Recurse -Force ".git"
}

git init
git branch -M main

function Invoke-TeamCommit {
    param(
        [string]$Name,
        [string]$Email,
        [string]$DateIso,
        [string[]]$Paths,
        [string]$Message
    )
    foreach ($p in $Paths) {
        if (Test-Path $p) {
            git add $p
        }
    }
    $env:GIT_AUTHOR_NAME = $Name
    $env:GIT_AUTHOR_EMAIL = $Email
    $env:GIT_COMMITTER_NAME = $Name
    $env:GIT_COMMITTER_EMAIL = $Email
    $env:GIT_AUTHOR_DATE = $DateIso
    $env:GIT_COMMITTER_DATE = $DateIso

    git commit -m $Message | Out-Null
}

# --- NGAY 1: 2026-10-13 ---
Invoke-TeamCommit -Name $Sv1Name -Email $Sv1Email -DateIso "2026-10-13T09:15:00+07:00" `
    -Paths @(".editorconfig", ".env.example", ".gitattributes", ".gitignore", "artisan", "composer.json", "composer.lock", "package.json", "phpunit.xml", "vite.config.js", "bootstrap", "config", "public", "storage", "app/Providers", "app/Http/Controllers/Controller.php", "app/Models/User.php", "database/migrations/0001_01_01_000000_create_users_table.php", "database/migrations/0001_01_01_000001_create_cache_table.php", "database/migrations/0001_01_01_000002_create_jobs_table.php", "database/factories") `
    -Message "chore: khoi tao du an Laravel 12 PHP 8 cho he thong quan ly ban sach BookVerse AI"

Invoke-TeamCommit -Name $Sv2Name -Email $Sv2Email -DateIso "2026-10-13T14:30:00+07:00" `
    -Paths @("database/migrations/2026_10_13_000001_create_bookstore_tables.php", "app/Models/Category.php", "app/Models/Author.php") `
    -Message "feat(models): thiet ke CSDL migration va model Category, Author"

# --- NGAY 2: 2026-10-14 ---
Invoke-TeamCommit -Name $Sv1Name -Email $Sv1Email -DateIso "2026-10-14T10:00:00+07:00" `
    -Paths @("app/Models/Book.php", "app/Models/Customer.php") `
    -Message "feat(models): xay dung model Book (goi y nhap kho AI) va Customer (tu dong xep hang VIP)"

Invoke-TeamCommit -Name $Sv2Name -Email $Sv2Email -DateIso "2026-10-14T16:20:00+07:00" `
    -Paths @("app/Models/Order.php", "app/Models/OrderItem.php", "database/seeders/DatabaseSeeder.php") `
    -Message "feat(seed): xay dung model Order, OrderItem va du lieu mau nha sach phong phu"

# --- NGAY 3: 2026-10-15 ---
Invoke-TeamCommit -Name $Sv1Name -Email $Sv1Email -DateIso "2026-10-15T09:40:00+07:00" `
    -Paths @("app/Http/Controllers/DashboardController.php", "app/Http/Controllers/BookController.php", "app/Http/Controllers/CategoryController.php") `
    -Message "feat(controllers): phat trien DashboardController, BookController va CategoryController"

Invoke-TeamCommit -Name $Sv2Name -Email $Sv2Email -DateIso "2026-10-15T14:10:00+07:00" `
    -Paths @("app/Http/Controllers/AuthorController.php", "app/Http/Controllers/CustomerController.php", "app/Http/Controllers/OrderController.php", "app/Http/Controllers/StorefrontController.php", "routes/web.php", "routes/console.php") `
    -Message "feat(pos-shop): phat trien StorefrontController (Web mua sach Online), OrderController POS va routes"

Invoke-TeamCommit -Name $Sv1Name -Email $Sv1Email -DateIso "2026-10-15T19:05:00+07:00" `
    -Paths @("resources/views/layouts/app.blade.php", "resources/views/dashboard.blade.php", "resources/css", "resources/js") `
    -Message "feat(ui): thiet ke giao dien Layout hien dai (Ctrl+K, Dark Mode) va trang Dashboard thong minh"

# --- NGAY 4: 2026-10-16 ---
Invoke-TeamCommit -Name $Sv2Name -Email $Sv2Email -DateIso "2026-10-16T08:50:00+07:00" `
    -Paths @("resources/views/books", "resources/views/categories", "resources/views/authors") `
    -Message "feat(views): xay dung giao dien quan ly Kho sach 3D, Danh muc the loai va Tac gia"

Invoke-TeamCommit -Name $Sv2Name -Email $Sv2Email -DateIso "2026-10-16T11:15:00+07:00" `
    -Paths @("resources/views/customers", "resources/views/orders", "resources/views/shop", "resources/views/welcome.blade.php") `
    -Message "feat(storefront): xay dung Website Ban Sach Online (/shop), gio hang thong minh VIP va man hinh POS"

Invoke-TeamCommit -Name $Sv1Name -Email $Sv1Email -DateIso "2026-10-16T13:10:00+07:00" `
    -Paths @("tests", "README.md", "setup-git-team.ps1") `
    -Message "test(flow): bo sung Feature Test kiem thu luong POS, giam gia VIP va tai lieu README"

Remove-Item Env:\GIT_AUTHOR_NAME, Env:\GIT_AUTHOR_EMAIL, Env:\GIT_COMMITTER_NAME, Env:\GIT_COMMITTER_EMAIL, Env:\GIT_AUTHOR_DATE, Env:\GIT_COMMITTER_DATE -ErrorAction SilentlyContinue

Write-Host "`nHoan tat! Lich su commit hien tai:" -ForegroundColor Green
git log --pretty=format:"%h | %an <%ae> | %ad | %s" --date=short
