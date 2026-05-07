# Jostru Community System 🌍♻️

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white)
![Python Integration](https://img.shields.io/badge/Python_Colab_Ready-3776AB?style=for-the-badge&logo=python&logoColor=white)
![OAuth2](https://img.shields.io/badge/Google_SSO-4285F4?style=for-the-badge&logo=google&logoColor=white)

Jostru Community System is an integrated web and mobile-ready platform designed for environmental management, community engagement, and resource tracking. Built with a robust Laravel backend, it handles everything from community social feeds and digital membership generation to complex waste deposit tracking and AI-driven analytics integrations.

Developed by **Aman Techsolution**.

## ✨ Key Features

### 👥 Community & Membership Management
- **Role-Based Access Control (RBAC):** Tiered access for Superadmins, Admins, and Members.
- **Google SSO Integration:** Seamless 1-click authentication using Google OAuth2.
- **Dynamic Digital ID Cards:** Automated generation of downloadable Member Cards in PDF and PNG formats.
- **Social Feed & Real-time Chat:** Community timeline with likes, comments, media uploads, and private messaging systems.

### ♻️ Environmental & Waste Management
- **Waste Deposit Tracking:** Systematic recording of waste deposits with weight metrics, status tracking (Pending/Approved), and media proof uploads.
- **Production Batches Lifecycle:** Converts logged waste deposits into actionable production batches with SKU and pricing metrics.

### 📊 AI Analytics & Data Science Ready
- **Colab/Python Endpoints:** Dedicated API routes (`/api/export-waste-data` and `/api/save-ai-results`) to seamlessly pipe data to Google Colab or Python environments for machine learning models and data classification.
- **Developer Diagnostics API:** Secure, token-protected `/api/dev/*` endpoints for remote server pinging, log reading, and direct SQL querying without requiring SSH/FTP access.

### 💰 Financial & Operational Dashboard
- **Cashflow Management:** Granular tracking of income and operational expenses with automated ledger logging.
- **Activity Logging:** Comprehensive audit trails capturing every user action, IP address, and system modification for tight security and accountability.

## 🛠️ Tech Stack & Architecture

- **Backend:** Laravel (PHP 8.x)
- **Database:** MySQL/MariaDB 
- **Authentication:** Laravel Sanctum / Google Socialite
- **Notifications:** OneSignal Push Notifications integration
- **Media:** Advanced handling for high-resolution images and videos with structural DB logging.

## 🚀 Installation & Setup

1. Clone the repository:
   ```bash
   git clone [https://github.com/AmanSegavo/Jostru_community.git](https://github.com/AmanSegavo/Jostru_community.git)
