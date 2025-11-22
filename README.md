# Workflow Management System

A comprehensive workflow management system built with Laravel 12 (backend) and Vue.js 3 (frontend). This application allows users to submit requests that flow through multiple departments with role-based access control, evaluations, and workflow path management.

## Features

### Internationalization (i18n)
- **Multi-language Support**: Full support for English and Arabic languages
- **RTL/LTR Layout**: Automatic right-to-left layout for Arabic
- **Language Switcher**: Easy toggle between languages with instant UI updates
- **Persistent Preference**: Language selection saved in browser localStorage
- **Arabic Typography**: Custom Arabic fonts (Cairo, Tajawal) for better readability

### User Management
- **Role-Based Access Control**: Admin, Manager, Employee, and User roles
- **Department Assignment**: Users can be assigned to multiple departments with different roles
- **Dynamic User Management**: Admins can create, update, and delete users through the admin panel

### Request Management
- **Request Submission**: Users can submit requests with attachments
- **Request Tracking**: Full history of request transitions between departments
- **Status Management**: Pending, In Review, Need More Details, Completed, Rejected
- **Attachments**: Support for file uploads with each request
- **Edit & Resubmit**: Users can edit and resubmit requests that need more details

### Workflow System
- **Custom Workflow Paths**: Define multiple workflow paths with sequential department steps
- **Department A Review**: Initial review and path assignment by Department A managers
- **Department Processing**: Each department can have managers and employees
- **Two-Level Approval**: Employees process requests, managers validate before moving to next step
- **Return Mechanism**: Ability to return requests to previous departments or Department A

### Evaluation System
- **Configurable Questions**: Admin can create evaluation questions with weights (total 100%)
- **1-10 Rating Scale**: Each question answered on a scale of 1-10
- **Mandatory Evaluation**: Department A managers must complete evaluation before taking any action
- **Weighted Scoring**: Final score calculated as (answer/10) * weight for each question
- **Action Blocking**: All action buttons disabled until evaluation is completed

### Admin Panel
- **Department Management**: Create, update, and manage departments
- **User Management**: Full CRUD operations for users
- **User-Department Assignment**: Assign users to departments with manager/employee roles
- **Evaluation Questions**: Manage evaluation questions with weight validation
- **Request Tracking**: View all requests with full transition history

## Technology Stack

### Backend
- **Laravel 12**: PHP framework
- **SQLite**: Database
- **Sanctum**: API authentication
- **RESTful API**: Clean API architecture

### Frontend
- **Vue.js 3**: Progressive JavaScript framework
- **Composition API**: Modern Vue.js patterns with `<script setup>`
- **Vue I18n**: Internationalization and localization
- **Pinia**: State management
- **Vue Router**: Client-side routing
- **Axios**: HTTP client
- **Vite**: Fast development build tool

## Installation Instructions

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18 or higher
- npm

### Backend Setup (Laravel)

1. **Clone the repository**
   ```bash
   git clone https://github.com/nasr25/hassen_service.git
   cd hassen_service
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   ```

4. **Configure database**
   Edit `.env` file and set database connection:
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/workflow-app/database/database.sqlite
   ```

5. **Create SQLite database**
   ```bash
   touch database/database.sqlite
   ```

6. **Generate application key**
   ```bash
   php artisan key:generate
   ```

7. **Run database migrations**
   ```bash
   php artisan migrate
   ```

8. **Seed demo data (optional)**
   ```bash
   php artisan db:seed
   ```

9. **Start Laravel development server**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
   The backend API will be available at `http://localhost:8000`

### Frontend Setup (Vue.js)

1. **Navigate to frontend directory**
   ```bash
   cd workflow-frontend
   ```

2. **Install npm dependencies**
   ```bash
   npm install
   ```

3. **Start Vite development server**
   ```bash
   npm run dev -- --host 0.0.0.0 --port 5173
   ```
   The frontend will be available at `http://localhost:5173`

## Default Users

After running the seeders, the following users are available. All users have the password: **password**

### System Users
- **Admin**: `admin@workflow.com` - Full system access
- **User**: `user@workflow.com` - Can submit requests

### Department A Manager
- **Manager A**: `manager.a@workflow.com` - Reviews and assigns workflow paths

### Department Managers
- **Tech Manager**: `manager.tech@workflow.com` - Technology department
- **Finance Manager**: `manager.finance@workflow.com` - Finance department
- **Legal Manager**: `manager.legal@workflow.com` - Legal department
- **Strategy Manager**: `manager.strategy@workflow.com` - Strategy department
- **HR Manager**: `manager.hr@workflow.com` - Human Resources department

### Department Employees
- **Tech Employee 1**: `emp.tech1@workflow.com`
- **Tech Employee 2**: `emp.tech2@workflow.com`
- **Finance Employee**: `emp.finance@workflow.com`
- **Legal Employee**: `emp.legal@workflow.com`
- **Strategy Employee 1**: `emp.strategy1@workflow.com`
- **Strategy Employee 2**: `emp.strategy2@workflow.com`
- **HR Employee 1**: `emp.hr1@workflow.com`
- **HR Employee 2**: `emp.hr2@workflow.com`

> **Note**: The login page includes a convenient test accounts section where you can click on any account to auto-fill the credentials.

## Usage Guide

### Language Selection
The application supports both English and Arabic languages:

1. **Switching Languages**: Click the language switcher button (🌐) in the top-right corner
   - Current language: "عربي" (when in English) or "English" (when in Arabic)
   - Click to instantly switch between languages

2. **RTL Support**: When Arabic is selected:
   - Layout automatically switches to right-to-left
   - All UI elements, navigation, and text adjust accordingly
   - Arabic fonts (Cairo, Tajawal) are applied for better readability

3. **Persistent Selection**: Your language preference is automatically saved and will be remembered on your next visit

4. **Supported Pages** (currently translated):
   - Login page
   - Dashboard
   - My Requests
   - New Request form

> **Note**: Additional pages (Request Details, Workflow Review, Department Workflow, Admin Panel) will be translated in future updates.

### For Regular Users
1. Login with user credentials
2. Submit a new request with title, description, and attachments
3. Track request status through the dashboard
4. Respond to "Need More Details" requests by editing and resubmitting

### For Department A Managers
1. Login with Department A manager credentials
2. View pending requests in Workflow Review
3. **Complete evaluation first** - Click "Start Evaluation" button
4. Answer all evaluation questions (1-10 scale)
5. Once evaluation is complete, action buttons become enabled
6. Assign request to a workflow path (IT, Finance, Legal, HR)
7. Or reject, request details, complete, or return to previous department

### For Department Managers
1. Login with department manager credentials
2. View requests in your department
3. Assign requests to employees in your department
4. Review completed work from employees
5. Move request to next department or return to Department A

### For Department Employees
1. Login with employee credentials
2. View requests assigned to you
3. Process the request
4. Return completed work to your manager

### For Admins
1. Login with admin credentials
2. Access Admin Panel
3. **Departments Tab**: Create and manage departments
4. **Users Tab**: Create and manage users
5. **Assignments Tab**: Assign users to departments with roles
6. **Evaluation Questions Tab**: Configure evaluation questions with weights
7. View all requests and full system activity

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout (authenticated)
- `GET /api/auth/user` - Get current user (authenticated)

### Requests
- `GET /api/requests` - Get user's requests
- `POST /api/requests` - Create request
- `GET /api/requests/{id}` - Get request details
- `PUT /api/requests/{id}` - Update request
- `DELETE /api/requests/{id}` - Delete request
- `POST /api/requests/{id}/submit` - Submit request
- `POST /api/requests/{id}/attachments` - Upload attachment
- `DELETE /api/requests/{requestId}/attachments/{attachmentId}` - Delete attachment

### Workflow (Department A Managers)
- `GET /api/workflow/pending-requests` - Get pending requests
- `GET /api/workflow/paths` - Get workflow paths
- `POST /api/workflow/requests/{id}/assign-path` - Assign to workflow path
- `POST /api/workflow/requests/{id}/reject` - Reject request
- `POST /api/workflow/requests/{id}/request-details` - Request more details
- `POST /api/workflow/requests/{id}/complete` - Complete request
- `POST /api/workflow/requests/{id}/return-to-previous` - Return to previous department
- `GET /api/workflow/requests/{id}/evaluation-questions` - Get evaluation questions
- `POST /api/workflow/requests/{id}/evaluation` - Submit evaluation

### Department Workflow (Managers & Employees)
- `GET /api/department/requests` - Get department requests
- `GET /api/department/employees` - Get department employees
- `POST /api/department/requests/{id}/assign-employee` - Assign to employee
- `POST /api/department/requests/{id}/return-to-manager` - Return to manager
- `POST /api/department/requests/{id}/return-to-dept-a` - Return to Department A

### Admin
- `GET /api/admin/departments` - Get all departments
- `POST /api/admin/departments` - Create department
- `PUT /api/admin/departments/{id}` - Update department
- `DELETE /api/admin/departments/{id}` - Delete department
- `GET /api/admin/users` - Get all users
- `POST /api/admin/users` - Create user
- `PUT /api/admin/users/{id}` - Update user
- `DELETE /api/admin/users/{id}` - Delete user
- `POST /api/admin/assign-user-department` - Assign user to department
- `PUT /api/admin/update-user-department-role` - Update user role in department
- `POST /api/admin/remove-user-department` - Remove user from department
- `GET /api/admin/requests` - Get all requests
- `GET /api/admin/requests/{id}` - Get request detail
- `GET /api/admin/evaluation-questions` - Get evaluation questions
- `POST /api/admin/evaluation-questions` - Create evaluation question
- `PUT /api/admin/evaluation-questions/{id}` - Update evaluation question
- `DELETE /api/admin/evaluation-questions/{id}` - Delete evaluation question
- `GET /api/admin/evaluation-weight-total` - Get total weight of active questions

## Database Schema

### Main Tables
- **users**: User accounts with roles
- **departments**: Department definitions
- **department_user**: Many-to-many relationship with roles (manager/employee)
- **requests**: User requests with status tracking
- **workflow_paths**: Predefined workflow paths
- **workflow_path_steps**: Steps within each workflow path
- **request_transitions**: Full audit trail of request movements
- **request_attachments**: File attachments for requests
- **evaluation_questions**: Configurable evaluation questions with weights
- **request_evaluations**: Evaluation answers for each request

## Workflow Process

1. **User submits request** → Status: Draft
2. **User submits for review** → Status: Pending, moves to Department A
3. **Department A manager completes evaluation** → All questions answered (1-10)
4. **Department A manager assigns path** → Request moves to first department in path
5. **Department manager assigns to employee** → Employee processes request
6. **Employee completes work** → Returns to department manager
7. **Department manager validates** → Moves to next department in path
8. **Final department completes** → Returns to Department A
9. **Department A performs final review** → Complete or return for revisions
10. **Request completed** → Status: Completed

## Evaluation System

### Configuration (Admin)
- Create evaluation questions with weights
- Total weights must equal 100%
- Questions can be activated/deactivated
- Order can be customized

### Evaluation Process (Department A Manager)
1. All action buttons are disabled for new requests
2. Warning message: "⚠️ Complete evaluation before taking action"
3. Click "Start Evaluation" button
4. Answer each question on 1-10 scale
5. Optional notes for each question
6. Progress indicator shows N/M questions answered
7. Submit button enabled only when all questions answered
8. After submission: "✅ Evaluation completed"
9. Action buttons become enabled
10. Can view/edit evaluation later

### Scoring
- Each answer: 1-10 rating
- Each question has a weight (percentage)
- Score per question = (answer/10) × weight
- Total score = sum of all question scores (max 100)

## Project Structure

```
workflow-app/                 # Laravel backend
├── app/
│   ├── Http/Controllers/API/
│   │   ├── AuthController.php
│   │   ├── RequestController.php
│   │   ├── WorkflowController.php
│   │   ├── DepartmentWorkflowController.php
│   │   └── AdminController.php
│   └── Models/
│       ├── User.php
│       ├── Department.php
│       ├── Request.php
│       ├── WorkflowPath.php
│       ├── RequestTransition.php
│       ├── EvaluationQuestion.php
│       └── RequestEvaluation.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
└── ...

workflow-frontend/            # Vue.js frontend
├── src/
│   ├── components/
│   │   └── LanguageSwitcher.vue    # Language toggle component
│   ├── locales/
│   │   ├── en.json                  # English translations
│   │   └── ar.json                  # Arabic translations
│   ├── stores/
│   │   └── auth.js                  # Authentication store
│   ├── views/
│   │   ├── Login.vue
│   │   ├── Dashboard.vue
│   │   ├── MyRequests.vue
│   │   ├── NewRequest.vue
│   │   ├── RequestDetail.vue
│   │   ├── WorkflowReview.vue
│   │   ├── DepartmentWorkflow.vue
│   │   └── AdminPanel.vue
│   ├── router/
│   │   └── index.js                 # Route definitions
│   ├── i18n.js                      # i18n configuration
│   ├── main.js                      # App entry point
│   └── App.vue                      # Root component
└── ...
```

## License

This project is open-source software licensed under the MIT license.

## Support

For issues and feature requests, please create an issue in the GitHub repository.
