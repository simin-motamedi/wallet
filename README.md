<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="#"><img src="https://img.shields.io/badge/Wallet-Purchase-blue" alt="Wallet Purchase Service"></a>
  <a href="#"><img src="https://img.shields.io/badge/API-v1.0-green" alt="API Version"></a>
  <a href="#"><img src="https://img.shields.io/badge/Laravel-12-red" alt="Laravel 12"></a>
</p>

# Wallet Purchase Service

This Laravel service implements a **wallet-based invoice purchase flow** with OTP verification, validation, transactional safety, and SMS notifications.

It consists of **two APIs** for handling invoice payments.

---

## Overview

### 1. Prepare Invoice API
- Performs **pre-validation** before payment:
    - Invoice is **not already paid**
    - **Transaction** exists
    - **Wallet** exists
    - Invoice is **not expired**
    - Wallet has **sufficient balance**
    - **User and wallet are active**
- Middleware performs a **global check** to avoid exceeding daily limits.
- Sends an **OTP SMS** to the user's mobile.

### 2. Pay Invoice API
- Receives the **OTP** from the user.
- **Revalidates** invoice, transaction, and wallet to ensure integrity.
- Checks the OTP from **cache**.
- Performs the **purchase transaction** atomically:
    - Locks wallet row to prevent **race conditions**.
    - Updates **wallet balance**, **invoice state**, and **transaction state** together.
    - Updates **daily spent total** in cache with proper locking.
- Sends **success or failure SMS** depending on outcome.

---

## Features

- **Two-step flow** for safety:
    1. Validate and send OTP
    2. Verify OTP and complete purchase
- **Chain of Responsibility** for validations
- **Middleware** for global daily spending limit
- **Atomic transactions** with row-level locking
- **Custom exceptions** with unique error codes
- **SMS sending** via **Strategy pattern** to support multiple templates
- **Polymorphic transactions**:
    - `Transaction` model is polymorphic and can be linked to multiple types (Invoice, Subscription, etc.)
    - Uses a **TransactionProcessor interface** to handle logic per transaction type
- **Cache-based OTP** and **daily spending tracking**

---

## Entities

- **Invoice**
- **Transaction** (polymorphic)
- **Wallet**
- **User**

---

## Exception Handling

- Custom exceptions extend a **CodedException** base class.
- Each exception contains:
    - `errorType`
    - `errorCode` (from `ErrorCodes` enum)
    - HTTP status code
    - Human-readable message
- Handled globally using **Laravel 12 `app.php` configuration**.

---

## SMS Strategy

- Implements **Strategy Design Pattern**:
    - Different SMS templates (OTP, InvoicePaid, InvoiceFailed)
    - Different token costs per template
    - Flexible for future templates

---


---

## Transaction Handling

- **DB transactions** ensure atomicity.
- **Row-level locking** avoids race conditions.
- Updates wallet balance, invoice, and transaction **together**.
- Updates daily spent totals via **cache locks** for concurrency safety.

---

## Notes

- Middleware ensures that requests exceeding daily limits are blocked early.
- OTP verification guarantees that only the intended user can complete payment.
- Re-validation in the second API ensures integrity if state changes between steps.
- Polymorphic transactions allow extending the system for new transaction types without changing core logic.

---

## License

The Laravel framework and this wallet purchase service are licensed under the [MIT license](https://opensource.org/licenses/MIT).

