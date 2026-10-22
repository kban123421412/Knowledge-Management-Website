# 🌐 Knowledge Management Platform & Real-Time Chat

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com/)
[![WebSockets](https://img.shields.io/badge/WebSockets-Ratchet-FF6C37?style=for-the-badge)](https://socketo.me/)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)

> An enterprise web application for team collaboration, task tracking, real-time messaging, and interactive team analytics.

---

## 🌟 Key Features

* **💬 Real-Time Messaging Subsystem:** Built with PHP Ratchet WebSockets for instantaneous direct and group chat, message history management, file uploads, and soft/permanent conversation deletion.
* **📊 Analytics & Performance Dashboard:** Dedicated views for employees and managers featuring team aggregate metrics, member summaries, and interactive charts.
* **📋 Task & Team Management:** Interactive task assignment, completion tracking, and team user directory.
* **🔐 Authentication & Role Control:** Session-based authentication with role-restricted views for employees and managerial staff[cite: 4].
* **🐳 Containerized Architecture:** Dockerized setup bundling Apache/PHP, WebSocket servers, and MySQL database instances[cite: 4].

---

## 📂 Repository Architecture

```text
├── analytics/                 # Analytics UI views & data renderers
├── api/                       # REST API Endpoints
│   ├── analytics/             # Member, team, and weekly metric endpoints
│   ├── auth/                  # Login and session termination scripts
│   └── tasks.php / teams.php  # Core business logic handlers
├── database/                  # SQL Schemas and seed data
│   ├── schema.sql             # Base database table structure
│   ├── seed.sql               # Initial database setup script
│   └── demo-seed.sql          # Sample demo dataset
├── js/                        # Client-side JS scripts and Chart integrations
├── subsystem/                 # Real-time WebSocket Messaging Application
│   ├── Websocket/             # Ratchet WS server logic (Chat.php, server.php)
│   └── textchatComponents/    # Chat UI components (sidebar, window, modals)
├── Dockerfile                 # Container setup specification
├── docker-compose.yml         # Multi-container orchestration config
└── dbConfig.php               # Database connection configuration
```[cite: 4]

---

## 🛠️ Tech Stack & Requirements

| Layer | Component |
| :--- | :--- |
| **Backend** | PHP 8.x, Ratchet WebSockets (ReactPHP)[cite: 4] |
| **Frontend** | HTML5, CSS3, JavaScript (ES6+), Chart.js[cite: 4] |
| **Database** | MySQL 8.0[cite: 4] |
| **Containerization** | Docker & Docker Compose[cite: 4] |

---

## 🚀 Quick Start (Docker Setup)

The easiest way to run the platform locally is using Docker Compose[cite: 4]:

1. **Clone the repository:**
   ```bash
   git clone [https://github.com/kban123421412/Knowledge-Management-Website.git](https://github.com/kban123421412/Knowledge-Management-Website.git)
   cd Knowledge-Management-Website/part-3-team-27-main
