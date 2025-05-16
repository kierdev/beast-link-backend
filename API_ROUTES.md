# BeastLink University Backend API Routes Documentation

## Applicant Management

### Get Applicant Information

-   `GET /api/applicants` - Get and filter applicants with pagination
    -   Example Routes:
        -   `GET /api/applicants?per_page=8` - Paginate applicants
        -   `GET /api/applicants?name=John&email=john@example.com` - Filter by name and email
        -   `GET /api/applicants?first_choice=Computer Science&academic_year=2024` - Filter by program and year
        -   `GET /api/applicants?status=pending&sort_by=newest&per_page=20` - Get pending applications, sorted by newest, 20 per page
        -   `GET /api/applicants?sort_by=name_asc` - Get all applicants sorted by name ascending
    -   Query Parameters:
        -   `name` (optional): Filter by applicant name
        -   `email` (optional): Filter by applicant email
        -   `first_choice` (optional): Filter by first choice program
        -   `second_choice` (optional): Filter by second choice program
        -   `academic_year` (optional): Filter by academic year
        -   `status` (optional): Filter by application status
        -   `sort_by` (optional): Sort results by:
            -   `newest` (default): Sort by timestamp descending
            -   `oldest`: Sort by timestamp ascending
            -   `name_asc`: Sort by last name ascending
            -   `name_desc`: Sort by last name descending
        -   `per_page` (optional): Number of results per page (default: 10, max: 100)
-   `GET /api/applicants/{id}` - Get specific applicant by ID
    -   Example: `GET /api/applicants/123` - Get applicant with ID 123

### Applicant Status Updates

-   `PUT /api/applicants/{id}/status/missing` - Update applicant status to Missing
    -   Example: `PUT /api/applicants/123/status/missing`
-   `PUT /api/applicants/{id}/status/submitted` - Update applicant status to Submitted
    -   Example: `PUT /api/applicants/123/status/submitted`
-   `PUT /api/applicants/{id}/status/pending` - Update applicant status to Pending
    -   Example: `PUT /api/applicants/123/status/pending`
-   `PUT /api/applicants/{id}/status/under-review` - Update applicant status to Under Review
    -   Example: `PUT /api/applicants/123/status/under-review`
-   `PUT /api/applicants/{id}/status/approved` - Update applicant status to Approved
    -   Example: `PUT /api/applicants/123/status/approved`
-   `PUT /api/applicants/{id}/status/rejected` - Update applicant status to Rejected
    -   Example: `PUT /api/applicants/123/status/rejected`

## Notifications

### Applicant Notifications

-   `GET /api/applicants/notifications/{applicantId}` - Get all notifications for a specific applicant
    -   Example: `GET /api/applicants/notifications/123`
-   `PUT /api/notifications/mark-all-read/{applicantId}` - Mark all notifications as read for an applicant
    -   Example: `PUT /api/notifications/mark-all-read/123`

### Admin Notifications

-   `GET /api/admin/notifications` - Get all notifications for admin
-   `PUT /api/notifications/mark-all-admin-read` - Mark all admin notifications as read

### General Notification Operations

-   `GET /api/notifications/{notificationId}` - Get a specific notification by ID
    -   Example: `GET /api/notifications/abc-123`
-   `DELETE /api/notifications/{notificationId}` - Delete a specific notification
    -   Example: `DELETE /api/notifications/abc-123`
-   `GET /api/notifications/send` - Send status notifications
-   `PUT /api/notifications/{notificationId}/read` - Mark a specific notification as read
    -   Example: `PUT /api/notifications/abc-123/read`

## Notes

-   All routes are prefixed with `/api`
-   Status update routes require an applicant ID in the URL
-   Notification routes may require either an applicant ID or notification ID depending on the operation
