# Workflow Application - Quick Start Guide

## What Has Been Created

### ✅ Completed
1. **Laravel Project** - Fresh Laravel 12 installation
2. **Laravel Sanctum** - API authentication installed
3. **Database Schema** - All 8 tables with proper relationships:
   - users (with role and is_active fields)
   - departments
   - workflow_paths
   - workflow_path_steps
   - department_user (pivot table)
   - requests
   - request_transitions
   - request_attachments

4. **Model Files Created** (Need to be updated with relationships):
   - User.php
   - Department.php
   - WorkflowPath.php
   - WorkflowPathStep.php
   - Request.php
   - RequestTransition.php
   - RequestAttachment.php

5. **Documentation**:
   - IMPLEMENTATION_GUIDE.md - Complete model code and architecture
   - This QUICKSTART.md file

## Database Structure Explanation

### Workflow Logic:
1. **User submits request** → Goes to **Department A**
2. **Department A reviews** → Three options:
   - Reject (End)
   - Request more details (Back to user)
   - Approve & Choose Path (1-6)
3. **Request enters chosen path** → Flows through departments in that path
4. **Each department processes** → Can transfer within department (manager to manager)
5. **After last department** → Returns to **Department A** for final validation
6. **Department A final review** → Three options:
   - Approve (Complete)
   - Reject (End)
   - Send back to specific department

### Key Tables:
- **workflow_paths**: Define the 6 paths (configurable)
- **workflow_path_steps**: Define which departments are in each path and their order
- **department_user**: Links users to departments with their role (manager/employee)
- **request_transitions**: Complete history/audit trail of every action

## Next Steps to Complete the Application

### Step 1: Update Model Files (IMPORTANT)
Copy the model code from `IMPLEMENTATION_GUIDE.md` to each model file:
- `app/Models/User.php` - Add relationships and helper methods
- `app/Models/Department.php` - Add relationships
- `app/Models/WorkflowPath.php` - Add relationships
- `app/Models/WorkflowPathStep.php` - Add relationships and navigation methods
- `app/Models/Request.php` - Add relationships and workflow methods
- `app/Models/RequestTransition.php` - Add relationships
- `app/Models/RequestAttachment.php` - Add relationships and file handling

### Step 2: Create Workflow Engine Service
Create `app/Services/WorkflowEngine.php` with code from IMPLEMENTATION_GUIDE.md
This handles all workflow logic:
- Submit requests
- Assign to paths
- Move between departments
- Approvals/Rejections
- Request more details

### Step 3: Create Controllers
```bash
php artisan make:controller API/AuthController
php artisan make:controller API/RequestController --api
php artisan make:controller API/WorkflowController
php artisan make:controller API/DepartmentController --api
php artisan make:controller API/AdminController
```

### Step 4: Configure API Routes
Edit `routes/api.php`:

```php
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\WorkflowController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\AdminController;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // User requests
    Route::apiResource('requests', RequestController::class);
    Route::post('/requests/{id}/submit', [RequestController::class, 'submit']);
    Route::post('/requests/{id}/attachments', [RequestController::class, 'uploadAttachment']);

    // Workflow operations (Department managers/employees)
    Route::prefix('workflow')->group(function () {
        Route::get('/pending', [WorkflowController::class, 'pending']);
        Route::post('/{id}/assign-path', [WorkflowController::class, 'assignPath']);
        Route::post('/{id}/approve', [WorkflowController::class, 'approve']);
        Route::post('/{id}/reject', [WorkflowController::class, 'reject']);
        Route::post('/{id}/request-details', [WorkflowController::class, 'requestDetails']);
        Route::post('/{id}/complete', [WorkflowController::class, 'complete']);
        Route::post('/{id}/send-back', [WorkflowController::class, 'sendBack']);
    });

    // Admin routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('workflow-paths', AdminController::class);
        Route::post('/workflow-paths/{id}/steps', [AdminController::class, 'addStep']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/users/{id}/departments', [AdminController::class, 'assignDepartment']);
    });
});
```

### Step 5: Create Sample Data (Seeds)
Create seeders for testing:

```bash
php artisan make:seeder DepartmentSeeder
php artisan make:seeder WorkflowPathSeeder
php artisan make:seeder UserSeeder
```

Example seed data:
- Department A (is_department_a = true)
- 6 Workflow Paths
- Multiple departments for each path
- Test users with different roles

### Step 6: Set up Vue.js Frontend

```bash
npm install
npm install vue@3 vue-router@4 pinia axios
npm install @vueuse/core
```

Create frontend structure:
```
resources/
└── js/
    ├── app.js
    ├── router.js
    ├── store.js
    └── components/
        ├── Auth/
        │   ├── Login.vue
        │   └── Register.vue
        ├── Requests/
        │   ├── RequestForm.vue
        │   ├── RequestList.vue
        │   └── RequestDetail.vue
        ├── Workflow/
        │   ├── PendingRequests.vue
        │   └── WorkflowActions.vue
        └── Admin/
            ├── DepartmentManager.vue
            ├── WorkflowBuilder.vue
            └── UserManagement.vue
```

### Step 7: Configure CORS
Edit `config/cors.php`:
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:5173'], // Vite dev server
'supports_credentials' => true,
```

### Step 8: Environment Setup
Ensure `.env` has:
```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173

DB_CONNECTION=sqlite
DB_DATABASE=/home/nasser/workflow-app/database/database.sqlite

SESSION_DRIVER=cookie
SESSION_DOMAIN=localhost
SANCTUM_STATEFUL_DOMAINS=localhost:5173
```

## Running the Application

### Backend (Laravel API)
```bash
php artisan serve
# Runs on http://localhost:8000
```

### Frontend (Vue.js)
```bash
npm run dev
# Runs on http://localhost:5173
```

## Key Features to Implement

### User Interface Components:

1. **User Dashboard**:
   - Submit new request form (title, description, attachments)
   - View my requests with status
   - Add additional details when requested

2. **Department A Dashboard** (Initial Review):
   - List of pending requests
   - View request details
   - Options: Approve (choose path 1-6), Reject, Request More Details

3. **Department Dashboard** (For each path department):
   - List of requests in my department
   - Approve and forward to next step
   - Assign to other managers within department
   - Request more details or reject

4. **Department A Dashboard** (Final Validation):
   - List of requests returned from paths
   - Options: Complete, Reject, Send back to specific department

5. **Admin Panel**:
   - Manage departments (CRUD)
   - Create/Edit workflow paths
   - Define which departments in each path
   - Assign users to departments with roles
   - View all requests and transitions

### Notifications (Optional but Recommended):
- Email notifications when request status changes
- In-app notifications for assigned requests
- Dashboard notifications counter

### File Upload:
- Support multiple file attachments
- Store in `storage/app/public/attachments`
- File type validation (pdf, doc, images)
- File size limits

## Testing the Workflow

1. Create test user accounts with different roles
2. Create Department A and mark it
3. Create 5-6 other departments
4. Create 6 workflow paths with different department sequences
5. Assign users to departments as managers/employees
6. Submit a test request as regular user
7. Process through Department A → Path selection
8. Process through path departments
9. Final validation in Department A

## Security Considerations

- Use Laravel Sanctum for API authentication
- Validate user permissions (can only act on their department's requests)
- File upload validation and sanitization
- Rate limiting on API endpoints
- CSRF protection for state-changing operations
- Input validation on all forms

## Architecture Benefits

### Dynamic & Configurable:
- Admin can create any number of workflow paths
- Easily add/remove departments from paths
- Reassign users without code changes
- Change workflow steps without migrations

### Audit Trail:
- Every action recorded in request_transitions
- Complete history of who did what and when
- Comments on every transition
- Enables reporting and analytics

### Scalable:
- Can handle multiple concurrent workflows
- Department-based isolation
- Role-based access control
- Supports multiple managers per department

## Troubleshooting

### Common Issues:

1. **Foreign Key Errors**: Run migrations in order
2. **CORS Errors**: Check config/cors.php and .env
3. **Authentication Errors**: Verify Sanctum configuration
4. **File Upload Errors**: Check storage permissions: `php artisan storage:link`

### Useful Commands:
```bash
php artisan migrate:fresh --seed  # Reset database
php artisan cache:clear           # Clear cache
php artisan config:clear          # Clear config cache
php artisan route:list            # View all routes
php artisan tinker                # Interactive console
```

## What's Next?

The foundation is complete! Now you need to:
1. ✅ Database schema is ready
2. ⏳ Update model files with relationships
3. ⏳ Create WorkflowEngine service
4. ⏳ Create API controllers
5. ⏳ Set up Vue.js frontend
6. ⏳ Implement file uploads
7. ⏳ Add notifications

Refer to `IMPLEMENTATION_GUIDE.md` for detailed code examples for each component.

Good luck with your workflow application!
