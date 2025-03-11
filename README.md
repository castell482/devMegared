# Banck transactions

Software for participation registration

## Overview

This application provides a secure and efficient way to manage user records with the following features:

- User authentication and authorization
- Complete MVC for user management
- **Fully responsive design** using native CSS
- **Real-time updates**
- Secure password handling

---

## Tech Stack

- **code** PHP
- **Database:** MySQL

## Prerequisites

Make sure you have the following installed on your system:

- PHP >= 8.1
- MySQL


## Installation

1. **Clone the Repository**:

```bash
git clone https://github.com/castell482/devMegared.git
cd devMegared
```

2. **Configure Environment Variables**:

```bash
cp .env.example .env
```

3. **Set Up Database Configuration**:
   Edit the `.env` file with your database credentials:

```env
ROOT_PASSWORD=root
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=megared
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
```

4. **Run Database Migrations**:

```bash
php .\database\migrate.php
```
```bash
 php .\database\seed.php```
```

### Responsive Design

- The application is fully responsive, ensuring a seamless user experience across devices, from desktops to smartphones.


### Authentication

- Secure login system
- Session management


### Crededentials
-email:admin@example.com
-password:Qwerty1234

- Secure login system
- Session management


# ER Diagram

```mermaid
erDiagram
    USER {
        int id PK "Llave primaria, identificador único del usuario."
        string name "Nombre completo del usuario."
        string email "Correo electrónico único, usado para autenticación."
        string password "Contraseña almacenada en formato hash por seguridad."
        datetime created_at "Fecha y hora de creación del usuario."
        datetime updated_at "Fecha y hora de la última actualización del usuario."
        datetime deleted_at "Fecha y hora de eliminación lógica del usuario (NULL si sigue activo)."
    }

    ACCOUNT {
        int id PK "Llave primaria, identificador único de la cuenta bancaria."
        int user_id FK "Llave foránea que relaciona la cuenta con un usuario."
        decimal balance "Saldo disponible en la cuenta, por defecto 0."
        datetime created_at "Fecha y hora de creación de la cuenta."
        datetime updated_at "Fecha y hora de la última actualización de la cuenta."
        datetime deleted_at "Fecha y hora de eliminación lógica de la cuenta (NULL si sigue activa)."
    }

    TRANSACTION {
        int id PK "Llave primaria, identificador único de la transacción."
        int account_id FK "Llave foránea, identifica la cuenta que inicia la transacción."
        int related_account_id FK "Llave foránea, identifica la cuenta destino."
        decimal amount "Monto de dinero transferido en la transacción."
        enum transaction_type "Tipo de transacción: DEPOSIT, WITHDRAWAL o TRANSFER."
        datetime created_at "Fecha y hora en que se realizó la transacción."
        datetime updated_at "Fecha y hora de la última actualización de la transacción."
        datetime deleted_at "Fecha y hora de eliminación lógica de la transacción (NULL si sigue activa)."
    }

    TRANSACTION_LOG {
        int id PK "Llave primaria, identificador único del registro de la transacción."
        int transaction_id FK "Llave foránea, referencia a la transacción asociada."
        int user_id FK "Llave foránea, referencia al usuario que realizó la transacción."
        decimal amount "Monto de la transacción registrada."
        string transaction_type "Tipo de transacción registrada."
        string ip_address "Dirección IP desde donde se realizó la transacción."
        text user_agent "Información del dispositivo/navegador del usuario."
        datetime created_at "Fecha y hora en que se registró el log."
        datetime updated_at "Fecha y hora de la última actualización del log."
    }

    USER ||--o{ ACCOUNT : owns
    ACCOUNT ||--o{ TRANSACTION : initiates 
    ACCOUNT ||--o{ TRANSACTION : receives
    TRANSACTION ||--o{ TRANSACTION_LOG : logs
    USER ||--o{ TRANSACTION_LOG : registers
```


# Author

* **Carlos Castellanos** - *Developer* - [Github](https://github.com/castell482)  - [linkedin](www.linkedin.com/in/carlos-mario-castellanos-81b3241a1)

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
