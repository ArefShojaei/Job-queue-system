<img width="1280" height="853" alt="image" src="https://github.com/user-attachments/assets/952b835a-0c61-44cb-acca-f4ce54cd46f5" />

# PHP Job Queue

A lightweight and simple Job Queue system built with pure PHP and MySQL.

This project allows you to dispatch, store, and process background jobs through a command-line worker. It is designed for developers who need a simple background task mechanism without using external queue services.

---

## Features

- Lightweight and simple architecture
- MySQL-based job storage engine
- Command-line queue worker
- Job dispatching system
- Event listener support
- Job lifecycle and status management
- Exception and failure handling
- Environment configuration using `.env`
- Eloquent Query Builder integration

---

## Requirements

- PHP 8.2 or higher
- MySQL 8.0 or higher
- Composer

---

## Installation

### Install via Composer (Recommended)

```bash
composer create-project arefshojaei/job-queue-system my-app
```

### Clone the Repository

```bash
git clone https://github.com/ArefShojaei/Job-queue-system.git
```

---

## Project Setup

### 1. Move into the project directory

```bash
cd my-app
```

### 2. Install dependencies

```bash
composer install

# Optional
npm install
```

### 3. Environment Configuration

Rename the `.env.example` file to `.env` and update the environment variables with your actual project configuration.

```txt
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_USERNAME=
DB_PASSWORD=
DB_REPOSITORY=
```

---

### 4. Database Setup

Create a new MySQL database and execute the following SQL query to create the required `jobs` table:

```sql
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payload LONGTEXT NOT NULL,
    status ENUM(
        'pending',
        'processing',
        'completed',
        'failed'
    ) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);
```

---

# Running the Queue Worker

The worker continuously watches the jobs table and processes available jobs.

```bash
php cli queue:work
```

---

## Available Commands

### List all jobs

```bash
php cli job:list
```

### Dispatch a test job

```bash
php cli job:test
```

### Remove all jobs

```bash
php cli job:clean
```

---

# Creating a Custom Job

Every job must implement the `Job` interface:

```php
interface Job
{
    public function handle(): void;
}
```

Example:

```php
class SendEmailJob implements Job
{
    public function handle(): void
    {
        // Your background logic goes here
    }
}
```

---

# Understanding How Jobs Work

This project includes example jobs inside the `app/Jobs` directory. These jobs exist only as examples to help you understand the workflow and architecture of the queue system.

You can use this queue system in two different ways:

### 1. Local Jobs (Same Project)

You can create your jobs directly inside the `app/Jobs` directory and let the queue worker process them.

This is the simplest approach and is recommended for learning and small applications.

---

### 2. External PHP Applications as Background Tasks

You may also use this project as a standalone background task processor for another PHP application.

In this architecture, your main application and this queue system run as independent applications, similar to separating services in a microservice architecture. However, this project itself is not a separate service or a message broker.

The external application must be written in PHP and must create jobs that follow the same contract:

```php
interface Job
{
    public function handle(): void;
}
```

The job object is then serialized and stored inside the queue database, where the worker application can retrieve and execute it.

Because both applications need to understand the same job classes, it is strongly recommended to share the same Composer dependencies, especially the job classes and their namespaces, between the producer application and this queue worker.

A common approach is to extract shared jobs and interfaces into a separate Composer package and install it in both projects. This guarantees compatibility and prevents serialization or class-loading issues.

---

## Important Notes

- This is not a replacement for distributed queue systems like RabbitMQ or Kafka.
- Jobs are PHP objects, so the producer and consumer must understand the same PHP classes.
- Both applications should maintain compatible Composer dependencies.
- The queue worker must always be running to process pending jobs.

---

Done.
