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

## Git Branching Strategy

We follow a three-branch workflow to maintain code stability and enable parallel development:

### Branches

1. **`main`**

    - Production-ready code only
    - Protected branch (direct commits disabled)
    - Updated only via approved merge requests from `development`
    - Tagged with version numbers for releases

2. **`development`**

    - Primary integration branch
    - All feature branches merge here first
    - Where staging/testing occurs
    - Must be stable at all times

3. **`feature/[feature-name]`**
    - Created from `development` branch
    - Named descriptively (e.g., `feature/user-auth`)
    - Short-lived branches (1-3 days recommended)
    - Deleted after merging to `development`

## Simple Git Workflow

### Basic Steps

1.  **Get latest code**

    ```bash
    git checkout development
    git pull
    # Explicitly pull from remote
    ```

2.  **Create feature branch**

    ```bash
    git checkout -b feature/module-name
    # Make your changes...
    ```

3.  **Commit your changes**

    ```bash
    # View changes
    git status

    # Add all files
    git add .

    # Commit
    git commit -m "feat: add login button"  # Use semantic prefixes (feat/fix/chore)
    ```

4.  **Push**

    ```bash
    git push -u origin feature/your-feature # `-u` sets upstream branch
    ```

    -   Create Pull Request (GitHub) from:

    -   feature/your-feature → development

## Git Commit Conventions

-   **`feat`**: A new feature
    Example: `feat: add authentication route`

-   **`fix`**: A bug fix
    Example: `fix: refactor authentication routes`

-   **`chore`**: Non-functional changes (e.g., tooling, build, deps)
    Example: `chore: update metadata`
