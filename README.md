# Raintree API Client

A PHP client for interacting with the Raintree API to authenticate, query data and get a patient record.

## Project Structure

```
raintree_task_2/
├── config/
│   └── config.php          # Configuration constants and settings
├── index.php               # Demo script
├── .env.example            # Environment variables template
├── .gitignore              # Git ignore rules
└── README.md
```

## Setup

1. **Configure Environment Variables**

   ```bash
   cp .env.example .env
   ```

   Add to the .env files the necessary authentication requirements.

## Usage

### Running the script

```bash
php index.php
```

## Configuration Options

- `DEBUG_MODE`: Enable/disable debug output
