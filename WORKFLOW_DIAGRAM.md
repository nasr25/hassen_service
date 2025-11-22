# Workflow Diagram and Process Flow

## Visual Workflow Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER SUBMITS REQUEST                     │
│                    (Title, Description, Files)                   │
└───────────────────────────────┬─────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                        DEPARTMENT A                              │
│                     (Initial Review)                             │
│                                                                   │
│  Options:                                                         │
│  1. ❌ REJECT → End Request                                      │
│  2. 📝 Request More Details → Back to User                       │
│  3. ✅ APPROVE → Choose Path (1-6)                               │
└───────────────────────────────┬─────────────────────────────────┘
                                │
                    ┌───────────┴───────────┐
                    │  SELECT WORKFLOW PATH  │
                    └───────────┬───────────┘
                                │
          ┌─────────────────────┼─────────────────────┐
          │                     │                     │
    ┌─────▼─────┐         ┌────▼────┐          ┌────▼────┐
    │  PATH 1   │         │ PATH 2  │   ...    │ PATH 6  │
    └─────┬─────┘         └────┬────┘          └────┬────┘
          │                    │                     │
    ┌─────▼─────────────┐      │               ┌────▼──────┐
    │   Department B    │      │               │  Dept X   │
    │  ┌─────────────┐  │      │               └────┬──────┘
    │  │Step 1       │  │      │                    │
    │  │Managers can │  │      │               ┌────▼──────┐
    │  │transfer     │  │      │               │  Dept Y   │
    │  │internally   │  │      │               └────┬──────┘
    │  └──────┬──────┘  │      │                    │
    │         │         │      │               ┌────▼──────┐
    │  ┌──────▼──────┐  │      │               │  Dept Z   │
    │  │Step 2       │  │      │               └────┬──────┘
    │  │             │  │      │                    │
    │  └──────┬──────┘  │      │                    │
    │         │         │      │                    │
    │  ┌──────▼──────┐  │      │                    │
    │  │Step N       │  │      │                    │
    │  └──────┬──────┘  │      │                    │
    └─────────┬─────────┘      │                    │
              │                │                    │
              └────────────────┴────────────────────┘
                               │
                    ALL PATHS RETURN TO
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                        DEPARTMENT A                              │
│                    (Final Validation)                            │
│                                                                   │
│  Options:                                                         │
│  1. ✅ COMPLETE → Request Done                                   │
│  2. ❌ REJECT → End Request                                      │
│  3. 🔙 Send Back → To Specific Previous Department              │
└───────────────────────────────┬─────────────────────────────────┘
                                │
                    ┌───────────┴───────────┐
                    │                       │
              ┌─────▼─────┐         ┌──────▼──────┐
              │ COMPLETED │         │  REJECTED   │
              └───────────┘         └─────────────┘
```

## Detailed Process Flows

### 1. User Request Submission Flow

```
User Dashboard
    │
    ├─► Fill Form
    │   ├─ Title (required)
    │   ├─ Description (required)
    │   └─ Upload Files (optional, multiple)
    │
    ├─► Save as Draft (can edit later)
    │
    └─► Submit Request
        │
        └─► Status: "Pending"
            └─► Assigned to: Department A
```

### 2. Department A - Initial Review

```
Department A Dashboard
    │
    ├─► View Pending Requests List
    │
    ├─► Click Request to Review
    │   ├─ View Details
    │   ├─ View Attachments
    │   └─ View User Information
    │
    └─► Take Action:
        │
        ├─► Option 1: REJECT
        │   ├─ Enter Reason (required)
        │   └─ Status → "Rejected"
        │       └─ Request Ends
        │
        ├─► Option 2: REQUEST MORE DETAILS
        │   ├─ Enter Comment (required)
        │   └─ Status → "Need More Details"
        │       └─ Back to User Dashboard
        │           └─ User adds details
        │               └─ Resubmit → Back to Dept A
        │
        └─► Option 3: APPROVE & ASSIGN PATH
            ├─ Select Path (1-6)
            └─ Status → "In Review"
                └─ Assigned to First Department in Path
```

### 3. Path Department Processing

```
Department Dashboard (B, C, D, etc.)
    │
    ├─► View Assigned Requests
    │
    ├─► Manager Actions:
    │   │
    │   ├─► Assign to Self
    │   ├─► Assign to Another Manager (in same dept)
    │   └─► Assign to Employee (in same dept)
    │
    ├─► Process Request:
    │   │
    │   ├─► Add Comments/Notes
    │   ├─► Upload Additional Documents
    │   └─► Make Decision:
    │       │
    │       ├─► APPROVE → Next Step
    │       │   └─ If last step → Return to Dept A
    │       │
    │       ├─► REJECT → End Request
    │       │   └─ Enter Reason
    │       │
    │       └─► REQUEST MORE DETAILS
    │           └─ Back to User or Previous Dept
    │
    └─► View Request History (Audit Trail)
```

### 4. Department A - Final Validation

```
Department A Final Review
    │
    ├─► View Returned Requests
    │   ├─ View All Path History
    │   ├─ View All Comments
    │   └─ View All Actions Taken
    │
    └─► Final Decision:
        │
        ├─► COMPLETE ✅
        │   ├─ Optional: Add Final Comments
        │   └─ Status → "Completed"
        │       └─ Request Ends Successfully
        │
        ├─► REJECT ❌
        │   ├─ Enter Reason (required)
        │   └─ Status → "Rejected"
        │       └─ Request Ends
        │
        └─► SEND BACK 🔙
            ├─ Select Previous Department
            ├─ Enter Comments (required)
            └─ Status → "In Review"
                └─ Back to Selected Department
                    └─ Process Again
                        └─ Return to Dept A
```

## Status Flow Chart

```
DRAFT
  │
  ├─► SUBMIT ────────────────────────────┐
  │                                       │
  ▼                                       ▼
PENDING ◄──────────────────────── NEED_MORE_DETAILS
  │                                       ▲
  ├─► APPROVE                             │
  │                                       │
  ▼                                       │
IN_REVIEW ───────────────────────────────┘
  │         (Can loop back multiple times)
  │
  ├─► Path Complete
  │
  ▼
PENDING (Back at Dept A)
  │
  ├─► COMPLETE ──► COMPLETED ✅
  ├─► REJECT ────► REJECTED ❌
  └─► SEND BACK ─► IN_REVIEW (loops)
```

## Example Path Configuration

### Path 1: Simple Approval (2 departments)
```
User → Dept A → Dept B → Dept C → Dept A → Complete
         ↓        ↓        ↓        ↓
      (Choose) (Review) (Final) (Validate)
```

### Path 2: Complex Multi-Department (4 departments)
```
User → Dept A → Dept D → Dept E → Dept F → Dept G → Dept A → Complete
         ↓        ↓        ↓        ↓        ↓        ↓
      (Choose) (Tech)  (Legal) (Finance) (Ops)  (Validate)
```

### Path 3: Financial (3 departments)
```
User → Dept A → Finance Dept → Accounting → Budget → Dept A → Complete
         ↓           ↓             ↓          ↓        ↓
      (Choose)   (Review)      (Verify)   (Approve) (Final)
```

## User Roles and Permissions

### Admin
```
✅ Full access to everything
✅ Create/Edit/Delete departments
✅ Create/Edit/Delete workflow paths
✅ Configure path steps (which depts, order)
✅ Assign users to departments
✅ Change user roles
✅ View all requests and complete audit trail
```

### Manager (of a department)
```
✅ View requests in their department
✅ Assign requests to employees in their department
✅ Transfer requests to other managers in same department
✅ Approve/Reject requests
✅ Request more details
✅ Add comments and documents
❌ Cannot access other departments
❌ Cannot configure workflows
```

### Employee (of a department)
```
✅ View requests assigned to them
✅ Add comments and documents
✅ Can recommend action (manager approves)
❌ Cannot approve/reject directly
❌ Cannot assign to others
```

### Regular User
```
✅ Submit new requests
✅ View their own requests
✅ Upload attachments
✅ Provide additional details when requested
✅ View request history and status
❌ Cannot access workflow operations
❌ Cannot view other users' requests
```

## Notification System

### User Receives Notifications When:
- Request status changes
- More details requested
- Request approved/rejected
- Request completed

### Manager Receives Notifications When:
- New request assigned to their department
- Request returned from next department
- Employee makes recommendation
- Final validation needed

### Department A Receives Notifications When:
- New request submitted
- Request completed in path (returning for validation)
- Request needs attention

## Audit Trail (request_transitions table)

Every action creates a record:
```
{
  request_id: 123,
  from_department: "Dept B",
  to_department: "Dept C",
  from_user: "John (Manager)",
  to_user: "Mary (Manager)",
  actioned_by: "John",
  action: "approve",
  from_status: "in_review",
  to_status: "in_review",
  comments: "Approved after technical review",
  created_at: "2025-11-20 15:30:00"
}
```

This provides:
- Complete history of request journey
- Who did what and when
- Comments at each step
- Status changes
- Department transfers
- Useful for reporting and analytics

## API Endpoints Summary

```
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/auth/user

GET    /api/requests                    # List user's requests
POST   /api/requests                    # Create draft
GET    /api/requests/{id}               # View details
PUT    /api/requests/{id}               # Update draft
POST   /api/requests/{id}/submit        # Submit for review
POST   /api/requests/{id}/attachments   # Upload file

GET    /api/workflow/pending            # Dept requests
POST   /api/workflow/{id}/assign-path   # Dept A: Choose path
POST   /api/workflow/{id}/approve       # Move to next
POST   /api/workflow/{id}/reject        # Reject
POST   /api/workflow/{id}/request-details # Ask for details
POST   /api/workflow/{id}/complete      # Final approval
POST   /api/workflow/{id}/send-back     # Send to prev dept

GET    /api/admin/departments
POST   /api/admin/departments
GET    /api/admin/workflow-paths
POST   /api/admin/workflow-paths
POST   /api/admin/workflow-paths/{id}/steps
GET    /api/admin/users
POST   /api/admin/users/{id}/departments
```

## Database Relationships

```
users
  ├─ belongsToMany → departments (via department_user)
  ├─ hasMany → submittedRequests (as user_id)
  └─ hasMany → assignedRequests (as current_user_id)

departments
  ├─ belongsToMany → users (via department_user)
  ├─ hasMany → workflowPathSteps
  └─ hasMany → currentRequests

workflow_paths
  ├─ hasMany → steps (workflow_path_steps)
  └─ hasMany → requests

requests
  ├─ belongsTo → user
  ├─ belongsTo → currentDepartment
  ├─ belongsTo → currentAssignee
  ├─ belongsTo → workflowPath
  ├─ hasMany → attachments
  └─ hasMany → transitions
```

This comprehensive workflow system is:
- ✅ Dynamic and configurable
- ✅ Fully auditable
- ✅ Role-based access control
- ✅ Scalable to any number of paths/departments
- ✅ Supports complex multi-step workflows
