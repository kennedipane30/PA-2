# Design Document - Spekta Academy PBL System

## Overview

Spekta Academy adalah aplikasi mobile learning management system yang dibangun menggunakan Flutter untuk frontend dan Laravel untuk backend API. Sistem ini menerapkan arsitektur client-server dengan fokus pada keamanan (CIA Triad), skalabilitas, dan user experience yang optimal.

### Technology Stack

**Frontend (Mobile):**
- Flutter 3.38.2+ dengan Dart
- HTTP package untuk API communication
- Shared Preferences untuk local storage
- Flutter Secure Storage untuk token management

**Backend:**
- Laravel 10+ (PHP)
- RESTful API architecture
- JWT untuk authentication
- Queue system untuk background jobs

**Database:**
- MySQL/PostgreSQL untuk relational data
- Redis untuk caching dan session management

**Cloud Infrastructure:**
- AWS/Google Cloud untuk deployment
- S3/Cloud Storage untuk file storage
- CloudFront/CDN untuk content delivery
- RDS untuk managed database
- CloudWatch/Stackdriver untuk monitoring

**Security:**
- bcrypt/Argon2 untuk password hashing
- AES-256 untuk data encryption
- TLS 1.3 untuk data in transit
- JWT dengan refresh token mechanism

**Testing:**
- Flutter Test untuk unit & widget tests
- Integration tests untuk end-to-end flows
- Mockito untuk mocking dependencies
- Postman/Newman untuk API testing

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Mobile Application                       │
│                        (Flutter)                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │  Login   │  │  Home    │  │  Classes │  │  Profile │   │
│  │  Screen  │  │  Screen  │  │  Screen  │  │  Screen  │   │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘  └────┬─────┘   │
│       │             │              │              │          │
│  ┌────┴─────────────┴──────────────┴──────────────┴─────┐  │
│  │              Services Layer                            │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐           │  │
│  │  │   Auth   │  │   API    │  │  Storage │           │  │
│  │  │ Service  │  │ Service  │  │ Service  │           │  │
│  │  └──────────┘  └──────────┘  └──────────┘           │  │
│  └────────────────────────┬───────────────────────────────┘  │
└───────────────────────────┼───────────────────────────────────┘
                            │ HTTPS/TLS
                            │
┌───────────────────────────┼───────────────────────────────────┐
│                           ▼                                    │
│                    API Gateway / Load Balancer                │
│                           │                                    │
│  ┌────────────────────────┴────────────────────────┐         │
│  │           Laravel Backend API                    │         │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐     │         │
│  │  │   Auth   │  │  Class   │  │ Payment  │     │         │
│  │  │Controller│  │Controller│  │Controller│     │         │
│  │  └────┬─────┘  └────┬─────┘  └────┬─────┘     │         │
│  │       │             │              │            │         │
│  │  ┌────┴─────────────┴──────────────┴─────┐    │         │
│  │  │         Business Logic Layer           │    │         │
│  │  │  ┌──────────┐  ┌──────────┐          │    │         │
│  │  │  │   Auth   │  │  Class   │          │    │         │
│  │  │  │ Service  │  │ Service  │          │    │         │
│  │  │  └──────────┘  └──────────┘          │    │         │
│  │  └────────────────────┬───────────────────┘    │         │
│  │                       │                         │         │
│  │  ┌────────────────────┴───────────────────┐   │         │
│  │  │         Data Access Layer              │   │         │
│  │  │  ┌──────────┐  ┌──────────┐          │   │         │
│  │  │  │   User   │  │  Class   │          │   │         │
│  │  │  │  Model   │  │  Model   │          │   │         │
│  │  │  └──────────┘  └──────────┘          │   │         │
│  │  └────────────────────┬───────────────────┘   │         │
│  └───────────────────────┼───────────────────────┘         │
│                          │                                   │
│  ┌───────────────────────┼───────────────────────┐         │
│  │                       ▼                        │         │
│  │              Database (MySQL/PostgreSQL)      │         │
│  │  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐        │         │
│  │  │Users │ │Class │ │Enroll│ │Trans │        │         │
│  │  └──────┘ └──────┘ └──────┘ └──────┘        │         │
│  └───────────────────────────────────────────────┘         │
│                                                              │
│  ┌──────────────────────────────────────────────┐          │
│  │         Cloud Storage (S3/GCS)               │          │
│  │  - Learning Materials                        │          │
│  │  - User Uploads                              │          │
│  │  - Backups                                   │          │
│  └──────────────────────────────────────────────┘          │
│                                                              │
│  ┌──────────────────────────────────────────────┐          │
│  │         Redis Cache                          │          │
│  │  - Session Data                              │          │
│  │  - API Response Cache                        │          │
│  └──────────────────────────────────────────────┘          │
└──────────────────────────────────────────────────────────────┘
```

### Mobile Application Architecture (Flutter)

**Layer Structure:**
1. **Presentation Layer** - UI Screens and Widgets
2. **Business Logic Layer** - State Management (Provider/Bloc)
3. **Service Layer** - API calls, Storage, Authentication
4. **Data Layer** - Models and DTOs

**Design Patterns:**
- Repository Pattern untuk data access
- Service Pattern untuk business logic
- Singleton Pattern untuk shared services
- Factory Pattern untuk object creation

## Components and Interfaces

### 1. Authentication Service

**Purpose:** Handle user authentication, registration, and session management

**Interface:**
```dart
class AuthService {
  // Registration
  Future<ApiResponse<User>> register({
    required String name,
    required String email,
    required String password,
    required String tanggalLahir,
    required String alamat,
    required String nomorWa,
    required String nomorWaOrtu,
  });
  
  // Login
  Future<ApiResponse<LoginResponse>> login({
    required String email,
    required String password,
  });
  
  // OTP Verification
  Future<ApiResponse<AuthToken>> verifyOtp({
    required String email,
    required String otp,
  });
  
  // Token Management
  Future<void> saveToken(String token);
  Future<String?> getToken();
  Future<void> clearToken();
  
  // Token Refresh
  Future<ApiResponse<AuthToken>> refreshToken();
  
  // Logout
  Future<void> logout();
  
  // Check Authentication Status
  Future<bool> isAuthenticated();
}
```

### 2. API Service

**Purpose:** Centralized HTTP client for all API communications

**Interface:**
```dart
class ApiService {
  // Base configuration
  static const String baseUrl = 'https://api.spektaacademy.com';
  
  // HTTP Methods
  Future<ApiResponse<T>> get<T>(
    String endpoint, {
    Map<String, dynamic>? queryParameters,
    bool requiresAuth = true,
  });
  
  Future<ApiResponse<T>> post<T>(
    String endpoint, {
    Map<String, dynamic>? body,
    bool requiresAuth = true,
  });
  
  Future<ApiResponse<T>> put<T>(
    String endpoint, {
    Map<String, dynamic>? body,
    bool requiresAuth = true,
  });
  
  Future<ApiResponse<T>> delete<T>(
    String endpoint, {
    bool requiresAuth = true,
  });
  
  // File Upload
  Future<ApiResponse<T>> uploadFile<T>(
    String endpoint,
    File file, {
    Map<String, String>? additionalFields,
  });
}
```

### 3. Storage Service

**Purpose:** Handle local data persistence and secure storage

**Interface:**
```dart
class StorageService {
  // Secure Storage (for tokens, sensitive data)
  Future<void> writeSecure(String key, String value);
  Future<String?> readSecure(String key);
  Future<void> deleteSecure(String key);
  
  // Regular Storage (for preferences, cache)
  Future<void> write(String key, dynamic value);
  Future<T?> read<T>(String key);
  Future<void> delete(String key);
  Future<void> clear();
  
  // Cache Management
  Future<void> cacheData(String key, dynamic data, Duration ttl);
  Future<T?> getCachedData<T>(String key);
  Future<bool> isCacheValid(String key);
}
```

### 4. Class Service

**Purpose:** Manage class-related operations

**Interface:**
```dart
class ClassService {
  // Get all classes
  Future<ApiResponse<List<Class>>> getAllClasses({
    int page = 1,
    int perPage = 10,
    String? search,
  });
  
  // Get enrolled classes
  Future<ApiResponse<List<Class>>> getEnrolledClasses();
  
  // Get class details
  Future<ApiResponse<ClassDetail>> getClassDetail(int classId);
  
  // Enroll in class
  Future<ApiResponse<Enrollment>> enrollClass(int classId);
  
  // Unenroll from class
  Future<ApiResponse<void>> unenrollClass(int enrollmentId);
  
  // Get class materials
  Future<ApiResponse<List<Material>>> getClassMaterials(int classId);
  
  // Instructor: Create class
  Future<ApiResponse<Class>> createClass(ClassCreateDto dto);
  
  // Instructor: Update class
  Future<ApiResponse<Class>> updateClass(int classId, ClassUpdateDto dto);
  
  // Instructor: Delete class
  Future<ApiResponse<void>> deleteClass(int classId);
}
```

### 5. Payment Service

**Purpose:** Handle payment processing and transaction management

**Interface:**
```dart
class PaymentService {
  // Create payment
  Future<ApiResponse<PaymentIntent>> createPayment({
    required int classId,
    required double amount,
    required String paymentMethod,
  });
  
  // Verify payment
  Future<ApiResponse<Transaction>> verifyPayment(String paymentId);
  
  // Get transaction history
  Future<ApiResponse<List<Transaction>>> getTransactionHistory({
    int page = 1,
    int perPage = 10,
  });
  
  // Get transaction detail
  Future<ApiResponse<TransactionDetail>> getTransactionDetail(int transactionId);
  
  // Request refund
  Future<ApiResponse<Refund>> requestRefund(int transactionId, String reason);
}
```

### 6. Notification Service

**Purpose:** Manage notifications and push notifications

**Interface:**
```dart
class NotificationService {
  // Get notifications
  Future<ApiResponse<List<Notification>>> getNotifications({
    int page = 1,
    int perPage = 20,
    bool unreadOnly = false,
  });
  
  // Mark as read
  Future<ApiResponse<void>> markAsRead(int notificationId);
  
  // Mark all as read
  Future<ApiResponse<void>> markAllAsRead();
  
  // Delete notification
  Future<ApiResponse<void>> deleteNotification(int notificationId);
  
  // Get unread count
  Future<ApiResponse<int>> getUnreadCount();
  
  // Update FCM token
  Future<ApiResponse<void>> updateFcmToken(String token);
  
  // Notification preferences
  Future<ApiResponse<NotificationPreferences>> getPreferences();
  Future<ApiResponse<void>> updatePreferences(NotificationPreferences prefs);
}
```

### 7. User Service

**Purpose:** Manage user profile and account operations

**Interface:**
```dart
class UserService {
  // Get current user profile
  Future<ApiResponse<User>> getProfile();
  
  // Update profile
  Future<ApiResponse<User>> updateProfile(UserUpdateDto dto);
  
  // Change password
  Future<ApiResponse<void>> changePassword({
    required String currentPassword,
    required String newPassword,
  });
  
  // Upload profile picture
  Future<ApiResponse<String>> uploadProfilePicture(File image);
  
  // Admin: Get all users
  Future<ApiResponse<List<User>>> getAllUsers({
    int page = 1,
    int perPage = 20,
    String? role,
    String? search,
  });
  
  // Admin: Update user role
  Future<ApiResponse<User>> updateUserRole(int userId, String role);
  
  // Admin: Deactivate user
  Future<ApiResponse<void>> deactivateUser(int userId);
}
```

### 8. Encryption Service

**Purpose:** Handle data encryption and decryption

**Interface:**
```dart
class EncryptionService {
  // Encrypt sensitive data
  String encrypt(String plainText);
  
  // Decrypt sensitive data
  String decrypt(String encryptedText);
  
  // Hash password (for client-side validation)
  String hashPassword(String password);
  
  // Generate secure random string
  String generateSecureRandom(int length);
  
  // Encrypt file
  Future<File> encryptFile(File file);
  
  // Decrypt file
  Future<File> decryptFile(File encryptedFile);
}
```

## Data Models

### User Model
```dart
class User {
  final int id;
  final String name;
  final String email;
  final String role; // 'student', 'instructor', 'admin'
  final String? profilePicture;
  final String tanggalLahir;
  final String alamat;
  final String nomorWa;
  final String? nomorWaOrtu;
  final bool isActive;
  final bool emailVerified;
  final DateTime createdAt;
  final DateTime updatedAt;
  
  User({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    this.profilePicture,
    required this.tanggalLahir,
    required this.alamat,
    required this.nomorWa,
    this.nomorWaOrtu,
    required this.isActive,
    required this.emailVerified,
    required this.createdAt,
    required this.updatedAt,
  });
  
  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      role: json['role'],
      profilePicture: json['profile_picture'],
      tanggalLahir: json['tanggal_lahir'],
      alamat: json['alamat'],
      nomorWa: json['nomor_wa'],
      nomorWaOrtu: json['nomor_wa_ortu'],
      isActive: json['is_active'],
      emailVerified: json['email_verified'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }
  
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'role': role,
      'profile_picture': profilePicture,
      'tanggal_lahir': tanggalLahir,
      'alamat': alamat,
      'nomor_wa': nomorWa,
      'nomor_wa_ortu': nomorWaOrtu,
      'is_active': isActive,
      'email_verified': emailVerified,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}
```

### Class Model
```dart
class Class {
  final int id;
  final String name;
  final String description;
  final int instructorId;
  final String instructorName;
  final String? thumbnail;
  final double price;
  final int capacity;
  final int enrolledCount;
  final String schedule;
  final String status; // 'active', 'inactive', 'full'
  final DateTime startDate;
  final DateTime? endDate;
  final DateTime createdAt;
  final DateTime updatedAt;
  
  Class({
    required this.id,
    required this.name,
    required this.description,
    required this.instructorId,
    required this.instructorName,
    this.thumbnail,
    required this.price,
    required this.capacity,
    required this.enrolledCount,
    required this.schedule,
    required this.status,
    required this.startDate,
    this.endDate,
    required this.createdAt,
    required this.updatedAt,
  });
  
  bool get isFull => enrolledCount >= capacity;
  bool get isActive => status == 'active';
  int get availableSlots => capacity - enrolledCount;
  
  factory Class.fromJson(Map<String, dynamic> json) {
    return Class(
      id: json['id'],
      name: json['name'],
      description: json['description'],
      instructorId: json['instructor_id'],
      instructorName: json['instructor_name'],
      thumbnail: json['thumbnail'],
      price: json['price'].toDouble(),
      capacity: json['capacity'],
      enrolledCount: json['enrolled_count'],
      schedule: json['schedule'],
      status: json['status'],
      startDate: DateTime.parse(json['start_date']),
      endDate: json['end_date'] != null ? DateTime.parse(json['end_date']) : null,
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }
}
```

### Enrollment Model
```dart
class Enrollment {
  final int id;
  final int userId;
  final int classId;
  final String className;
  final String status; // 'active', 'completed', 'cancelled'
  final double progress; // 0.0 to 1.0
  final DateTime enrolledAt;
  final DateTime? completedAt;
  final DateTime? cancelledAt;
  
  Enrollment({
    required this.id,
    required this.userId,
    required this.classId,
    required this.className,
    required this.status,
    required this.progress,
    required this.enrolledAt,
    this.completedAt,
    this.cancelledAt,
  });
  
  bool get isActive => status == 'active';
  bool get isCompleted => status == 'completed';
  int get progressPercentage => (progress * 100).round();
  
  factory Enrollment.fromJson(Map<String, dynamic> json) {
    return Enrollment(
      id: json['id'],
      userId: json['user_id'],
      classId: json['class_id'],
      className: json['class_name'],
      status: json['status'],
      progress: json['progress'].toDouble(),
      enrolledAt: DateTime.parse(json['enrolled_at']),
      completedAt: json['completed_at'] != null ? DateTime.parse(json['completed_at']) : null,
      cancelledAt: json['cancelled_at'] != null ? DateTime.parse(json['cancelled_at']) : null,
    );
  }
}
```

### Transaction Model
```dart
class Transaction {
  final int id;
  final int userId;
  final int classId;
  final String className;
  final double amount;
  final String paymentMethod;
  final String status; // 'pending', 'completed', 'failed', 'refunded'
  final String? paymentGatewayId;
  final String? receiptUrl;
  final Map<String, dynamic>? encryptedPaymentDetails;
  final DateTime createdAt;
  final DateTime? completedAt;
  
  Transaction({
    required this.id,
    required this.userId,
    required this.classId,
    required this.className,
    required this.amount,
    required this.paymentMethod,
    required this.status,
    this.paymentGatewayId,
    this.receiptUrl,
    this.encryptedPaymentDetails,
    required this.createdAt,
    this.completedAt,
  });
  
  bool get isPending => status == 'pending';
  bool get isCompleted => status == 'completed';
  bool get isFailed => status == 'failed';
  
  String get maskedPaymentMethod {
    // Mask sensitive payment information
    if (paymentMethod.length > 4) {
      return '****${paymentMethod.substring(paymentMethod.length - 4)}';
    }
    return paymentMethod;
  }
  
  factory Transaction.fromJson(Map<String, dynamic> json) {
    return Transaction(
      id: json['id'],
      userId: json['user_id'],
      classId: json['class_id'],
      className: json['class_name'],
      amount: json['amount'].toDouble(),
      paymentMethod: json['payment_method'],
      status: json['status'],
      paymentGatewayId: json['payment_gateway_id'],
      receiptUrl: json['receipt_url'],
      encryptedPaymentDetails: json['encrypted_payment_details'],
      createdAt: DateTime.parse(json['created_at']),
      completedAt: json['completed_at'] != null ? DateTime.parse(json['completed_at']) : null,
    );
  }
}
```

### Notification Model
```dart
class Notification {
  final int id;
  final int userId;
  final String title;
  final String body;
  final String type; // 'class_update', 'payment', 'system', 'announcement'
  final Map<String, dynamic>? data;
  final bool isRead;
  final DateTime createdAt;
  final DateTime? readAt;
  
  Notification({
    required this.id,
    required this.userId,
    required this.title,
    required this.body,
    required this.type,
    this.data,
    required this.isRead,
    required this.createdAt,
    this.readAt,
  });
  
  factory Notification.fromJson(Map<String, dynamic> json) {
    return Notification(
      id: json['id'],
      userId: json['user_id'],
      title: json['title'],
      body: json['body'],
      type: json['type'],
      data: json['data'],
      isRead: json['is_read'],
      createdAt: DateTime.parse(json['created_at']),
      readAt: json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
    );
  }
}
```

### Material Model
```dart
class Material {
  final int id;
  final int classId;
  final String title;
  final String description;
  final String type; // 'pdf', 'video', 'document', 'link'
  final String fileUrl;
  final int? fileSize;
  final int order;
  final bool isPublished;
  final DateTime createdAt;
  final DateTime updatedAt;
  
  Material({
    required this.id,
    required this.classId,
    required this.title,
    required this.description,
    required this.type,
    required this.fileUrl,
    this.fileSize,
    required this.order,
    required this.isPublished,
    required this.createdAt,
    required this.updatedAt,
  });
  
  String get fileSizeFormatted {
    if (fileSize == null) return 'Unknown';
    if (fileSize! < 1024) return '$fileSize B';
    if (fileSize! < 1024 * 1024) return '${(fileSize! / 1024).toStringAsFixed(1)} KB';
    return '${(fileSize! / (1024 * 1024)).toStringAsFixed(1)} MB';
  }
  
  factory Material.fromJson(Map<String, dynamic> json) {
    return Material(
      id: json['id'],
      classId: json['class_id'],
      title: json['title'],
      description: json['description'],
      type: json['type'],
      fileUrl: json['file_url'],
      fileSize: json['file_size'],
      order: json['order'],
      isPublished: json['is_published'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }
}
```

### API Response Wrapper
```dart
class ApiResponse<T> {
  final bool success;
  final T? data;
  final String? message;
  final int? statusCode;
  final Map<String, dynamic>? errors;
  
  ApiResponse({
    required this.success,
    this.data,
    this.message,
    this.statusCode,
    this.errors,
  });
  
  factory ApiResponse.success(T data, {String? message, int? statusCode}) {
    return ApiResponse(
      success: true,
      data: data,
      message: message,
      statusCode: statusCode ?? 200,
    );
  }
  
  factory ApiResponse.error(String message, {int? statusCode, Map<String, dynamic>? errors}) {
    return ApiResponse(
      success: false,
      message: message,
      statusCode: statusCode ?? 500,
      errors: errors,
    );
  }
  
  factory ApiResponse.fromJson(Map<String, dynamic> json, T Function(dynamic) fromJsonT) {
    return ApiResponse(
      success: json['success'] ?? false,
      data: json['data'] != null ? fromJsonT(json['data']) : null,
      message: json['message'],
      statusCode: json['status_code'],
      errors: json['errors'],
    );
  }
}
```

### Database Schema

**Tables (Minimum 5 required):**

1. **users**
   - id (PK)
   - name
   - email (unique)
   - password_hash
   - role (enum: student, instructor, admin)
   - profile_picture
   - tanggal_lahir
   - alamat (encrypted)
   - nomor_wa (encrypted)
   - nomor_wa_ortu (encrypted)
   - is_active
   - email_verified
   - email_verified_at
   - remember_token
   - created_at
   - updated_at
   - deleted_at (soft delete)

2. **classes**
   - id (PK)
   - name
   - description
   - instructor_id (FK -> users.id)
   - thumbnail
   - price
   - capacity
   - enrolled_count
   - schedule
   - status (enum: active, inactive, full)
   - start_date
   - end_date
   - created_at
   - updated_at
   - deleted_at

3. **enrollments**
   - id (PK)
   - user_id (FK -> users.id)
   - class_id (FK -> classes.id)
   - status (enum: active, completed, cancelled)
   - progress (decimal 0-1)
   - enrolled_at
   - completed_at
   - cancelled_at
   - created_at
   - updated_at

4. **transactions**
   - id (PK)
   - user_id (FK -> users.id)
   - class_id (FK -> classes.id)
   - amount
   - payment_method
   - status (enum: pending, completed, failed, refunded)
   - payment_gateway_id
   - receipt_url
   - encrypted_payment_details (encrypted JSON)
   - created_at
   - completed_at
   - updated_at

5. **notifications**
   - id (PK)
   - user_id (FK -> users.id)
   - title
   - body
   - type (enum: class_update, payment, system, announcement)
   - data (JSON)
   - is_read
   - read_at
   - created_at
   - updated_at

6. **materials**
   - id (PK)
   - class_id (FK -> classes.id)
   - title
   - description
   - type (enum: pdf, video, document, link)
   - file_url
   - file_size
   - order
   - is_published
   - created_at
   - updated_at
   - deleted_at

7. **otp_verifications**
   - id (PK)
   - email
   - otp_code (hashed)
   - expires_at
   - verified_at
   - created_at

8. **audit_logs**
   - id (PK)
   - user_id (FK -> users.id)
   - action
   - entity_type
   - entity_id
   - old_values (JSON)
   - new_values (JSON)
   - ip_address
   - user_agent
   - created_at

9. **sessions**
   - id (PK)
   - user_id (FK -> users.id)
   - token_hash
   - device_info
   - ip_address
   - last_activity
   - expires_at
   - created_at

10. **refunds**
    - id (PK)
    - transaction_id (FK -> transactions.id)
    - reason
    - status (enum: pending, approved, rejected, completed)
    - refund_amount
    - processed_by (FK -> users.id)
    - processed_at
    - created_at
    - updated_at
