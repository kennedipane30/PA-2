# Requirements Document - Spekta Academy PBL System

## Introduction

Spekta Academy adalah aplikasi mobile berbasis Flutter yang menerapkan konsep Project-Based Learning (PBL) untuk mengintegrasikan pembelajaran dari lima mata kuliah: PKPL (Pengelolaan Kualitas dan Proyek Perangkat Lunak), ATLV (Analisis dan Testing Lanjut Verifikasi), KEPAL (Keamanan Perangkat Lunak), SISKA (Sistem Keamanan), dan PA2 (Proyek Akhir 2). Aplikasi ini menyediakan platform bimbingan belajar online dengan fitur autentikasi, manajemen kelas, notifikasi, dan profil pengguna.

## Glossary

- **System**: Aplikasi mobile Spekta Academy
- **User**: Siswa yang menggunakan aplikasi untuk mengakses layanan bimbingan belajar
- **Admin**: Administrator yang mengelola sistem, kelas, dan pengguna
- **Instructor**: Pengajar yang memberikan materi dan mengelola kelas
- **Authentication Service**: Layanan backend yang menangani registrasi, login, dan verifikasi OTP
- **OTP**: One-Time Password yang dikirim melalui WhatsApp untuk verifikasi
- **Session**: Periode waktu dimana user tetap terautentikasi dalam aplikasi
- **Password Policy**: Aturan keamanan password yang mengharuskan minimal 8 karakter, 1 huruf kapital, 1 angka, dan 1 simbol
- **API Endpoint**: URL backend yang menerima request HTTP dari aplikasi
- **Navigation Context**: Konteks Flutter yang digunakan untuk navigasi antar halaman
- **Form Validation**: Proses validasi input user sebelum data dikirim ke server
- **CIA Triad**: Confidentiality, Integrity, Availability - prinsip keamanan informasi
- **Encryption**: Proses mengenkripsi data sensitif menggunakan algoritma kriptografi
- **Hashing**: Proses mengubah password menjadi hash yang tidak dapat dikembalikan
- **Cloud Service**: Layanan cloud computing untuk deployment dan storage (AWS, Google Cloud, dll)
- **Database**: Sistem penyimpanan data terstruktur dengan minimal 5 tabel
- **Use Case**: Skenario interaksi antara aktor dan sistem (minimal 5 use case)
- **Sensitive Data**: Data yang memerlukan perlindungan khusus seperti password, data pribadi, dan transaksi
- **Access Token**: Token yang digunakan untuk autentikasi API setelah login berhasil
- **Role-Based Access Control**: Sistem kontrol akses berdasarkan peran pengguna

## Requirements

### Requirement 1: User Registration

**User Story:** As a new student, I want to register an account with my personal information, so that I can access the Spekta Academy learning platform.

#### Acceptance Criteria

1. WHEN a user submits registration form with valid data THEN the System SHALL send the data to the Authentication Service registration endpoint
2. WHEN the Authentication Service returns status code 201 THEN the System SHALL display a success message and navigate to the login page
3. WHEN the Authentication Service returns an error status THEN the System SHALL display an error message to the user
4. WHEN a user enters data in any required field THEN the System SHALL validate that the field is not empty before submission
5. WHERE the user provides a password THEN the System SHALL validate the password against the Password Policy before submission

### Requirement 2: Password Security Validation

**User Story:** As a security-conscious system, I want to enforce strong password requirements, so that user accounts are protected from unauthorized access.

#### Acceptance Criteria

1. WHEN a user enters a password with fewer than 8 characters THEN the System SHALL display an error message "Minimal 8 karakter"
2. WHEN a user enters a password without uppercase letters THEN the System SHALL display an error message "Wajib ada 1 Huruf KAPITAL"
3. WHEN a user enters a password without digits THEN the System SHALL display an error message "Wajib ada 1 ANGKA"
4. WHEN a user enters a password without special characters THEN the System SHALL display an error message "Wajib ada 1 SIMBOL (!@# dll)"
5. WHEN a user enters a confirmation password that does not match the original password THEN the System SHALL display an error message "Password tidak cocok"

### Requirement 3: User Authentication

**User Story:** As a registered student, I want to login with my email and password, so that I can access my personalized learning dashboard.

#### Acceptance Criteria

1. WHEN a user submits valid credentials THEN the System SHALL send a POST request to the Authentication Service login endpoint
2. WHEN the Authentication Service returns status code 200 THEN the System SHALL navigate the user to the OTP verification page
3. WHEN the System navigates after an async operation THEN the System SHALL verify the Navigation Context is still mounted before navigation
4. WHEN the Authentication Service returns an error status THEN the System SHALL handle the error gracefully without crashing
5. THE System SHALL accept email and password as text input fields with appropriate masking for password

### Requirement 4: OTP Verification

**User Story:** As a logged-in user, I want to verify my identity using an OTP sent to my WhatsApp, so that my account access is secure.

#### Acceptance Criteria

1. WHEN a user enters an OTP code THEN the System SHALL send the OTP and email to the Authentication Service verify-otp endpoint
2. WHEN the Authentication Service returns status code 200 THEN the System SHALL navigate to the main application screen
3. WHEN navigating to the main screen THEN the System SHALL remove all previous navigation history from the stack
4. WHEN the System navigates after OTP verification THEN the System SHALL verify the Navigation Context is still mounted
5. THE System SHALL display the user's email address in the OTP verification instructions

### Requirement 5: Main Navigation Interface

**User Story:** As an authenticated user, I want to navigate between different sections of the app, so that I can access home, classes, notifications, and account features.

#### Acceptance Criteria

1. THE System SHALL provide a bottom navigation bar with four sections: Home, Kelas, Notifikasi, and Akun
2. WHEN a user taps a navigation item THEN the System SHALL display the corresponding page content
3. WHEN a navigation item is selected THEN the System SHALL highlight it with the Spekta red color (0xFF990000)
4. WHEN a navigation item is not selected THEN the System SHALL display it in grey color
5. THE System SHALL maintain the selected navigation state across user interactions

### Requirement 6: Home Dashboard Display

**User Story:** As a user, I want to see promotional content and academy information on the home page, so that I stay informed about current offers.

#### Acceptance Criteria

1. THE System SHALL display a curved red header with "Spekta Academy" branding and tagline
2. THE System SHALL show a "Promo Hari Ini" section below the header
3. WHEN displaying promotional content THEN the System SHALL use a container with Spekta red border and semi-transparent background
4. THE System SHALL make the home page scrollable to accommodate varying content lengths
5. THE System SHALL use SafeArea widget to prevent content from overlapping with system UI elements

### Requirement 7: Class Management Display

**User Story:** As a student, I want to view my enrolled classes, so that I can access my learning materials and track my courses.

#### Acceptance Criteria

1. THE System SHALL display a list of enrolled classes with class name, instructor name, and navigation icon
2. WHEN displaying class items THEN the System SHALL use Card widgets with ListTile for consistent styling
3. THE System SHALL show a menu book icon in Spekta red color for each class item
4. THE System SHALL provide an app bar with title "Kelas Saya"
5. THE System SHALL make the class list scrollable when content exceeds screen height

### Requirement 8: Notification Center

**User Story:** As a user, I want to view my notifications, so that I stay updated about important announcements and activities.

#### Acceptance Criteria

1. WHEN no notifications exist THEN the System SHALL display an empty state with a notification icon and message
2. THE System SHALL center the empty state content vertically and horizontally
3. THE System SHALL use grey color for empty state icons and text
4. THE System SHALL provide an app bar with title "Notifikasi"
5. THE System SHALL prepare the UI structure to accommodate future notification items

### Requirement 9: User Account Profile

**User Story:** As a user, I want to view and manage my account information, so that I can update my profile and logout when needed.

#### Acceptance Criteria

1. THE System SHALL display user profile information including avatar, name, and email
2. THE System SHALL provide a red header section containing the user profile summary
3. THE System SHALL offer menu options for "Edit Profil" and "Ganti Password"
4. THE System SHALL provide a logout button at the bottom of the screen
5. WHEN displaying menu items THEN the System SHALL use Spekta red color for icons

### Requirement 10: API Communication

**User Story:** As the system, I want to communicate with the backend API reliably, so that user data is processed correctly.

#### Acceptance Criteria

1. THE System SHALL use HTTP POST requests for all authentication operations
2. THE System SHALL set Accept header to "application/json" for all API requests
3. THE System SHALL use base URL "http://10.0.2.2:8000/api" for Android emulator connections
4. WHEN sending registration data THEN the System SHALL include all required fields in the request body
5. THE System SHALL handle HTTP response status codes appropriately for success and error cases

### Requirement 11: Async Context Safety

**User Story:** As a developer following Flutter best practices, I want to ensure BuildContext is used safely across async operations, so that the app does not crash or behave unexpectedly.

#### Acceptance Criteria

1. WHEN using BuildContext after an await operation THEN the System SHALL check context.mounted before usage
2. WHEN using BuildContext in StatefulWidget after async operation THEN the System SHALL check mounted property
3. WHEN the widget is no longer mounted THEN the System SHALL abort the operation without attempting navigation or showing dialogs
4. THE System SHALL pass Flutter analyze without any use_build_context_synchronously warnings
5. THE System SHALL prevent memory leaks and crashes from disposed widget contexts

### Requirement 12: UI Consistency and Branding

**User Story:** As Spekta Academy, I want consistent branding throughout the app, so that users have a cohesive visual experience.

#### Acceptance Criteria

1. THE System SHALL use color code 0xFF990000 (Spekta red) as the primary brand color throughout the app
2. WHEN displaying elevated buttons THEN the System SHALL use Spekta red background with white text
3. THE System SHALL use Material Design principles for UI components
4. WHEN using deprecated Flutter APIs THEN the System SHALL migrate to current recommended alternatives
5. THE System SHALL maintain consistent spacing, typography, and color schemes across all screens

### Requirement 13: Password Hashing and Encryption

**User Story:** As a security engineer, I want all passwords to be hashed and sensitive data to be encrypted, so that user data remains confidential even if the database is compromised.

#### Acceptance Criteria

1. WHEN a user registers or changes password THEN the System SHALL hash the password using bcrypt or Argon2 algorithm before storage
2. THE System SHALL NOT store plain text passwords in the Database
3. WHEN storing sensitive user data THEN the System SHALL encrypt the data using AES-256 encryption
4. WHEN transmitting sensitive data THEN the System SHALL use HTTPS protocol with TLS 1.2 or higher
5. THE System SHALL store encryption keys securely separate from encrypted data

### Requirement 14: Data Integrity Protection

**User Story:** As a system administrator, I want to ensure data integrity, so that user data cannot be tampered with without detection.

#### Acceptance Criteria

1. WHEN data is modified THEN the System SHALL record timestamp and user identifier for audit trail
2. THE System SHALL validate data integrity using checksums or digital signatures for critical transactions
3. WHEN detecting data tampering THEN the System SHALL log the incident and alert administrators
4. THE System SHALL implement database constraints to prevent invalid data entry
5. THE System SHALL use database transactions to ensure atomic operations for related data changes

### Requirement 15: System Availability and Reliability

**User Story:** As a student, I want the system to be available whenever I need to access my learning materials, so that my studies are not interrupted.

#### Acceptance Criteria

1. THE System SHALL be deployed on Cloud Service infrastructure with 99.9% uptime SLA
2. WHEN the backend service fails THEN the System SHALL display appropriate error messages to users
3. THE System SHALL implement retry logic for failed API requests with exponential backoff
4. THE System SHALL cache critical data locally to enable offline viewing capabilities
5. THE System SHALL implement health check endpoints for monitoring system availability

### Requirement 16: Multi-Actor Role Management

**User Story:** As an administrator, I want to manage different user roles, so that each actor has appropriate access permissions.

#### Acceptance Criteria

1. THE System SHALL support at least three actor types: Student, Instructor, and Admin
2. WHEN a user logs in THEN the System SHALL retrieve and store the user's role from the Authentication Service
3. WHEN displaying UI elements THEN the System SHALL show or hide features based on user role
4. THE System SHALL implement Role-Based Access Control for all API endpoints
5. WHEN an unauthorized user attempts restricted actions THEN the System SHALL deny access and log the attempt

### Requirement 17: Instructor Class Management

**User Story:** As an instructor, I want to create and manage classes, so that I can organize my teaching materials and student enrollments.

#### Acceptance Criteria

1. WHEN an instructor creates a class THEN the System SHALL store class information including name, description, schedule, and capacity
2. THE System SHALL allow instructors to upload learning materials to their classes
3. WHEN an instructor updates class information THEN the System SHALL notify enrolled students
4. THE System SHALL provide instructors with a dashboard showing their classes and student statistics
5. THE System SHALL allow instructors to view enrolled student lists for each class

### Requirement 18: Student Enrollment Management

**User Story:** As a student, I want to enroll in classes and track my progress, so that I can manage my learning journey.

#### Acceptance Criteria

1. WHEN a student browses available classes THEN the System SHALL display class details including instructor, schedule, and available slots
2. WHEN a student enrolls in a class THEN the System SHALL update enrollment records and decrease available slots
3. THE System SHALL prevent students from enrolling in classes that are full or have schedule conflicts
4. WHEN a student views their enrolled classes THEN the System SHALL display progress tracking information
5. THE System SHALL allow students to unenroll from classes before the deadline

### Requirement 19: Payment and Transaction Management

**User Story:** As a student, I want to make secure payments for classes, so that I can complete my enrollment.

#### Acceptance Criteria

1. WHEN a student initiates payment THEN the System SHALL encrypt all transaction data before transmission
2. THE System SHALL integrate with secure payment gateway for processing transactions
3. WHEN a payment is completed THEN the System SHALL store encrypted transaction records in the Database
4. THE System SHALL generate payment receipts and send them to the user's email
5. WHEN displaying transaction history THEN the System SHALL mask sensitive payment information

### Requirement 20: Admin User Management

**User Story:** As an admin, I want to manage all users in the system, so that I can maintain platform integrity and handle user issues.

#### Acceptance Criteria

1. WHEN an admin views user list THEN the System SHALL display all users with their roles and status
2. THE System SHALL allow admins to activate, deactivate, or delete user accounts
3. WHEN an admin modifies user data THEN the System SHALL log the action for audit purposes
4. THE System SHALL allow admins to reset user passwords securely
5. THE System SHALL provide admins with user activity reports and statistics

### Requirement 21: Learning Material Management

**User Story:** As an instructor, I want to upload and organize learning materials, so that students can access course content easily.

#### Acceptance Criteria

1. WHEN an instructor uploads a file THEN the System SHALL store it in Cloud Service storage
2. THE System SHALL support multiple file formats including PDF, video, and documents
3. WHEN a file is uploaded THEN the System SHALL scan it for malware before making it available
4. THE System SHALL organize materials by class and topic for easy navigation
5. THE System SHALL track which students have accessed each learning material

### Requirement 22: Notification System

**User Story:** As a user, I want to receive notifications about important events, so that I stay informed about my classes and activities.

#### Acceptance Criteria

1. WHEN a relevant event occurs THEN the System SHALL create a notification record in the Database
2. THE System SHALL send push notifications to mobile devices for time-sensitive events
3. WHEN a user views notifications THEN the System SHALL mark them as read
4. THE System SHALL allow users to configure notification preferences
5. THE System SHALL send notifications for class updates, payment confirmations, and system announcements

### Requirement 23: Cloud Deployment and Scalability

**User Story:** As a system architect, I want the application deployed on cloud infrastructure, so that it can scale with user demand.

#### Acceptance Criteria

1. THE System SHALL be deployed on a Cloud Service provider (AWS, Google Cloud, or equivalent)
2. THE System SHALL use cloud-based Database service for data storage
3. THE System SHALL use cloud storage service for file uploads and media content
4. WHEN user load increases THEN the System SHALL automatically scale resources
5. THE System SHALL implement CDN for serving static assets and improving performance

### Requirement 24: Database Schema with Multiple Tables

**User Story:** As a database administrator, I want a well-structured database schema, so that data is organized efficiently and relationships are maintained.

#### Acceptance Criteria

1. THE System SHALL implement at least 5 database tables: Users, Classes, Enrollments, Transactions, and Notifications
2. THE System SHALL define foreign key relationships between related tables
3. THE System SHALL implement indexes on frequently queried columns for performance
4. THE System SHALL use appropriate data types and constraints for each column
5. THE System SHALL implement soft delete functionality to preserve data integrity

### Requirement 25: Comprehensive API Coverage

**User Story:** As a mobile developer, I want comprehensive API endpoints, so that the mobile app can perform all necessary operations.

#### Acceptance Criteria

1. THE System SHALL implement API endpoints covering more than 90% of use cases
2. THE System SHALL provide RESTful API endpoints for all CRUD operations on major entities
3. WHEN an API request is received THEN the System SHALL validate the Access Token before processing
4. THE System SHALL return appropriate HTTP status codes and error messages for all API responses
5. THE System SHALL document all API endpoints with request/response examples

### Requirement 26: Automated Testing Coverage

**User Story:** As a quality assurance engineer, I want comprehensive automated tests, so that code quality is maintained and bugs are caught early.

#### Acceptance Criteria

1. THE System SHALL implement unit tests for all service classes and utility functions
2. THE System SHALL implement widget tests for all UI components
3. THE System SHALL implement integration tests for critical user flows
4. THE System SHALL achieve minimum 80% code coverage for automated tests
5. THE System SHALL run all tests automatically in CI/CD pipeline before deployment

### Requirement 27: Functional Testing for All Features

**User Story:** As a QA tester, I want to perform functional testing on all features, so that the application works as expected from user perspective.

#### Acceptance Criteria

1. THE System SHALL pass functional tests for user registration and login flows
2. THE System SHALL pass functional tests for class enrollment and management
3. THE System SHALL pass functional tests for payment processing
4. THE System SHALL pass functional tests for notification delivery
5. THE System SHALL pass functional tests for role-based access control

### Requirement 28: Session Management and Token Security

**User Story:** As a security engineer, I want secure session management, so that user sessions cannot be hijacked or misused.

#### Acceptance Criteria

1. WHEN a user logs in successfully THEN the System SHALL generate a secure Access Token with expiration time
2. THE System SHALL store tokens securely using platform-specific secure storage
3. WHEN a token expires THEN the System SHALL prompt user to re-authenticate
4. THE System SHALL invalidate tokens on logout
5. THE System SHALL implement token refresh mechanism to maintain user sessions

### Requirement 29: Audit Logging and Monitoring

**User Story:** As a system administrator, I want comprehensive audit logs, so that I can track system usage and investigate security incidents.

#### Acceptance Criteria

1. WHEN a user performs a sensitive action THEN the System SHALL log the action with timestamp, user ID, and action details
2. THE System SHALL log all authentication attempts including failures
3. THE System SHALL log all data modifications with before and after values
4. THE System SHALL store logs securely in Cloud Service logging infrastructure
5. THE System SHALL provide admin interface for searching and analyzing logs

### Requirement 30: Data Backup and Recovery

**User Story:** As a system administrator, I want automated data backups, so that data can be recovered in case of system failure.

#### Acceptance Criteria

1. THE System SHALL perform automated daily backups of the Database
2. THE System SHALL store backups in geographically separate Cloud Service regions
3. THE System SHALL encrypt all backup data before storage
4. THE System SHALL test backup restoration procedures monthly
5. THE System SHALL retain backups for minimum 30 days
