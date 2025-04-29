 Sistema de Reservas para Coworking - Docker + Laravel
Backpack for Laravel
Docker


Sistema completo de gestión de reservas para espacios de coworking desarrollado con:

Backpack for Laravel (Panel de administración)

Laravel 10 (Backend)

Docker (Entorno de desarrollo)

MySQL (Base de datos)

Spatie Permissions (Gestión de roles)

🚀 Requisitos Previos
Docker Desktop instalado (Descargar Docker)

Git (Descargar Git)

Puerto 80 y 3306 disponibles

🛠 Instalación
1. Clonar el repositorio
bash
git clone https://github.com/PeterGabrielVE/coworking-reservation.git
cd coworking-reservation
2. Configurar variables de entorno
Copiar el archivo .env.example a .env:

bash
cp .env.example .env
3. Construir los contenedores Docker
bash
docker-compose up -d --build
4. Instalar dependencias
bash
docker-compose exec app composer install
5. Generar clave de aplicación
bash
docker-compose exec app php artisan key:generate
6. Ejecutar migraciones y seeders
bash
docker-compose exec app php artisan migrate --seed
7. Compilar assets (opcional para desarrollo)
bash
docker-compose exec app npm install
docker-compose exec app npm run dev
🔑 Acceso al Sistema
URL de desarrollo: http://localhost

Panel de administración: http://localhost/admin

Credenciales de prueba:
Administrador:

Email: admin@example.com

Contraseña: password
