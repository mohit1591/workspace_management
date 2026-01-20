### Setup Steps

1. **Clone the repository**
```bash
git clone https://github.com/mohit1591/workspace_management
cd workspace-management
```

3. **Install dependencies**
```bash
composer install
```

4. **Run migrations and seeders**
```bash
php artisan migrate --seed
```

6. **Start the development server**
```bash
php artisan serve --host=localhost --port=8000
```

You also need to install postgress else you can simply install mysql 

Currently have only built login and items api 
The API will be available at `http://localhost:8000/api/login`

## Login Credentials

### System Admin
- Email: `admin@system.com`
- Password: `password`
- Can access all workspaces

### Workspace 1 (Acme Corporation)
**Admin:**
- Email: `john@acme.com`
- Password: `password`

**Member:**
- Email: `jane@acme.com`
- Password: `password`

### Workspace 2 (TechStart Inc)
**Admin:**
- Email: `bob@techstart.com`
- Password: `password`

**Member:**
- Email: `alice@techstart.com`
- Password: `password`


## API Usage Examples

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@acme.com","password":"password"}'
```


## Tenant Isolation Strategy

### Implementation Approach

This application implements **Global Query Scopes** for automatic tenant isolation:

1. **Global Scopes on Models**: Both `Item` and `Attachment` models have global scopes that automatically filter queries based on the authenticated user's workspace.

### Benefits
- **Automatic**: Developers don't need to remember to add WHERE clauses
- **Secure**: Impossible to accidentally query cross-tenant data
- **Centralized**: Logic is in one place (model's booted method)

### Code Example
```php
// In Item model
protected static function booted(): void
{
    static::addGlobalScope('workspace', function (Builder $builder) {
        $user = auth()->user();
        
        if (!$user || $user->isSystemAdmin()) {
            return;
        }
        
        $builder->where('items.workspace_id', $user->workspace_id);
    });
}
```


## Trade-offs & Notes
PostgreSQL was not set up on my system initially, so I had to install and configure it. This process took additional time due to issues related to an existing installation.

I began working on the task around 3:00 PM. Additionally, while setting up the React project, I encountered challenges because React has recently been updated to version 19, which introduced several breaking changes. My prior experience is primarily with React 18. As a result, I set up the project using React 19, but due to time constraints, I was unable to create any pages or components.

However, I have implemented the basic project boilerplate, defined the necessary migrations and seeders, included a login API and Items API, and created the required models along with their relationships.