## 🚀 Installation

### Step 1: Clone the Repository
```bash
git clone https://github.com/kierdev/beast-link-backend.git
cd beast-link-backend
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Set Up Environment Variables
```bash
cp .env.example .env
```

### Step 4: Generate Application Key
```bash
php artisan key:generate
```

### Step 5: Run Database Migrations
```bash
php artisan migrate
```

### Step 6: Start the Local Server

```bash
php artisan serve
```

### Notes
1. All logic are inside app/Http/Controllers.  
2. All models are inside app/Models.  
3. All database migrations are inside database/migrations  
4. All API routes should be inside routes/api.php. Do not add anything inside web.php and console.php.  